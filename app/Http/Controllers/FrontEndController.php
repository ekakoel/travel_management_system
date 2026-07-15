<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Activities;
use App\Models\Promotion;
use App\Models\Tours;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\HotelPackage;
use App\Models\TourPrices;
use Illuminate\Support\Facades\Log;
use App\Models\HotelPromo;
use App\Models\Transports;
use App\Models\UsdRates;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PublicFaqService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;

class FrontEndController extends Controller
{
    public function index(Request $request, PublicFaqService $publicFaqService)
    {
        try {
            $now = Carbon::now();
            $activeHotelsCount = Hotels::active()->count();
            $activeTransportsCount = Transports::where('status', 'Active')->count();
            $promosQuery = HotelPromo::with('hotels')
                ->active()
                ->validForBooking($now)
                ->whereNotNull('rooms_id')
                ->whereHas('rooms', function ($query) {
                    $query->where('status', 'Active');
                })
                ->whereHas('hotels', function ($query) {
                    $query->where('status', 'Active');
                })
                ->orderBy('book_periode_start', 'asc')
                ->get();

            // Filter a second time to ensure hotels relationship exists and is not null.
            $promos = $promosQuery->filter(function ($promo) {
                return $promo->hotels !== null;
            })->unique('hotels_id');

            $homeStats = [
                'active_hotels' => $activeHotelsCount,
                'active_transports' => $activeTransportsCount,
                'live_promotions' => $promos->count(),
                'support_label' => '24/7',
            ];

            $homeServiceImages = [
                'accommodations' => $this->resolveHomeServiceImagePath(
                    Hotels::query()
                        ->active()
                        ->whereNotNull('cover')
                        ->where('cover', '!=', '')
                        ->latest('updated_at')
                        ->value('cover'),
                    'hotels/hotels-cover/'
                ),
                'transportation' => $this->resolveHomeServiceImagePath(
                    Transports::query()
                        ->where('status', 'Active')
                        ->whereNotNull('cover')
                        ->where('cover', '!=', '')
                        ->latest('updated_at')
                        ->value('cover'),
                    'transports/transports-cover/'
                ),
                'tours' => $this->resolveHomeServiceImagePath(
                    Tours::query()
                        ->where('status', 'Active')
                        ->whereNotNull('cover')
                        ->where('cover', '!=', '')
                        ->latest('updated_at')
                        ->value('cover'),
                    'tours/tours-cover/'
                ),
                'activities' => $this->resolveHomeServiceImagePath(
                    Activities::query()
                        ->where('status', 'Active')
                        ->whereNotNull('cover')
                        ->where('cover', '!=', '')
                        ->latest('updated_at')
                        ->value('cover'),
                    'activities/activities-cover/'
                ),
            ];

            $homeFaqItems = $publicFaqService->items();

            return view('frontend.home.index', compact('promos', 'homeStats', 'homeServiceImages', 'homeFaqItems'));
        } catch (\Exception $e) {
            Log::error('Error on homepage: ' . $e->getMessage());
            abort(500, 'An error occurred while loading the homepage.');
        }
    }

