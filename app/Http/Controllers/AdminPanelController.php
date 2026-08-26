<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\Villas;
use App\Models\UserLog;
use App\Models\Services;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\Activities;
use App\Models\AdminPanel;
use App\Models\HotelPrice;
use App\Models\Transports;
use App\Models\WebsiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\RegistrationAccessService;
use App\Services\Navigation\ServiceNavigationRegistry;
use App\Http\Requests\StoreAdminPanelRequest;
use App\Http\Requests\UpdateAdminPanelRequest;

class AdminPanelController extends Controller
{
    public function __construct(
        private readonly ServiceNavigationRegistry $serviceNavigationRegistry,
    )
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    
    // FUNCTION ADD SERVICE =============================================================================================================>
    public function admin_panel_main(Request $request){
        return view('backend.developer.index', $this->adminPanelData());
    }

    public function hotelPriceChart(Request $request)
    {
        $hotelId = $request->hotel_id;

        $prices = HotelPrice::where('hotels_id', $hotelId)
            ->select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('AVG(contract_rate) as avg_price')
            )
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->orderBy('month')
            ->get();

        $months = [];
        $values = [];

        foreach ($prices as $price) {
            $months[] = Carbon::create()->month((int) $price->month)->format('M');
            $values[] = round((float) $price->avg_price, 2);
        }

