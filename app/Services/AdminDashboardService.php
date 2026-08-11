<?php

namespace App\Services;

use App\Models\Activities;
use App\Models\Hotels;
use App\Models\HotelPackage;
use App\Models\HotelPromo;
use App\Models\InvoiceAdmin;
use App\Models\OrderWedding;
use App\Models\Reservation;
use App\Models\Tours;
use App\Models\Transports;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AdminDashboardService
{
    public function build(array $filters = []): array
    {
        $period = $filters['period'] ?? 'month';
        [$start, $end, $periodLabel] = $this->periodRange($period);

        $serviceTotals = $this->serviceTotals();
        $orderStatus = $this->statusCounts('orders', ['Pending', 'Confirmed', 'Approved', 'Paid', 'Canceled', 'Rejected', 'Invalid'], $start, $end);
        $reservationStatus = $this->statusCounts('reservations', ['Pending', 'Active', 'Approved', 'Paid', 'Canceled', 'Cancelled'], $start, $end);
        $ordersInPeriod = $this->periodCount('orders', $start, $end);
        $reservationsInPeriod = $this->periodCount('reservations', $start, $end);

        return [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'periodOptions' => $this->periodOptions(),
            'kpis' => $this->kpis($serviceTotals, $ordersInPeriod, $reservationsInPeriod, $orderStatus, $reservationStatus),
            'services' => $this->serviceCards($serviceTotals),
            'orderStatus' => $orderStatus,
            'reservationStatus' => $reservationStatus,
            'recentActivities' => $this->recentActivities(),
            'upcomingServices' => $this->upcomingServices(),
            'attentionItems' => $this->attentionItems(),
        ];
    }

    private function periodOptions(): array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    private function periodRange(string $period): array
    {
        $now = Carbon::now(config('app.timezone'));

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'This Week'],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'This Year'],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'This Month'],
        };
    }

    private function serviceTotals(): array
    {
        return [
            'hotels' => $this->countByStatus(Hotels::query()),
            'hotelPackages' => $this->countByStatus(HotelPackage::query()),
            'hotelPromos' => $this->countByStatus(HotelPromo::query()),
            'transports' => $this->countByStatus(Transports::query()),
            'tours' => $this->countByStatus(Tours::query()),
            'activities' => $this->countByStatus(Activities::query()),
            'users' => $this->countByStatus(User::query(), 'status', 'Active'),
        ];
    }

    private function countByStatus($query, string $statusColumn = 'status', string $activeValue = 'Active'): array
    {
        $table = $query->getModel()->getTable();

        if (! Schema::hasTable($table)) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }

        $total = (clone $query)->count();

        if (! Schema::hasColumn($table, $statusColumn)) {
            return ['total' => $total, 'active' => $total, 'inactive' => 0];
        }

        $active = (clone $query)->where($statusColumn, $activeValue)->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => max($total - $active, 0),
        ];
    }

    private function statusCounts(string $table, array $statuses, Carbon $start, Carbon $end): array
    {
        if (! Schema::hasTable($table)) {
            return collect($statuses)->mapWithKeys(fn ($status) => [$status => 0])->all();
        }

        $counts = DB::table($table)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statuses)->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)])->all();
    }

    private function periodCount(string $table, Carbon $start, Carbon $end): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->whereBetween('created_at', [$start, $end])->count();
    }

    private function kpis(array $serviceTotals, int $totalBookings, int $totalReservations, array $orderStatus, array $reservationStatus): array
    {
        $totalServices = collect($serviceTotals)->except('users')->sum('total');
        $activeServices = collect($serviceTotals)->except('users')->sum('active');
        $pendingBookings = $orderStatus['Pending'] ?? 0;
        $unpaidReservations = max($totalReservations - (($reservationStatus['Paid'] ?? 0) + ($reservationStatus['Canceled'] ?? 0) + ($reservationStatus['Cancelled'] ?? 0)), 0);

        return [
            ['label' => 'Total Services', 'value' => $totalServices, 'meta' => $activeServices . ' active', 'icon' => 'dw dw-box', 'tone' => 'teal'],
            ['label' => 'Active Services', 'value' => $activeServices, 'meta' => ($totalServices - $activeServices) . ' inactive', 'icon' => 'dw dw-check', 'tone' => 'green'],
            ['label' => 'Total Bookings', 'value' => $totalBookings, 'meta' => 'Selected period', 'icon' => 'fa fa-tags', 'tone' => 'blue'],
            ['label' => 'Pending Bookings', 'value' => $pendingBookings, 'meta' => 'Need review', 'icon' => 'fa fa-clock-o', 'tone' => 'amber'],
            ['label' => 'Reservations', 'value' => $totalReservations, 'meta' => 'Selected period', 'icon' => 'dw dw-calendar-5', 'tone' => 'teal'],
            ['label' => 'Unpaid Reservations', 'value' => $unpaidReservations, 'meta' => 'Payment follow-up', 'icon' => 'fa fa-credit-card', 'tone' => 'amber'],
        ];
    }

    private function serviceCards(array $serviceTotals): array
    {
        return [
            ['label' => 'Hotels', 'data' => $serviceTotals['hotels'], 'icon' => 'dw dw-building1', 'route' => Route::has('view.hotels') ? route('view.hotels') : null],
            ['label' => 'Hotel Packages', 'data' => $serviceTotals['hotelPackages'], 'icon' => 'dw dw-hotel', 'route' => Route::has('view.hotels') ? route('view.hotels') : null],
            ['label' => 'Hotel Promotions', 'data' => $serviceTotals['hotelPromos'], 'icon' => 'fa fa-percent', 'route' => Route::has('index.flyers') ? route('index.flyers') : null],
            ['label' => 'Transports', 'data' => $serviceTotals['transports'], 'icon' => 'dw dw-bus', 'route' => Route::has('admin.transports.index') ? route('admin.transports.index') : null],
            ['label' => 'Tour Packages', 'data' => $serviceTotals['tours'], 'icon' => 'dw dw-map-6', 'route' => Route::has('admin.tour-packages.index') ? route('admin.tour-packages.index') : null],
            ['label' => 'Activities', 'data' => $serviceTotals['activities'], 'icon' => 'dw dw-pin-1', 'route' => Route::has('admin.activities.index') ? route('admin.activities.index') : null],
            ['label' => 'Users', 'data' => $serviceTotals['users'], 'icon' => 'dw dw-group', 'route' => Route::has('user-manager') ? route('user-manager') : null],
        ];
    }

    private function recentActivities(): array
    {
        if (! Schema::hasTable('orders') || ! Schema::hasTable('reservations')) {
            return [];
        }

        $orders = DB::table('orders')
            ->select('id', 'orderno as code', 'name', 'service', 'status', 'checkin as date', 'created_at')
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(fn ($order) => [
                'code' => $order->code ?: 'Order #' . $order->id,
                'name' => $order->name ?: 'Unknown customer',
                'type' => $order->service ?: 'Order',
                'status' => $order->status ?: 'Unknown',
                'date' => $this->formatDate($order->date ?: $order->created_at),
                'route' => Route::has('view.detail-order-admin') ? route('view.detail-order-admin', $order->id) : null,
            ]);

        $reservations = DB::table('reservations')
            ->select('id', 'rsv_no as code', 'customer_name as name', 'service', 'status', 'checkin as date', 'created_at')
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->latest('created_at')
            ->limit(4)
            ->get()
            ->map(fn ($reservation) => [
                'code' => $reservation->code ?: 'Reservation #' . $reservation->id,
                'name' => $reservation->name ?: 'Unknown guest',
                'type' => $reservation->service ?: 'Reservation',
                'status' => $reservation->status ?: 'Unknown',
                'date' => $this->formatDate($reservation->date ?: $reservation->created_at),
                'route' => Route::has('view.reservation.detail') ? route('view.reservation.detail', $reservation->id) : null,
            ]);

        return $orders
            ->merge($reservations)
            ->sortByDesc(fn ($item) => $item['date'])
            ->take(8)
            ->values()
            ->all();
    }

    private function upcomingServices(): array
    {
        if (! Schema::hasTable('orders')) {
            return [];
        }

        $start = Carbon::now(config('app.timezone'))->startOfDay()->toDateString();
        $end = Carbon::now(config('app.timezone'))->addDays(14)->endOfDay()->toDateString();

        return DB::table('orders')
            ->select('id', 'orderno', 'name', 'service', 'status', 'checkin')
            ->whereNotIn('status', ['Canceled', 'Cancelled', 'Rejected', 'Invalid', 'Deleted'])
            ->whereBetween('checkin', [$start, $end])
            ->orderBy('checkin')
            ->limit(8)
            ->get()
            ->map(fn ($order) => [
                'code' => $order->orderno ?: 'Order #' . $order->id,
                'name' => $order->name ?: 'Unknown customer',
                'type' => $order->service ?: 'Service',
                'status' => $order->status ?: 'Unknown',
                'date' => $this->formatDate($order->checkin),
                'route' => Route::has('view.detail-order-admin') ? route('view.detail-order-admin', $order->id) : null,
            ])
            ->all();
    }

    private function attentionItems(): array
    {
        return [
            ['label' => 'Inactive Services', 'value' => $this->inactiveServiceCount(), 'meta' => 'Products not visible to users', 'tone' => 'amber'],
            ['label' => 'Hotels Without Rooms', 'value' => $this->missingRelationCount('hotels', 'hotel_rooms', 'hotels_id'), 'meta' => 'Need room setup', 'tone' => 'red'],
            ['label' => 'Transports Without Price', 'value' => $this->missingRelationCount('transports', 'transport_prices', 'transports_id'), 'meta' => 'Need price setup', 'tone' => 'red'],
            ['label' => 'Tours Without Price', 'value' => $this->missingRelationCount('tours', 'tour_prices', 'tour_id'), 'meta' => 'Need rate setup', 'tone' => 'red'],
            ['label' => 'Orders Without Assignment', 'value' => $this->ordersWithoutAssignment(), 'meta' => 'Missing guide or driver', 'tone' => 'amber'],
        ];
    }

    private function inactiveServiceCount(): int
    {
        return collect([
            $this->inactiveCount('hotels'),
            $this->inactiveCount('transports'),
            $this->inactiveCount('tours'),
            $this->inactiveCount('activities'),
        ])->sum();
    }

    private function inactiveCount(string $table): int
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'status')) {
            return 0;
        }

        return DB::table($table)->where('status', '!=', 'Active')->count();
    }

    private function missingRelationCount(string $sourceTable, string $relationTable, string $foreignKey): int
    {
        if (! Schema::hasTable($sourceTable) || ! Schema::hasTable($relationTable)) {
            return 0;
        }

        return DB::table($sourceTable)
            ->leftJoin($relationTable, "{$relationTable}.{$foreignKey}", '=', "{$sourceTable}.id")
            ->whereNull("{$relationTable}.id")
            ->count("{$sourceTable}.id");
    }

    private function ordersWithoutAssignment(): int
    {
        if (! Schema::hasTable('orders')) {
            return 0;
        }

        return DB::table('orders')
            ->whereNotIn('status', ['Canceled', 'Cancelled', 'Rejected', 'Invalid', 'Deleted'])
            ->where(function ($query) {
                $query->whereNull('driver_id')->orWhereNull('guide_id');
            })
            ->count();
    }

    private function formatDate($date): string
    {
        if (blank($date)) {
            return '-';
        }

        return Carbon::parse($date)->format('d M Y');
    }
}