    public function tour_package_services(Request $request)
    {
        $now = Carbon::now()->toDateString();
        $searchName = trim((string) $request->input('search_name', ''));
        $searchArea = trim((string) $request->input('search_area', ''));
        $searchType = trim((string) $request->input('search_type', ''));
        $areaColumn = Schema::hasColumn('tours', 'area')
            ? 'area'
            : (Schema::hasColumn('tours', 'region') ? 'region' : null);
        $hasTypeRelation = Schema::hasColumn('tours', 'type_id');
        $hasLegacyType = Schema::hasColumn('tours', 'type');
        $hasTypeTraditional = Schema::hasTable('tour_types') && Schema::hasColumn('tour_types', 'type_traditional');
        $hasTypeSimplified = Schema::hasTable('tour_types') && Schema::hasColumn('tour_types', 'type_simplified');
        $hasTraditionalName = Schema::hasColumn('tours', 'name_traditional');
        $hasSimplifiedName = Schema::hasColumn('tours', 'name_simplified');
        $baseToursQuery = Tours::query()
            ->where('status', 'Active')
            ->when($hasTypeRelation, function ($query) {
                $query->with(['type']);
            })
            ->withCount([
                'prices as active_rates_count' => function ($query) use ($now) {
                    $query->where('status', 'Active')
                        ->whereDate('expired_date', '>=', $now);
                },
                'locations as tour_destination_highlights_count' => function ($query) {
                    $query->where('is_active', true)
                        ->whereIn('location_type', ['Attraction', 'Activity']);
                },
                'locations as tour_food_stops_count' => function ($query) {
                    $query->where('is_active', true)
                        ->where('location_type', 'F&B');
                },
            ]);

        $areaOptions = $areaColumn
            ? (clone $baseToursQuery)
                ->whereNotNull($areaColumn)
                ->where($areaColumn, '!=', '')
                ->orderBy($areaColumn)
                ->pluck($areaColumn)
                ->unique()
                ->values()
            : collect();

        if ($hasTypeRelation) {
            $typeOptions = (clone $baseToursQuery)
                ->get()
                ->pluck('type')
                ->filter()
                ->unique('id')
                ->sortBy('type')
                ->values();
        } elseif ($hasLegacyType) {
            $typeOptions = (clone $baseToursQuery)
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->orderBy('type')
                ->pluck('type')
                ->unique()
                ->values();
        } else {
            $typeOptions = collect();
        }

        $topArea = $areaColumn
            ? (clone $baseToursQuery)
                ->whereNotNull($areaColumn)
                ->where($areaColumn, '!=', '')
                ->get()
                ->groupBy($areaColumn)
                ->sortByDesc(function ($items) {
                    return $items->count();
                })
                ->map(function ($items, $area) {
                    return [
                        'name' => $area,
                        'count' => $items->count(),
                    ];
                })
                ->first()
            : null;

        $toursQuery = (clone $baseToursQuery)
            ->with([
                'prices' => function ($query) use ($now) {
                    $query->where('status', 'Active')
                        ->whereDate('expired_date', '>=', $now)
                        ->orderBy('contract_rate');
                },
            ])
            ->when($searchName, function ($query) use ($searchName, $areaColumn, $hasTraditionalName, $hasSimplifiedName, $hasTypeRelation, $hasLegacyType, $hasTypeTraditional, $hasTypeSimplified) {
                $query->where(function ($nested) use ($searchName, $areaColumn, $hasTraditionalName, $hasSimplifiedName, $hasTypeRelation, $hasLegacyType, $hasTypeTraditional, $hasTypeSimplified) {
                    $nested->where('name', 'LIKE', "%{$searchName}%");

                    if ($hasTraditionalName) {
                        $nested->orWhere('name_traditional', 'LIKE', "%{$searchName}%");
                    }

                    if ($hasSimplifiedName) {
                        $nested->orWhere('name_simplified', 'LIKE', "%{$searchName}%");
                    }

                    if ($areaColumn) {
                        $nested->orWhere($areaColumn, 'LIKE', "%{$searchName}%");
                    }

                    if ($hasLegacyType) {
                        $nested->orWhere('type', 'LIKE', "%{$searchName}%");
                    }

                    if ($hasTypeRelation) {
                        $nested->orWhereHas('type', function ($typeQuery) use ($searchName, $hasTypeTraditional, $hasTypeSimplified) {
                            $typeQuery->where('type', 'LIKE', "%{$searchName}%");

                            if ($hasTypeTraditional) {
                                $typeQuery->orWhere('type_traditional', 'LIKE', "%{$searchName}%");
                            }

                            if ($hasTypeSimplified) {
                                $typeQuery->orWhere('type_simplified', 'LIKE', "%{$searchName}%");
                            }
                        });
                    }
                });
            })
            ->when($searchArea && $areaColumn, function ($query) use ($searchArea, $areaColumn) {
                $query->where($areaColumn, $searchArea);
            })
            ->when($searchType && $hasTypeRelation, function ($query) use ($searchType) {
                $query->where('type_id', $searchType);
            })
            ->when($searchType && !$hasTypeRelation && $hasLegacyType, function ($query) use ($searchType) {
                $query->where('type', $searchType);
            });

        $tours = $toursQuery
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $usdrates = UsdRates::where('name', 'USD')->first();
        $tax = Tax::where('name', 'tax')->first() ?: Tax::find(1);

        $tours->getCollection()->transform(function ($tour) use ($usdrates, $tax, $areaColumn, $hasTypeRelation, $hasLegacyType) {
            $tour->display_name = $this->localizedModelField($tour, 'name');
            $tour->display_area = $areaColumn
                ? ($areaColumn === 'area'
                    ? $this->localizedModelField($tour, 'area')
                    : (string) ($tour->{$areaColumn} ?? ''))
                : '';
            $tour->display_area = $tour->display_area ?: __('tour-packages.fallback.area');
            $tour->display_description = $this->localizedModelField($tour, 'short_description')
                ?: \Illuminate\Support\Str::limit(strip_tags($this->localizedModelField($tour, 'description')), 150);
            $tour->display_type = $hasTypeRelation && $tour->type
                ? $this->localizedModelField($tour->type, 'type')
                : ($hasLegacyType && filled($tour->type) ? $tour->type : __('tour-packages.fallback.type'));

            $startingPrice = $tour->prices
                ->map(function ($price) use ($usdrates, $tax) {
                    return ($usdrates && $tax) ? $price->calculatePrice($usdrates, $tax) : (float) $price->contract_rate;
                })
                ->filter()
                ->min();

            $tour->display_starting_price = $startingPrice
                ? 'USD ' . number_format((float) $startingPrice, 0)
                : __('tour-packages.fallback.request_rate');

            return $tour;
        });

        $featuredTour = $tours->first() ?: (clone $baseToursQuery)
            ->whereNotNull('cover')
            ->where('cover', '!=', '')
            ->orderBy('name')
            ->first();

        $directoryStats = [
            'total_tours' => (clone $baseToursQuery)->count(),
            'page_tours' => $tours->count(),
            'total_areas' => $areaOptions->count(),
            'total_types' => $typeOptions->count(),
            'top_area_name' => $topArea['name'] ?? null,
            'top_area_count' => $topArea['count'] ?? 0,
            'active_rates' => TourPrices::where('status', 'Active')
                ->whereDate('expired_date', '>=', $now)
                ->count(),
        ];

        return view('frontend.landing-page.tours.directory', compact(
            'tours',
            'areaOptions',
            'typeOptions',
            'searchName',
            'searchArea',
            'searchType',
            'featuredTour',
            'directoryStats'
        ));
    }
    public function accommodation_service(Request $request)
    {
        $now = Carbon::now()->toDateString();
        $searchName = $request->input('search_name');
        $searchRegion = $request->input('search_region');
        $promoAvailable = $request->boolean('promo_available');
        $baseHotelsQuery = Hotels::query()
            ->where('status', 'Active')
            ->select([
                'id',
                'code',
                'name',
                'region',
                'cover',
                'min_stay',
                'airport_duration',
                'airport_distance',
                'map',
            ])
            ->withCount([
                'promos as active_promos_count' => function ($query) use ($now) {
                    $query->active()->validForBooking($now);
                },
                'packages as active_packages_count' => function ($query) use ($now) {
                    $query->where('status', 'Active')
                        ->whereDate('stay_period_end', '>=', $now);
                },
            ]);

        $regionOptions = (clone $baseHotelsQuery)
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->orderBy('region')
            ->pluck('region')
            ->unique()
            ->values();

        $topRegion = (clone $baseHotelsQuery)
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->get()
            ->groupBy('region')
            ->sortByDesc(function ($items) {
                return $items->count();
            })
            ->map(function ($items, $region) {
                return [
                    'name' => $region,
                    'count' => $items->count(),
                ];
            })
            ->first();

        $minimumStayNights = (clone $baseHotelsQuery)
            ->whereNotNull('min_stay')
            ->min('min_stay');

        $hotelsQuery = (clone $baseHotelsQuery)
            ->when($searchName, function ($query, $value) {
                $query->where('name', 'LIKE', '%' . $value . '%');
            })
            ->when($searchRegion, function ($query, $value) {
                $query->where('region', 'LIKE', '%' . $value . '%');
            })
            ->when($promoAvailable, function ($query) use ($now) {
                $query->whereHas('promos', function ($promoQuery) use ($now) {
                    $promoQuery->active()->validForBooking($now);
                });
            });

        $hotels = $hotelsQuery
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $featuredHotel = $hotels->first() ?: (clone $baseHotelsQuery)
            ->whereNotNull('cover')
            ->where('cover', '!=', '')
            ->orderBy('name')
            ->first();

        $directoryStats = [
            'total_hotels' => $hotels->total(),
            'page_hotels' => $hotels->count(),
            'total_regions' => $regionOptions->count(),
            'top_region_name' => $topRegion['name'] ?? null,
            'top_region_count' => $topRegion['count'] ?? 0,
            'minimum_stay_nights' => $minimumStayNights ?: 1,
        ];

        return view('frontend.landing-page.accommodations.index', [
            'hotels' => $hotels,
            'regions' => $regionOptions,
            'searchName' => $searchName,
            'searchRegion' => $searchRegion,
            'promoAvailable' => $promoAvailable,
            'featuredHotel' => $featuredHotel,
            'directoryStats' => $directoryStats,
        ]);
    }