        return response()->json([
            'months' => $months,
            'values' => $values
        ]);
    }
    
    public function index()
    {
        return view('backend.developer.index', $this->adminPanelData());
    }

    protected function adminPanelData(): array
    {
        $serviceCounts = [
            'Hotels' => $this->activeDraftCounts(Hotels::query()),
            'Tours' => $this->activeDraftCounts(Tours::query()),
            'Activities' => $this->activeDraftCounts(Activities::query()),
            'Transports' => $this->activeDraftCounts(Transports::query()),
            'Villas' => $this->activeDraftCounts(Villas::query()),
            'Weddings' => [
                'active' => Weddings::query()->where('status', 'Active')->count(),
                'draft' => Weddings::query()->where('status', '!=', 'Active')->count(),
            ],
        ];

        $services = Services::query()
            ->orderByRaw("status = 'Active' desc")
            ->orderBy('name')
            ->get()
            ->map(function ($service) use ($serviceCounts) {
                $navigation = $this->serviceNavigationRegistry->item($service);
                $counts = $serviceCounts[$navigation['content_key']] ?? ['active' => 0, 'draft' => 0];

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'nicname' => $service->nicname,
                    'icon' => $navigation['icon'],
                    'status' => $service->status,
                    'canonical_slug' => $navigation['canonical_slug'],
                    'public_route' => $navigation['public_route'],
                    'admin_route' => $navigation['admin_route'],
                    'navigation_ready' => $navigation['navigation_ready'],
                    'active_count' => $counts['active'],
                    'draft_count' => $counts['draft'],
                    'total_count' => $counts['active'] + $counts['draft'],
                ];
            });

        $expectedCurrencies = collect(['USD', 'CNY', 'TWD']);
        $currencyRates = UsdRates::query()
            ->whereIn('name', $expectedCurrencies->all())
            ->get()
            ->keyBy('name');

        $inactiveServices = $services->where('status', '!=', 'Active')->count();
        $totalDraftContent = collect($serviceCounts)->sum('draft');
        $missingCurrencyRates = $expectedCurrencies
            ->reject(fn ($currency) => $currencyRates->has($currency))
            ->values();
        $servicesMissingMetadata = $services
            ->filter(fn ($service) => empty($service['nicname'])
                || empty($service['icon'])
                || ($service['status'] === 'Active' && ! $service['navigation_ready']))
            ->values();

        $developerHealthChecks = collect([
            [
                'label' => 'Service Registry',
                'status' => $servicesMissingMetadata->isEmpty()
                    ? __('service-registry.health.healthy')
                    : __('service-registry.health.needs_review'),
                'meta' => $servicesMissingMetadata->isEmpty()
                    ? __('service-registry.health.ready')
                    : __('service-registry.health.review', ['count' => $servicesMissingMetadata->count()]),
                'tone' => $servicesMissingMetadata->isEmpty() ? 'healthy' : 'warning',
            ],
            [
                'label' => 'Access Baseline',
                'status' => 'Role Based',
                'meta' => 'Developer pages rely on route middleware and policies for access control.',
                'tone' => 'info',
            ],
            [
                'label' => 'Currency Integration',
                'status' => $missingCurrencyRates->isEmpty() ? 'Ready' : 'Incomplete',
                'meta' => $missingCurrencyRates->isEmpty()
                    ? 'USD, CNY, and TWD exchange rates are configured.'
                    : 'Missing rate: ' . $missingCurrencyRates->implode(', '),
                'tone' => $missingCurrencyRates->isEmpty() ? 'healthy' : 'danger',
            ],
        ]);

        return [
            'adminpanel' => AdminPanel::query()->get(),
            'currencyRates' => $currencyRates,
            'expectedCurrencies' => $expectedCurrencies,
            'missingCurrencyRates' => $missingCurrencyRates,
            'services' => $services,
            'serviceCounts' => $serviceCounts,
            'developerHealthChecks' => $developerHealthChecks,
            'trafficAnalytics' => $this->trafficAnalytics(),
            'registrationAccess' => app(RegistrationAccessService::class)->setting(),
            'dashboardStats' => [
                [
                    'label' => 'Registered Services',
                    'value' => $services->where('status', 'Active')->count(),
                    'meta' => $inactiveServices . ' inactive from ' . $services->count() . ' total services',
                    'icon' => 'fa fa-cubes',
                    'tone' => 'teal',
                ],
                [
                    'label' => 'Draft Content',
                    'value' => $totalDraftContent,
                    'meta' => 'Inactive or draft records across service domains',
                    'icon' => 'fa fa-code-fork',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Currency Setup',
                    'value' => $currencyRates->count() . '/' . $expectedCurrencies->count(),
                    'meta' => $missingCurrencyRates->isEmpty() ? 'Required rates configured' : 'Missing ' . $missingCurrencyRates->implode(', '),
                    'icon' => 'fa fa-exchange',
                    'tone' => 'green',
                ],
            ],
        ];
    }

    public function updateRegistrationAccess(Request $request, RegistrationAccessService $registrationAccess)
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $setting = $registrationAccess->update($request->boolean('enabled'));

        return redirect()
            ->to('/admin/panel-main')
            ->with('success', $setting->status
                ? 'Registration access has been enabled.'
                : 'Registration access has been disabled.');
    }

    protected function activeDraftCounts($query): array
    {
        return [
            'active' => (clone $query)->where('status', 'Active')->count(),
            'draft' => (clone $query)->where('status', '!=', 'Active')->count(),
        ];
    }

    protected function trafficAnalytics(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $visits = WebsiteVisit::query();
        $last30Days = (clone $visits)->where('occurred_at', '>=', $start);
        $totalVisits = (clone $last30Days)->count();
        $periods = collect(['day', 'week', 'month', 'year'])
            ->mapWithKeys(fn ($period) => [$period => $this->trafficPeriodAnalytics($period)]);

        return [
            'summary' => [
                [
                    'label' => 'Total Visits',
                    'value' => $totalVisits,
                    'meta' => 'Last 30 days',
                ],
                [
                    'label' => 'Unique Visitors',
                    'value' => (clone $last30Days)->distinct('visitor_hash')->count('visitor_hash'),
                    'meta' => 'Based on anonymous visitor hash',
                ],
                [
                    'label' => 'Countries',
                    'value' => (clone $last30Days)->distinct('country_name')->count('country_name'),
                    'meta' => 'Detected from proxy/CDN country headers',
                ],
                [
                    'label' => 'Tracked Pages',
                    'value' => (clone $last30Days)->distinct('path')->count('path'),
                    'meta' => 'HTML pages with successful response',
                ],
            ],
            'series' => $periods->map(fn ($period) => $period['series'])->all(),
            'periods' => $periods->all(),
            'topCountries' => $this->topVisitRows('country_name', 'Unknown', $start),
            'topPages' => $this->topVisitRows('path', '/', $start),
            'devices' => $this->topVisitRows('device_type', 'unknown', $start),
        ];
    }

    protected function visitSeries(string $period): array
    {
        return $this->trafficPeriodAnalytics($period)['series'];
    }

    protected function trafficPeriodAnalytics(string $period): array
    {
        $config = $this->trafficPeriodConfig($period);
        $dates = collect();
        $start = $config['start'];
        $end = now();

        for ($index = 0; $index < $config['count']; $index++) {
            $date = $start->copy()->add($index, $config['unit']);
            $dates->put($date->format($config['format']), [
                'label' => $config['label']($date),
                'value' => 0,
            ]);
        }

        $this->visitBuckets($period, $start)
            ->each(function ($row) use ($dates) {
                if ($dates->has($row->bucket)) {
                    $point = $dates->get($row->bucket);
                    $point['value'] = (int) $row->total;
                    $dates->put($row->bucket, $point);
                }
            });

        $points = $dates->values();
        $totalVisits = (int) WebsiteVisit::query()
            ->whereBetween('occurred_at', [$start, $end])
            ->count();
        $uniqueVisitors = (int) WebsiteVisit::query()
            ->whereBetween('occurred_at', [$start, $end])
            ->distinct('visitor_hash')
            ->count('visitor_hash');
        $previousVisits = $this->previousVisitCount($start, $end);
        $trend = $this->percentageChange($totalVisits, $previousVisits);
        $peak = $points
            ->sortByDesc('value')
            ->first() ?: ['label' => '-', 'value' => 0];

        return [
            'key' => $period,
            'label' => $config['title'],
            'range' => $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d'),
            'summary' => [
                [
                    'label' => 'Visits',
                    'value' => $totalVisits,
                    'meta' => $trend['label'],
                    'tone' => $trend['tone'],
                ],
                [
                    'label' => 'Unique Visitors',
                    'value' => $uniqueVisitors,
                    'meta' => $totalVisits > 0 ? round(($uniqueVisitors / $totalVisits) * 100) . '% of visits' : 'No tracked visits',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Average',
                    'value' => round($totalVisits / max(1, $config['count']), 1),
                    'meta' => 'Visits per ' . $config['unit'],
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Peak',
                    'value' => (int) $peak['value'],
                    'meta' => $peak['label'],
                    'tone' => 'info',
                ],
            ],
            'series' => [
                'labels' => $points->pluck('label')->values()->all(),
                'values' => $points->pluck('value')->values()->all(),
                'max' => max(1, (int) $points->max('value')),
                'total' => $totalVisits,
            ],
            'breakdowns' => [
                'countries' => $this->topVisitRows('country_name', 'Unknown', $start, $end),
                'pages' => $this->topVisitRows('path', '/', $start, $end),
                'devices' => $this->topVisitRows('device_type', 'unknown', $start, $end),
                'referrers' => $this->topVisitRows('referrer_host', 'Direct / None', $start, $end),
                'areas' => $this->topVisitRows('area', 'unknown', $start, $end),
            ],
        ];
    }

    protected function trafficPeriodConfig(string $period): array
    {
        return match ($period) {
            'week' => [
                'title' => 'Weekly',
                'count' => 8,
                'unit' => 'week',
                'format' => 'o-W',
                'start' => now()->copy()->startOfWeek()->subWeeks(7),
                'label' => fn (Carbon $date) => 'W' . $date->isoWeek() . ' ' . $date->format('Y'),
            ],
            'month' => [
                'title' => 'Monthly',
                'count' => 12,
                'unit' => 'month',
                'format' => 'Y-m',
                'start' => now()->copy()->startOfMonth()->subMonths(11),
                'label' => fn (Carbon $date) => $date->format('M Y'),
            ],
            'year' => [
                'title' => 'Yearly',
                'count' => 5,
                'unit' => 'year',
                'format' => 'Y',
                'start' => now()->copy()->startOfYear()->subYears(4),
                'label' => fn (Carbon $date) => $date->format('Y'),
            ],
            default => [
                'title' => 'Daily',
                'count' => 30,
                'unit' => 'day',
                'format' => 'Y-m-d',
                'start' => now()->copy()->startOfDay()->subDays(29),
                'label' => fn (Carbon $date) => $date->format('M d'),
            ],
        };
    }

    protected function visitBuckets(string $period, Carbon $start): Collection
    {
        $bucket = $this->visitBucketExpression($period);

        return WebsiteVisit::query()
            ->where('occurred_at', '>=', $start)
            ->selectRaw("{$bucket} as bucket, COUNT(*) as total")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();
    }

    protected function visitBucketExpression(string $period): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return match ($period) {
                'week' => "strftime('%Y-%W', occurred_at)",
                'month' => "strftime('%Y-%m', occurred_at)",
                'year' => "strftime('%Y', occurred_at)",
                default => "strftime('%Y-%m-%d', occurred_at)",
            };
        }

        return match ($period) {
            'week' => "DATE_FORMAT(occurred_at, '%x-%v')",
            'month' => "DATE_FORMAT(occurred_at, '%Y-%m')",
            'year' => "DATE_FORMAT(occurred_at, '%Y')",
            default => "DATE_FORMAT(occurred_at, '%Y-%m-%d')",
        };
    }

    protected function previousVisitCount(Carbon $start, Carbon $end): int
    {
        $seconds = $start->diffInSeconds($end);
        $previousEnd = $start->copy()->subSecond();
        $previousStart = $start->copy()->subSeconds($seconds);

        return (int) WebsiteVisit::query()
            ->whereBetween('occurred_at', [$previousStart, $previousEnd])
            ->count();
    }

    protected function percentageChange(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'label' => $current > 0 ? 'New traffic in this period' : 'No previous traffic',
                'tone' => $current > 0 ? 'up' : 'neutral',
            ];
        }

        $change = round((($current - $previous) / $previous) * 100, 1);

        return [
            'label' => ($change >= 0 ? '+' : '') . $change . '% vs previous period',
            'tone' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral'),
        ];
    }

    protected function topVisitRows(string $column, string $fallback, Carbon $start, ?Carbon $end = null)
    {
        return WebsiteVisit::query()
            ->whereBetween('occurred_at', [$start, $end ?: now()])
            ->selectRaw("COALESCE({$column}, ?) as label, COUNT(*) as total", [$fallback])
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }

