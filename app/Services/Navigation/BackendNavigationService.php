<?php

namespace App\Services\Navigation;

use App\Models\Orders;
use App\Models\Promotion;
use App\Models\Services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BackendNavigationService
{
    private ?array $resolved = null;
    private ?Collection $resolvedServices = null;
    private ?Collection $resolvedNavigationItems = null;

    public function __construct(
        private readonly ServiceNavigationRegistry $serviceRegistry,
    ) {
    }

    public function data(Request $request): array
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        /** @var User|null $user */
        $user = $request->user();
        $now = Carbon::now();
        $orderCounts = $this->orderCounts($user, $now);
        $pendingCounts = $this->pendingCounts($now);

        $pendingCounts['all'] = $pendingCounts['tour'];
        $pendingCounts['operations'] = $pendingCounts['tour'];

        $ordersActive = $request->routeIs('admin.order.*', 'admin.orders.*')
            || $request->is('orders-admin', 'orders-admin/*');
        $reservationsActive = $request->routeIs('view.reservation*', 'admin.reservations.*', 'spks.show')
            || $request->is('reservation', 'reservation/*', 'order-rsv/*');
        $invoicesActive = $request->routeIs('admin.invoices.*')
            || $request->is('invoice', 'invoice/*');
        $transportMasterDataActive = $request->routeIs('admin.transport-types.*', 'admin.transport-brands.*');

        return $this->resolved = [
            'user' => $user,
            'canAccessAdminDashboard' => $user?->canAccessAdminDashboard() ?? false,
            'isApprovedUser' => $this->isApprovedUser($user),
            'orderCounts' => $orderCounts,
            'pendingCounts' => $pendingCounts,
            'services' => $this->navigationItems(),
            'promotions' => $this->activePromotions($now),
            'active' => [
                'orders' => $ordersActive,
                'reservations' => $reservationsActive,
                'invoices' => $invoicesActive,
                'operations' => $ordersActive || $reservationsActive || $invoicesActive || $transportMasterDataActive,
            ],
            'logos' => [
                'color' => config('app.logo_img_color'),
                'white' => config('app.logo_img_white'),
            ],
        ];
    }

    private function orderCounts(?User $user, Carbon $now): array
    {
        $defaults = array_fill_keys([
            'Active',
            'Rejected',
            'Invalid',
            'Waiting',
            'Draft',
            'Confirmed',
            'Approved',
        ], 0);

        if (! $user || ! Schema::hasTable('orders')) {
            return $defaults;
        }

        $counts = Orders::query()
            ->where('user_id', $user->getKey())
            ->where('checkin', '>=', $now)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        foreach ($defaults as $status => $count) {
            $defaults[$status] = (int) ($counts[$status] ?? 0);
        }

        return $defaults;
    }

    private function pendingCounts(Carbon $now): array
    {
        $tour = Schema::hasTable('orders')
            ? Orders::query()->where('status', 'Pending')->where('checkin', '>=', $now)->count()
            : 0;
        return [
            'tour' => $tour,
        ];
    }

    public function activeServices(): Collection
    {
        if ($this->resolvedServices !== null) {
            return $this->resolvedServices;
        }

        return $this->resolvedServices = Schema::hasTable('services')
            ? Services::query()->where('status', 'Active')->orderBy('name')->get()
            : collect();
    }

    public function navigationItems(): Collection
    {
        if ($this->resolvedNavigationItems !== null) {
            return $this->resolvedNavigationItems;
        }

        return $this->resolvedNavigationItems = $this->serviceRegistry->items(
            $this->activeServices()
        );
    }

    private function activePromotions(Carbon $now): Collection
    {
        return Schema::hasTable('promotions')
            ? Promotion::query()
                ->where('periode_start', '<', $now)
                ->where('periode_end', '>', $now)
                ->where('status', 'Active')
                ->get()
            : collect();
    }

    private function isApprovedUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return ! Schema::hasColumn('users', 'is_approved') || (bool) $user->is_approved;
    }

}