    public function activity_services(Request $request)
    {
        $searchName = trim((string) $request->input('search_name', ''));
        $searchLocation = trim((string) $request->input('search_location', ''));
        $searchType = trim((string) $request->input('search_type', ''));

        $baseActivitiesQuery = Activities::query()
            ->where('status', 'Active')
            ->select([
                'id',
                'partners_id',
                'name',
                'code',
                'type',
                'location',
                'description',
                'duration',
                'min_pax',
                'cover',
                'updated_at',
            ])
            ->with([
                'partners:id,name',
            ]);

        $locationOptions = (clone $baseActivitiesQuery)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->orderBy('location')
            ->pluck('location')
            ->unique()
            ->values();

        $typeOptions = (clone $baseActivitiesQuery)
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->orderBy('type')
            ->pluck('type')
            ->unique()
            ->values();

        $topLocation = (clone $baseActivitiesQuery)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->get()
            ->groupBy('location')
            ->sortByDesc(function ($items) {
                return $items->count();
            })
            ->map(function ($items, $location) {
                return [
                    'name' => $location,
                    'count' => $items->count(),
                ];
            })
            ->first();

        $minimumPax = (clone $baseActivitiesQuery)
            ->whereNotNull('min_pax')
            ->min('min_pax');

        $activities = (clone $baseActivitiesQuery)
            ->when($searchName, function ($query) use ($searchName) {
                $query->where(function ($nested) use ($searchName) {
                    $nested->where('name', 'LIKE', "%{$searchName}%")
                        ->orWhere('location', 'LIKE', "%{$searchName}%")
                        ->orWhere('type', 'LIKE', "%{$searchName}%")
                        ->orWhere('description', 'LIKE', "%{$searchName}%");
                });
            })
            ->when($searchLocation, function ($query) use ($searchLocation) {
                $query->where('location', $searchLocation);
            })
            ->when($searchType, function ($query) use ($searchType) {
                $query->where('type', $searchType);
            })
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $activities->getCollection()->transform(function ($activity) use ($minimumPax) {
            $activity->display_location = filled($activity->location)
                ? $activity->location
                : __('activities.fallback.location');
            $activity->display_type = filled($activity->type)
                ? $activity->type
                : __('activities.fallback.type');
            $activity->display_description = filled($activity->description)
                ? Str::limit(strip_tags($activity->description), 150)
                : __('activities.fallback.description');
            $activity->display_duration = filled($activity->duration)
                ? $activity->duration
                : __('activities.fallback.duration');
            $activity->display_min_pax = (int) ($activity->min_pax ?: ($minimumPax ?: 1));
            $activity->display_partner = filled(optional($activity->partners)->name)
                ? $activity->partners->name
                : __('messages.Supplier') . ' -';

            return $activity;
        });

        $featuredActivity = $activities->first() ?: (clone $baseActivitiesQuery)
            ->whereNotNull('cover')
            ->where('cover', '!=', '')
            ->orderBy('name')
            ->first();

        $directoryStats = [
            'total_activities' => (clone $baseActivitiesQuery)->count(),
            'page_activities' => $activities->count(),
            'total_locations' => $locationOptions->count(),
            'total_types' => $typeOptions->count(),
            'top_location_name' => $topLocation['name'] ?? null,
            'top_location_count' => $topLocation['count'] ?? 0,
            'minimum_pax' => $minimumPax ?: 1,
        ];

        return view('frontend.landing-page.activities.index', compact(
            'activities',
            'locationOptions',
            'typeOptions',
            'searchName',
            'searchLocation',
            'searchType',
            'featuredActivity',
            'directoryStats'
        ));
    }