// FUNCTION ADD SERVICE =============================================================================================================>
    public function func_add_service(StoreAdminPanelRequest $request)
    {
        DB::transaction(function () use ($request) {
            $service = Services::query()->create([
                ...$request->safe()->only(['name', 'nicname', 'icon']),
                'status' => 'Draft',
            ]);

            $this->writeServiceLog($request, 'Add', $service, 'Add Service');
        });

        return redirect()->route('admin-panel')->with('success', __('service-registry.flash.created'));
    }

// FUNCTION EDIT SERVICE =============================================================================================================>
    public function func_edit_service(UpdateAdminPanelRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $service = Services::query()->findOrFail($id);
            $service->update($request->validated());

            $this->writeServiceLog($request, 'Edit Service', $service, "Update Service: {$id}");
        });

        return redirect()->route('admin-panel')->with('success', __('service-registry.flash.updated'));
    }

// FUNCTION DISABLE SERVICE =============================================================================================================>
    public function func_disable_service(Request $request,$id)
    {
        DB::transaction(function () use ($request, $id) {
            $service = Services::query()->findOrFail($id);
            $service->update(['status' => 'Draft']);

            $this->writeServiceLog($request, 'Update Service', $service, "Disable Service: {$id}");
        });

        return redirect()->route('admin-panel')->with('success', __('service-registry.flash.disabled'));
    }

