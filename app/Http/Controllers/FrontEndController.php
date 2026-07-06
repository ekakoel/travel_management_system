<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\HotelPackage;
use Illuminate\Support\Facades\Log;
use App\Models\HotelPromo;
use App\Models\Transports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FrontEndController extends Controller
{
    public function index(Request $request)
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
            ];

            return view('frontend.home.index', compact('promos', 'homeStats', 'homeServiceImages'));
        } catch (\Exception $e) {
            Log::error('Error on homepage: ' . $e->getMessage());
            abort(500, 'An error occurred while loading the homepage.');
        }
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

        return view('frontend.accommodations.index', [
            'hotels' => $hotels,
            'regions' => $regionOptions,
            'searchName' => $searchName,
            'searchRegion' => $searchRegion,
            'promoAvailable' => $promoAvailable,
            'featuredHotel' => $featuredHotel,
            'directoryStats' => $directoryStats,
        ]);
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

        return view('frontend.accommodations.detail', compact('hotel', 'canUseCheckPriceForm', 'checkPriceCta'));
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

        if (!$this->isProfileComplete($user) || $user->status !== 'Active') {
            return [false, [
                'text' => __('messages.Complete your profile to continue to live hotel pricing.'),
                'url' => route('profile'),
                'button_label' => __('messages.Complete Profile'),
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

    private function isProfileComplete($user): bool
    {
        return filled($user->name)
            && filled($user->phone)
            && filled($user->office)
            && filled($user->address)
            && filled($user->country);
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
        $searchName = $request->input('search_name');
        $searchType = $request->input('search_region');
        $transports = Transports::where('status','Active')->get();
        if ($searchName) {
            $transports->where('name', 'LIKE', "%{$searchName}%");
        }
        if ($searchType) {
            $transports->where('region', 'LIKE', "%{$searchType}%");
        }
        $types = $transports->pluck('type')->unique();
        return view('home.landing-page.transport', compact('transports','types', 'searchName', 'searchType'));
    }
}