    public function activity_detail(Request $request, $code)
    {
        $now = Carbon::now();
        $activity = Activities::query()
            ->where('status', 'Active')
            ->where('code', $code)
            ->with([
                'images' => function ($query) {
                    $query->select(['id', 'activities_id', 'image']);
                },
                'partners:id,name',
            ])
            ->firstOrFail();

        $activity->display_location = filled($activity->location)
            ? $activity->location
            : __('activities.fallback.location');
        $activity->display_type = filled($activity->type)
            ? $activity->type
            : __('activities.fallback.type');
        $activity->display_description = filled($activity->description)
            ? Str::limit(strip_tags($activity->description), 180)
            : __('activities.fallback.description');
        $activity->display_duration = filled($activity->duration)
            ? $activity->duration
            : __('activities.fallback.duration');
        $activity->display_min_pax = (int) ($activity->min_pax ?: 1);
        $activity->display_capacity = (int) ($activity->qty ?: $activity->display_min_pax);
        $activity->display_supplier = filled(optional($activity->partners)->name)
            ? $activity->partners->name
            : '-';
        $activity->display_validity = filled($activity->validity)
            ? dateFormat($activity->validity)
            : '-';

        $galleryImages = collect();

        if (filled($activity->cover)) {
            $galleryImages->push([
                'src' => asset('storage/activities/activities-cover/' . $activity->cover),
                'thumb' => getThumbnail('/activities/activities-cover/' . $activity->cover, 780, 520),
                'alt' => $activity->name,
            ]);
        }

        $galleryImages = $galleryImages->merge(
            $activity->images->filter(function ($image) {
                return filled($image->image);
            })->map(function ($image) use ($activity) {
                return [
                    'src' => asset('storage/activities/activities-images/' . $image->image),
                    'thumb' => asset('storage/activities/activities-images/' . $image->image),
                    'alt' => $activity->name,
                ];
            })
        )->unique('src')->values();

        $activitySections = collect([
            [
                'eyebrow' => __('messages.About This Activity'),
                'title' => __('messages.Essential activity information'),
                'content' => filled(trim(strip_tags((string) $activity->description))) ? $activity->description : null,
                'empty_text' => __('messages.Activity description is not available yet.'),
                'compact' => false,
            ],
            [
                'eyebrow' => __('messages.Included'),
                'title' => __('messages.What is included'),
                'content' => filled(trim(strip_tags((string) $activity->include))) ? $activity->include : null,
                'compact' => true,
            ],
            [
                'eyebrow' => __('messages.Itinerary'),
                'title' => __('messages.How the activity experience is structured'),
                'content' => filled(trim(strip_tags((string) $activity->itinerary))) ? $activity->itinerary : null,
                'compact' => true,
            ],
            [
                'eyebrow' => __('messages.Additional Information'),
                'title' => __('messages.Operational notes and service reminders'),
                'content' => filled(trim(strip_tags((string) $activity->additional_info))) ? $activity->additional_info : null,
                'compact' => true,
            ],
            [
                'eyebrow' => __('messages.Cancellation Policy'),
                'title' => __('messages.Cancellation terms'),
                'content' => filled(trim(strip_tags((string) $activity->cancellation_policy))) ? $activity->cancellation_policy : null,
                'compact' => true,
            ],
        ]);

        $contentSectionsCount = $activitySections->filter(function ($section) {
            return filled($section['content']);
        })->count();

        $summaryStats = [
            [
                'label' => __('messages.Location'),
                'value' => $activity->display_location,
            ],
            [
                'label' => __('messages.Type'),
                'value' => $activity->display_type,
            ],
            [
                'label' => __('messages.Duration'),
                'value' => $activity->display_duration,
            ],
            [
                'label' => __('messages.Min Pax'),
                'value' => $activity->display_min_pax . ' pax',
            ],
        ];

        $overviewFacts = [
            [
                'label' => __('messages.Location coverage'),
                'value' => $activity->display_location,
            ],
            [
                'label' => __('messages.Availability notes'),
                'value' => $activity->display_validity,
            ],
            [
                'label' => __('messages.Activity content'),
                'value' => $contentSectionsCount . ' ' . __('messages.sections'),
            ],
            [
                'label' => __('messages.Visual preview'),
                'value' => $galleryImages->count() . ' ' . __('messages.items'),
            ],
        ];

        $sidebarFacts = [
            [
                'label' => __('messages.Type'),
                'value' => $activity->display_type,
            ],
            [
                'label' => __('messages.Location'),
                'value' => $activity->display_location,
            ],
            [
                'label' => __('messages.Duration'),
                'value' => $activity->display_duration,
            ],
            [
                'label' => __('messages.Min Pax'),
                'value' => $activity->display_min_pax . ' pax',
            ],
            [
                'label' => __('messages.Capacity'),
                'value' => $activity->display_capacity . ' pax',
            ],
        ];

        if ($activity->display_supplier !== '-') {
            $sidebarFacts[] = [
                'label' => __('messages.Supplier'),
                'value' => $activity->display_supplier,
            ];
        }

        $tax = Tax::where('name', 'tax')->first() ?: Tax::find(1);
        $usdRate = UsdRates::where('name', 'USD')->first();
        $activePromotions = Promotion::query()
            ->where('status', 'Active')
            ->where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->get();
        $promotionDiscount = (float) $activePromotions->sum('discounts');
        $basePrice = $usdRate
            ? ceil(((float) $activity->contract_rate) / max((float) $usdRate->rate, 1)) + (float) $activity->markup
            : ((float) $activity->contract_rate + (float) $activity->markup);
        $taxAmount = $tax ? ceil(((float) $tax->tax / 100) * $basePrice) : 0;
        $pricePerPax = max($basePrice + $taxAmount, 0);
        $activityOrderErrors = $request->session()->get('errors', new ViewErrorBag());
        $activityOrderErrorKeys = collect($activityOrderErrors->keys());
        $activityOrderInitialStep = 0;

        if ($activityOrderErrorKeys->contains('guests') || $activityOrderErrorKeys->contains(fn ($key) => str_starts_with($key, 'guests'))) {
            $activityOrderInitialStep = 1;
        } elseif ($activityOrderErrorKeys->contains('note') || $activityOrderErrorKeys->contains('terms_accepted')) {
            $activityOrderInitialStep = 2;
        }

        $activityOrderForm = [
            'action' => route('view.activity-order.store', ['code' => $activity->code]),
            'capacity' => (int) ($activity->qty ?: $activity->display_capacity),
            'min_pax' => (int) $activity->display_min_pax,
            'price_per_pax' => $pricePerPax,
            'promotion_discount' => $promotionDiscount,
            'default_guest_count' => (int) max(1, $activity->display_min_pax),
            'default_travel_date' => $now->copy()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'),
            'minimum_travel_date' => $now->copy()->addHour()->format('Y-m-d\TH:i'),
            'duration_label' => $activity->display_duration,
            'supplier' => $activity->display_supplier,
            'order_source' => 'activity-detail-modern',
            'open_on_load' => old('activity_order_source') === 'activity-detail-modern' && $activityOrderErrors->any(),
            'initial_step' => $activityOrderInitialStep,
            'prefill' => [
                'number_of_guests' => (int) old('number_of_guests', max(1, $activity->display_min_pax)),
                'travel_date' => old('travel_date', $now->copy()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i')),
                'guests' => collect(old('guests', []))
                    ->map(function ($guest) {
                        return [
                            'name' => trim((string) ($guest['name'] ?? '')),
                            'phone' => trim((string) ($guest['phone'] ?? '')),
                            'age' => trim((string) ($guest['age'] ?? '')),
                            'sex' => trim((string) ($guest['sex'] ?? '')),
                            'identification_type' => trim((string) ($guest['identification_type'] ?? '')),
                            'identification_no' => trim((string) ($guest['identification_no'] ?? '')),
                            'is_leader' => (bool) ($guest['is_leader'] ?? false),
                        ];
                    })
                    ->filter(function ($guest) {
                        return collect($guest)
                            ->except('is_leader')
                            ->contains(fn ($value) => $value !== '');
                    })
                    ->values()
                    ->all(),
                'note' => old('note', ''),
            ],
        ];

        $nearActivities = Activities::query()
            ->where('status', 'Active')
            ->where('id', '!=', $activity->id)
            ->when(filled($activity->location), function ($query) use ($activity) {
                $query->where('location', $activity->location);
            }, function ($query) use ($activity) {
                $query->where('type', $activity->type);
            })
            ->orderBy('name')
            ->take(3)
            ->get()
            ->map(function ($nearActivity) {
                $nearActivity->display_location = filled($nearActivity->location)
                    ? $nearActivity->location
                    : __('activities.fallback.location');
                $nearActivity->display_type = filled($nearActivity->type)
                    ? $nearActivity->type
                    : __('activities.fallback.type');
                $nearActivity->display_duration = filled($nearActivity->duration)
                    ? $nearActivity->duration
                    : __('activities.fallback.duration');
                $nearActivity->display_description = filled($nearActivity->description)
                    ? Str::limit(strip_tags($nearActivity->description), 110)
                    : __('activities.fallback.description');

                return $nearActivity;
            });

        [$canUseActivityOrderFlow, $activityOrderCta] = $this->resolveActivityOrderAccess($activity->code);

        return view('frontend.landing-page.activities.detail', compact(
            'activity',
            'galleryImages',
            'activitySections',
            'summaryStats',
            'overviewFacts',
            'sidebarFacts',
            'contentSectionsCount',
            'nearActivities',
            'canUseActivityOrderFlow',
            'activityOrderCta',
            'activityOrderForm'
        ));
    }

    public function accommodation_detail(Request $request, $code)
    {
        $now = Carbon::now()->toDateString();
        $columns = [
            'id',
            'name',
            'code',
            'region',
            'address',
            'min_stay',
            'airport_duration',
            'airport_distance',
            'description',
            'facility',
            'benefits',
            'cover',
        ];

        foreach (['description', 'facility', 'benefits'] as $field) {
            foreach (['_traditional', '_simplified'] as $suffix) {
                $localizedColumn = $field . $suffix;

                if (Schema::hasColumn('hotels', $localizedColumn)) {
                    $columns[] = $localizedColumn;
                }
            }
        }

        $hotel = Hotels::query()
            ->select($columns)
            ->active()
            ->where('code', $code)
            ->with([
                'rooms' => function ($query) use ($now) {
                    $query->select(['id', 'hotels_id', 'rooms', 'cover'])
                        ->withCount([
                            'promos as active_promos_count' => function ($promoQuery) use ($now) {
                                $promoQuery->active()->validForBooking($now);
                            },
                            'packages as active_packages_count' => function ($packageQuery) use ($now) {
                                $packageQuery->where('status', 'Active')
                                    ->whereDate('stay_period_end', '>=', $now);
                            },
                        ])
                        ->with([
                            'promos' => function ($promoQuery) use ($now) {
                                $promoQuery->select([
                                    'id',
                                    'rooms_id',
                                    'name',
                                    'promotion_type',
                                    'book_periode_start',
                                    'book_periode_end',
                                    'periode_start',
                                    'periode_end',
                                ])
                                    ->active()
                                    ->validForBooking($now)
                                    ->orderBy('book_periode_start');
                            },
                            'packages' => function ($packageQuery) use ($now) {
                                $packageQuery->select([
                                    'id',
                                    'rooms_id',
                                    'name',
                                    'stay_period_start',
                                    'stay_period_end',
                                    'duration',
                                ])
                                    ->where('status', 'Active')
                                    ->whereDate('stay_period_end', '>=', $now)
                                    ->orderBy('stay_period_start');
                            },
                        ])
                        ->active()
                        ->orderBy('rooms');
                },
            ])
            ->firstOrFail();

        $hotel->localized_description = $this->localizedModelField($hotel, 'description');
        $hotel->localized_facility = $this->localizedModelField($hotel, 'facility');
        $hotel->localized_benefits = $this->localizedModelField($hotel, 'benefits');

        [$canUseCheckPriceForm, $checkPriceCta] = $this->resolveCheckPriceAccess($hotel->code);

        return view('frontend.landing-page.accommodations.detail', compact('hotel', 'canUseCheckPriceForm', 'checkPriceCta'));
    }

    public function remove_booking_code(Request $request)
    {
        $request->session()->forget('bookingcode');

        return back()->with('success', 'Booking code removed.');
    }

    private function localizedModelField($model, string $field): string
    {
        $locale = app()->getLocale();
        $localizedField = match ($locale) {
            'zh' => $field . '_traditional',
            'zh-CN' => $field . '_simplified',
            default => $field,
        };

        $value = trim((string) ($model->{$localizedField} ?? ''));

        if ($value !== '') {
            return $value;
        }

        return trim((string) ($model->{$field} ?? ''));
    }

    private function resolveCheckPriceAccess(string $hotelCode): array
    {
        if (!Auth::check()) {
            $redirectTarget = route('view.accommodation-detail', [
                'code' => $hotelCode,
                'check_price' => 1,
            ]) . '#check-price-panel';

            return [false, [
                'text' => __('messages.Login to continue to live hotel pricing.'),
                'url' => route('login', ['redirect' => $redirectTarget]),
                'button_label' => __('messages.Login to Check Price'),
            ]];
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return [false, [
                'text' => __('messages.Verify your email to continue to live hotel pricing.'),
                'url' => route('verification.notice'),
                'button_label' => __('messages.Verify Email'),
            ]];
        }

        if (!$this->isProfileComplete($user)) {
            return [false, [
                'text' => __('messages.Add a valid email to your profile to continue to live hotel pricing.'),
                'url' => route('profile'),
                'button_label' => __('messages.Update Email'),
            ]];
        }

        if (!$user->is_approved) {
            return [false, [
                'text' => __('messages.Your account approval is still pending. Open your profile to continue the approval flow before checking live hotel pricing.'),
                'url' => route('approval.pending'),
                'button_label' => __('messages.Profile'),
            ]];
        }

        return [true, [
            'text' => __('messages.Continue to the dedicated hotel check price page to view contract pricing, active promotions, and matching packages for your selected stay dates.'),
            'url' => route('view.accommodation-check-price', ['code' => $hotelCode]),
            'button_label' => __('messages.Check Price'),
        ]];
    }

    private function resolveActivityOrderAccess(string $activityCode): array
    {
        if (!Auth::check()) {
            $redirectTarget = route('view.activity-public-detail', [
                'code' => $activityCode,
                'continue_order' => 1,
            ]) . '#activity-action-panel';

            return [false, [
                'text' => __('messages.Login to continue to the activity order flow.'),
                'url' => route('login', ['redirect' => $redirectTarget]),
                'button_label' => __('messages.Login to Continue'),
            ]];
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return [false, [
                'text' => __('messages.Verify your email to continue to the activity order flow.'),
                'url' => route('verification.notice'),
                'button_label' => __('messages.Verify Email'),
            ]];
        }

        if (!$this->isProfileComplete($user)) {
            return [false, [
                'text' => __('messages.Add a valid email to your profile to continue to the activity order flow.'),
                'url' => route('profile'),
                'button_label' => __('messages.Update Email'),
            ]];
        }

        if (!$user->is_approved) {
            return [false, [
                'text' => __('messages.Your account approval is still pending. Open your profile to continue the approval flow before placing an activity order.'),
                'url' => route('approval.pending'),
                'button_label' => __('messages.Profile'),
            ]];
        }

        return [true, [
            'text' => __('messages.Continue to the dedicated activity order page to review price, apply booking code, and place your order.'),
            'url' => route('view.activity-detail', ['code' => $activityCode]),
            'button_label' => __('messages.Continue to Order'),
        ]];
    }

    private function isProfileComplete($user): bool
    {
        return filled($user->email);
    }

    private function resolveHomeServiceImagePath(?string $cover, string $directory): string
    {
        if (filled($cover)) {
            return $directory . ltrim($cover, '/');
        }

        return 'images/default.webp';
    }

    public function transport_service(Request $request)
    {
        $searchName = trim((string) $request->input('search_name', ''));
        $searchType = trim((string) $request->input('search_type', ''));
        $searchBrand = trim((string) $request->input('search_brand', ''));
        $minimumCapacity = $request->integer('minimum_capacity');

        $baseQuery = Transports::query()->where('status', 'Active');
        $types = (clone $baseQuery)
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->orderBy('type')
            ->pluck('type')
            ->unique()
            ->values();
        $brands = (clone $baseQuery)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->orderBy('brand')
            ->pluck('brand')
            ->unique()
            ->values();

        $transports = Transports::query()
            ->where('status', 'Active')
            ->when($searchName, function ($query) use ($searchName) {
                $query->where(function ($nested) use ($searchName) {
                    $nested->where('name', 'LIKE', "%{$searchName}%")
                        ->orWhere('brand', 'LIKE', "%{$searchName}%")
                        ->orWhere('type', 'LIKE', "%{$searchName}%");
                });
            })
            ->when($searchType, function ($query) use ($searchType) {
                $query->where('type', $searchType);
            })
            ->when($searchBrand, function ($query) use ($searchBrand) {
                $query->where('brand', $searchBrand);
            })
            ->when($minimumCapacity > 0, function ($query) use ($minimumCapacity) {
                $query->where('capacity', '>=', $minimumCapacity);
            })
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $directoryStats = [
            'total_transports' => (clone $baseQuery)->count(),
            'total_types' => $types->count(),
            'total_brands' => $brands->count(),
            'max_capacity' => (clone $baseQuery)->max('capacity') ?: 0,
        ];

        return view('frontend.landing-page.transports.index', compact(
            'transports',
            'types',
            'brands',
            'directoryStats',
            'searchName',
            'searchType',
            'searchBrand',
            'minimumCapacity'
        ));
    }
}