// FUNCTION ENNABLE SERVICE =============================================================================================================>
    public function func_enable_service(Request $request,$id)
    {
        DB::transaction(function () use ($request, $id) {
            $service = Services::query()->findOrFail($id);
            $service->update(['status' => 'Active']);

            $this->writeServiceLog($request, 'Update Service', $service, "Enable Service: {$id}");
        });

        return redirect()->route('admin-panel')->with('success', __('service-registry.flash.activated'));
    }

// FUNCTION REMOVE SERVICE =============================================================================================================>
    public function func_remove_service(Request $request,$id)
    {
        DB::transaction(function () use ($request, $id) {
            $service = Services::query()->findOrFail($id);
            $this->writeServiceLog($request, 'Remove Service', $service, "Remove Service: {$id}");
            $service->delete();
        });

        return redirect()->route('admin-panel')->with('success', __('service-registry.flash.removed'));
    }

    private function writeServiceLog(Request $request, string $action, Services $service, string $note): void
    {
        UserLog::query()->create([
            'action' => $action,
            'service' => 'Service',
            'subservice' => $service->name,
            'subservice_id' => $service->getKey(),
            'page' => 'admin-panel',
            'user_id' => $request->user()->getKey(),
            'user_ip' => $request->ip(),
            'note' => $note,
        ]);
    }
    
}
