<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\Promotion;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\BookingCode;
use App\Models\HotelPackage;
use App\Models\OptionalRate;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\OptionalRateOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class HotelsController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except([
            'checkPriceEntry',
        ]);
    }
    public function index(Request $request)
    {
        $now = Carbon::now();

        $promotions = Promotion::select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', "Active")
            ->where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->get();

        $hotels = $this->getHotelsQuery($request)->paginate(12);
        return view('main.hotels', compact('hotels', 'promotions'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $hotels = Hotels::where('name', 'LIKE', "%{$query}%")
            ->where('status', 'Active')
            ->limit(5)
            ->get(['id', 'name']);
    
        return response()->json(['hotels' => $hotels]);
    }
    public function autocompleteRegion(Request $request)
    {
        $query = $request->input('query');
        $regions = Hotels::where('region', 'LIKE', "%{$query}%")
            ->where('status', 'Active')
            ->select('region')
            ->distinct()
            ->limit(5)
            ->get(['id', 'region']);

        return response()->json(['regions' => $regions]);
    }
    
    public function loadMore(Request $request)
    {
        $hotels = $this->getHotelsQuery($request)->paginate(12);
        $html = view('partials.hotel-list', compact('hotels'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hotels->hasMorePages()
        ]);
    }

    private function getHotelsQuery(Request $request)
    {
        $now = Carbon::now();
        $hotelsQuery = Hotels::select('code', 'name', 'region', 'map', 'cover', 'id')
            ->where('status', 'active')
            ->with(['promos' => function ($query) use ($now) {
                $query->select('promotion_type', 'hotels_id')
                    ->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now)
                    ->latest();
            }]);

        if ($request->filled('hotel_name')) {
            $hotelsQuery->where('name', 'like', '%' . $request->input('hotel_name') . '%');
        }
        if ($request->filled('hotel_region')) {
            $hotelsQuery->where('region', 'like', '%' . $request->input('hotel_region') . '%');
        }

        return $hotelsQuery;
    }

    // HOTEL SEARCH ====================================================================================> OK
    public function search_hotel(Request $request)
    {
        if (!$request->hotel_name && !$request->hotel_region) {
            return redirect("/hotels");
        }
        $now = Carbon::now();
        $hotels = Hotels::query()
            ->select('code', 'name', 'region', 'map', 'cover', 'id')
            ->where('status', 'active')
            ->when($request->hotel_name, function ($query, $hotel_name) {
                $query->where('name', 'LIKE', "%$hotel_name%");
            })
            ->when($request->hotel_region, function ($query, $hotel_region) {
                $query->where('region', 'LIKE', "%$hotel_region%");
            })
            ->with(['promos' => function ($query) use ($now) {
                $query->select('promotion_type', 'hotels_id')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now)
                    ->where('status', 'Active');
            }])
            ->latest()
            ->get();
        $promotions = Promotion::query()
            ->select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', 'Active')
            ->whereBetween('periode_start', [$now->startOfDay(), $now->endOfDay()])
            ->get();
        return view('main.hotelsearch', compact('hotels', 'promotions'));
    }

    // Detail Hotel ====================================================================================> OK
    public function checkPriceEntry($code)
    {
        if (!Auth::check()) {
            session([
                'url.intended' => route('view.accommodation-detail', [
                    'code' => $code,
                    'check_price' => 1,
                ]) . '#check-price-panel',
            ]);

            return redirect()->guest(route('login'));
        }

        return redirect()->to(route('view.accommodation-detail', ['code' => $code, 'check_price' => 1]) . '#check-price-panel');
    }

    // Detail Hotel BOOKING CODE ===========================================================================>
    public function hoteldetail_bookingcode($code,$bcode){
        [$bookingCode, $bookingCodeStatus] = $this->resolveBookingCodeForCurrentUser($bcode);

        if (!$bookingCode) {
            $this->forgetBookingCodeSession();

            return redirect()
                ->route('view.accommodation-detail', ['code' => $code])
                ->with('danger', $this->bookingCodeStatusMessage($bookingCodeStatus));
        }

        $this->storeBookingCodeSession($bookingCode);

        return redirect()
            ->route('view.accommodation-detail', ['code' => $code])
            ->with('success', 'Booking code applied successfully.');
    }

// Hotel Price =========================================================================================>
    public function hotel_price(Request $request, $code)
    {
        try {
            [$checkin, $checkout] = $this->extractStayDates($request);
        } catch (\InvalidArgumentException $exception) {
            return redirect()
                ->route('view.accommodation-detail', ['code' => $code, 'check_price' => 1])
                ->with('error', __('messages.Please select stay dates to continue.'));
        }
        
        return redirect()->route('view.hotel-prices.page', [
            'code' => $code,
            'checkin' => $checkin,
            'checkout' => $checkout,
        ]);
    }

    public function hotel_price_page(Request $request, $code)
    {
        $checkin = $request->query('checkin') ?: session('booking_dates.checkin');
        $checkout = $request->query('checkout') ?: session('booking_dates.checkout');

        if (!$this->hasValidStayDates($checkin, $checkout)) {
            return redirect()
                ->route('view.accommodation-detail', ['code' => $code, 'check_price' => 1])
                ->with('error', __('messages.Please select stay dates to continue.'));
        }

        return $this->renderHotelPricePage($code, $checkin, $checkout, url()->previous());
    }

    private function renderHotelPricePage(string $code, string $checkin, string $checkout, ?string $previousUrl = null)
    {
        if ($previousUrl) {
            session(['previous_url' => $previousUrl]);
        }

        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        Session::put('booking_dates', [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'duration' => $duration,
        ]);
        $now = now()->format('Y-m-d');
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $business = Cache::remember('business_profile', 3600, function () {
            return BusinessProfile::select('id', 'name', 'address')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $roomSelectColumns = $this->getHotelRoomAvailabilitySelectColumns();

        $hotel = Hotels::with(['rooms' => function ($query) use ($roomSelectColumns) {
            $query->select($roomSelectColumns)
                  ->where('status', 'Active');
        }])->where('code', $code)->first();

        if (!$hotel) {
            abort(404);
        }

        $this->decorateHotelForLocale($hotel);
        
        $nearhotels = Hotels::with([
            'promos' => function ($query) use ($now) {
                $query->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now);
                }])->where('status', 'Active')
            ->where('region', $hotel->region)
            ->where('id', '!=', $hotel->id)
            ->take(8)
            ->get(['id','code','cover', 'name', 'region']);

        $promotions = Promotion::where('status', 'Active')
            ->where('periode_start','<=',$now)
            ->where('periode_end','>=',$now)
            ->get();
        $promotion_name = $promotions->pluck('name')->implode(', ');
        $promotion_price = $promotions->sum('discounts');
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
        if ($duration < $hotel->min_stay) {
            return redirect()
                ->route('view.accommodation-detail', ['code' => $code, 'check_price' => 1])
                ->with('error', __('messages.Minimum stay') . ' ' . $hotel->min_stay . ' ' . __('messages.nights'));
        }
        $promo_colors = [
            "Special Offer" => "bg-blue",
            "Best Choice"   => "bg-green",
            "Best Price"    => "bg-orange",
            "Hot Deal"      => "bg-red",
            "Normal"        => "bg-gray",
        ];
        $roomPrices = HotelPrice::
            with([
                'rooms'=> function ($query) {
                        $query->where('status', 'Active'); 
                    },
                'hotels',
            ])
            ->where('hotels_id', $hotel->id)
            ->get();

        $packages = HotelPackage::with(['hotels', 'room'])
            ->where('hotels_id', $hotel->id)
            ->where('status', 'Active')
            ->forDuration($duration)
            ->validForStay($checkin)
            ->get()
            ->map(function ($package) use ($usdrates, $tax) {
                $package->calculated_price = $package->calculatePrice($usdrates, $tax);
                return $package;
            });

        $hotelPromotions = HotelPromo::active()
            ->validForBooking($now)
            ->where('hotels_id', $hotel->id)
            ->get()
            ->keyBy('id');

        $bookingCodeDiscount = session('bookingcode.discounts');

        $processedPromos = [];
        $normalPriceData = [];
        foreach ($hotel->rooms as $room) {
            $promoDetails = $this->processPromo($room, $roomPrices, $hotelPromotions, $checkin, $duration, $usdrates, $tax);
            if ($promoDetails) {
                $processedPromos[] = $this->buildPromoRateCard($promoDetails, $hotelPromotions, $promo_colors, $bookingCodeDiscount);
            }

            $normalCard = $this->buildStandardRateCard(
                $room,
                $roomPrices,
                $checkin,
                $duration,
                $usdrates,
                $tax,
                $promotion_name,
                $promotion_price,
                $bookingCodeDiscount
            );

            if ($normalCard) {
                $normalPriceData[] = $normalCard;
            }
        }

        $packageCards = $packages
            ->map(function ($package) use ($bookingCodeDiscount) {
                return $this->buildPackageRateCard($package, $bookingCodeDiscount);
            })
            ->values()
            ->all();

        $rateSections = array_values(array_filter([
            [
                'key' => 'promotion',
                'eyebrow' => __('messages.Promotion Prices'),
                'title' => __('messages.Promotional room offers for your selected stay'),
                'cards' => $processedPromos,
            ],
            [
                'key' => 'package',
                'eyebrow' => __('messages.Package Prices'),
                'title' => __('messages.Bundled accommodation packages matching your stay'),
                'cards' => $packageCards,
            ],
            [
                'key' => 'standard',
                'eyebrow' => __('messages.Normal Prices'),
                'title' => __('messages.Standard contract rates across your selected dates'),
                'cards' => $normalPriceData,
            ],
        ], fn ($section) => count($section['cards']) > 0));

        $hasAnyResults = count($rateSections) > 0;

        return view('main.hotelavailability', [
            'tax' => $tax,
            'usdrates' => $usdrates,
            'business' => $business,
            'now' => $now,
            'hotel' => $hotel,
            'nearhotels' => $nearhotels,
            'promotions' => $promotions,
            'duration' => $duration,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rateSections' => $rateSections,
            'hasAnyResults' => $hasAnyResults,
        ]);
    }

    private function hasValidStayDates($checkin, $checkout): bool
    {
        if (!$checkin || !$checkout) {
            return false;
        }

        try {
            $checkinDate = Carbon::parse($checkin);
            $checkoutDate = Carbon::parse($checkout);
        } catch (\Throwable $exception) {
            return false;
        }

        return $checkoutDate->greaterThan($checkinDate);
    }

    private function processPromo($room, Collection $room_prices, Collection $hotel_promotions, $checkin, $duration, $usdrates, $tax)
    {
        $on_dates = [];
        $promo_type = [];
        $hotel_promo = [];
        $promo_id_list = [];
        $type_price_list = [];
        $type_price = [];
        $price_list = [];
        $promo_include = [];
        $current_date = strtotime($checkin);
        $promo_off = [];
        $check_availability = 0;

        $valid_promotions = $hotel_promotions->filter(function ($promo) use ($checkin, $duration) {
            return $this->calculatePromoDuration($promo, $checkin, $duration) > 0 
                && $promo->minimum_stay <= $duration;
        });

        $remaining_duration = $duration;

        for ($i = 0; $i < $duration; $i++) {
            $date_in = date('Y-m-d', $current_date + ($i * 86400));
            $has_promo = false;

            foreach ($valid_promotions as $hotel_promotion) {
                if ((int) $hotel_promotion->rooms_id === (int) $room->id) {
                    if ($hotel_promotion->periode_start <= $date_in && $hotel_promotion->periode_end >= $date_in) {
                        $promo_days_left = $this->calculatePromoDuration($hotel_promotion, $date_in, $remaining_duration);
                        if ($promo_days_left >= $hotel_promotion->minimum_stay) {
                            for ($j = 0; $j < $promo_days_left; $j++) {
                                if ($i + $j < $duration) {
                                    $current_date_in = date('Y-m-d', $current_date + (($i + $j) * 86400));
                                    $on_dates[] = $current_date_in;
                                    $promo_type[] = $hotel_promotion->promotion_type;
                                    $type_price[] = 1;
                                    $hotel_promo[] = $hotel_promotion;
                                    $promo_id_list[] = $hotel_promotion->id;
                                    $type_price_list[] = $hotel_promotion->id;
                                    $price_list[] = $hotel_promotion->calculatePrice($usdrates, $tax);
                                    $promo_include[] = $hotel_promotion->include;
                                    $promo_off[] = 1;
                                    $check_availability++;
                                    $remaining_duration--;
                                }
                            }
                            $has_promo = true;
                            $i += $promo_days_left - 1;
                            break;
                        }
                    }
                }
            }
            if (!$has_promo) {
                $normalPrice = $this->findRoomPriceForDate($room_prices, $room->id, $date_in);
                $type_price_list[] = $normalPrice?->id ?? 0;
                $normal_price = $normalPrice ? $normalPrice->calculatePrice($usdrates, $tax) : 0;
                $on_dates[] = $date_in;
                $promo_type[] = 'Normal';
                $type_price[] = 0;
                $hotel_promo[] = NULL;
                $promo_id_list[] = NULL;
                $promo_include[] = NULL;
                $promo_off[] = 0;
                $price_list[] = $normal_price;
                $remaining_duration--;
            }
        }
        $total_promo_off = array_sum($promo_off);
        if (in_array(0,$price_list)) {
            return NULL;
        }else{
            if ($total_promo_off > 0) {
                return [
                    'room' => $room,
                    'on_dates' => $on_dates,
                    'promo_type' => $promo_type,
                    'hotel_promo' => $hotel_promo,
                    'promo_id_list' => $promo_id_list,
                    'type_price_list' => $type_price_list,
                    'type_price' => $type_price,
                    'price_list' => $price_list,
                    'total_price' => array_sum($price_list),
                    'check_availability' => $check_availability,
                    'total_promo_off' => $total_promo_off,
                    'promo_include' => $promo_include,
                ];
            } else {
                return NULL;
            }
        }
    }

    private function calculatePromoDuration($hotel_promotion, $checkin, $duration)
    {
        $promo_check_duration = 0;
        for ($j = 0; $j < $duration; $j++) {
            $check_date = date('Y-m-d', strtotime("+$j days", strtotime($checkin)));
            if ($hotel_promotion->periode_start <= $check_date && $hotel_promotion->periode_end >= $check_date) {
                $promo_check_duration++;
            }
        }
        return $promo_check_duration;
    }

    private function findRoomPriceForDate(Collection $roomPrices, int $roomId, string $date)
    {
        return $roomPrices->first(function ($roomPrice) use ($roomId, $date) {
            return (int) $roomPrice->rooms_id === $roomId
                && $roomPrice->start_date <= $date
                && $roomPrice->end_date >= $date;
        });
    }

    private function buildOccupancyMeta($room)
    {
        $adults = (int) ($room->capacity_adult ?? 0);
        $children = (int) ($room->capacity_child ?? 0);

        if ($adults === 0 && $children === 0) {
            $adults = 2;
        }

        $adultLabel = $adults === 1 ? __('messages.Adult') : __('messages.Adults');
        $childLabel = $children === 1 ? __('messages.Child') : __('messages.Children');
        $label = $adults . ' ' . $adultLabel;

        if ($children > 0) {
            $label .= ' + ' . $children . ' ' . $childLabel;
        }

        return [
            'adults' => $adults,
            'children' => $children,
            'label' => $label,
        ];
    }

    private function getHotelRoomAvailabilitySelectColumns(): array
    {
        $columns = [
            'id',
            'hotels_id',
            'rooms',
            'status',
            'cover',
            'include',
            'capacity_adult',
            'capacity_child',
        ];

        foreach (['include_traditional', 'include_simplified'] as $optionalColumn) {
            if (Schema::hasColumn('hotel_rooms', $optionalColumn)) {
                $columns[] = $optionalColumn;
            }
        }

        return $columns;
    }

    private function buildPromoRateCard(array $promoDetails, Collection $hotelPromotions, array $promoColors, $bookingCodeDiscount)
    {
        $promotions = collect($promoDetails['promo_id_list'])
            ->filter()
            ->unique()
            ->map(fn ($promoId) => $hotelPromotions->get($promoId))
            ->filter()
            ->values();

        return [
            'category' => 'promotion',
            'offer_label' => __('messages.Promotional offer'),
            'sort_priority' => 1,
            'sort_price' => $promoDetails['total_price'],
            'room' => $promoDetails['room'],
            'room_name' => $promoDetails['room']->rooms,
            'room_cover' => $promoDetails['room']->cover,
            'occupancy' => $this->buildOccupancyMeta($promoDetails['room']),
            'meta_label' => count($promoDetails['price_list']) . ' ' . __('messages.nightly rates'),
            'badges' => $promotions
                ->map(function ($promotion) use ($promoColors) {
                    $promotionTypeLabel = $this->translateMessageLabel($promotion->promotion_type);

                    return [
                        'label' => $promotionTypeLabel . ' | ' . $promotion->minimum_stay . 'N',
                        'class' => 'availability-badge availability-badge--promo ' . ($promoColors[$promotion->promotion_type] ?? 'bg-default'),
                    ];
                })
                ->all(),
            'inline_notes' => array_values(array_filter([
                $bookingCodeDiscount ? __('messages.Booking code') . ' | $' . $bookingCodeDiscount : null,
            ])),
            'details_title' => null,
            'detail_partials' => $promotions
                ->filter(fn ($promotion) => !empty($promotion->include))
                ->map(function ($promotion) {
                    return [
                        'label' => __('messages.Include'),
                        'view' => 'partials.modal-hotel-promo-include',
                        'data' => ['hotel_promotion' => $promotion],
                    ];
                })
                ->all(),
            'nightly_rates' => collect($promoDetails['price_list'])
                ->map(function ($price, $index) use ($promoDetails, $promoColors) {
                    $promoType = $promoDetails['promo_type'][$index];

                    return [
                        'date' => $promoDetails['on_dates'][$index],
                        'short_date' => date('m/d', strtotime($promoDetails['on_dates'][$index])),
                        'price' => $price,
                        'price_class' => $promoColors[$promoType] ?? 'bg-default',
                    ];
                })
                ->all(),
            'totals' => [
                [
                    'label' => __('messages.Total stay price'),
                    'value' => $promoDetails['total_price'],
                    'muted' => false,
                    'strikethrough' => false,
                ],
            ],
            'price_heading' => __('messages.Nightly breakdown'),
            'panel_variant' => 'default',
            'footnote' => null,
            'booking_action' => [
                'route' => 'view.order-hotel-promo',
                'route_parameter' => $promoDetails['room']->id,
                'method' => 'POST',
                'label' => __('messages.Reserve now'),
                'helper' => __('messages.Review stay details and continue to guest information.'),
                'fields' => [
                    'promo_id' => json_encode($promotions->pluck('id')->values()->all()),
                    'promo_price' => $promoDetails['total_price'],
                    'price_list' => json_encode(array_values($promoDetails['price_list'])),
                ],
            ],
        ];
    }

    private function buildPackageRateCard($package, $bookingCodeDiscount)
    {
        return [
            'category' => 'package',
            'offer_label' => __('messages.Package offer'),
            'sort_priority' => 2,
            'sort_price' => $package->calculated_price,
            'room' => $package->room,
            'room_name' => $package->room->rooms,
            'room_cover' => $package->room->cover,
            'occupancy' => $this->buildOccupancyMeta($package->room),
            'meta_label' => $package->duration . ' ' . __('messages.nights'),
            'badges' => [
                [
                    'label' => __('messages.Package'),
                    'class' => 'availability-badge availability-badge--package',
                ],
            ],
            'inline_notes' => array_values(array_filter([
                $bookingCodeDiscount ? __('messages.Booking code') . ' | $' . $bookingCodeDiscount : null,
            ])),
            'details_title' => $package->name,
            'detail_partials' => array_values(array_filter([
                $package->benefits ? [
                    'label' => null,
                    'view' => 'partials.modal-hotel-package-benefits',
                    'data' => ['package' => $package],
                ] : null,
                $package->include ? [
                    'label' => null,
                    'view' => 'partials.modal-hotel-package-include',
                    'data' => ['package' => $package],
                ] : null,
            ])),
            'nightly_rates' => [],
            'totals' => [
                [
                    'label' => __('messages.Package total'),
                    'value' => $package->calculated_price,
                    'muted' => false,
                    'strikethrough' => false,
                ],
            ],
            'price_heading' => __('messages.Package price'),
            'panel_variant' => 'compact',
            'footnote' => __('messages.Valid for stay dates within') . ' ' . dateFormat($package->stay_period_start) . ' - ' . dateFormat($package->stay_period_end),
            'booking_action' => [
                'route' => 'view.order-hotel-package',
                'route_parameter' => $package->room->id,
                'method' => 'POST',
                'label' => __('messages.Reserve now'),
                'helper' => __('messages.Review stay details and continue to guest information.'),
                'fields' => [
                    'package_id' => $package->id,
                ],
            ],
        ];
    }

    private function buildStandardRateCard($room, Collection $roomPrices, string $checkin, int $duration, $usdrates, $tax, $promotionName, $promotionPrice, $bookingCodeDiscount)
    {
        $nightlyRates = [];
        $totalPrice = 0;
        $totalKickBack = 0;

        for ($k = 0; $k < $duration; $k++) {
            $currentDate = date('Y-m-d', strtotime('+' . $k . 'days', strtotime($checkin)));
            $roomPrice = $this->findRoomPriceForDate($roomPrices, $room->id, $currentDate);

            if (!$roomPrice) {
                return null;
            }

            $nightPrice = $roomPrice->calculatePrice($usdrates, $tax);
            $kickBack = $roomPrice->kick_back ?: 0;

            $nightlyRates[] = [
                'date' => $currentDate,
                'short_date' => date('m/d', strtotime($currentDate)),
                'price' => $nightPrice,
                'price_class' => 'bg-gray',
            ];

            $totalPrice += $nightPrice;
            $totalKickBack += $kickBack;
        }

        $finalPrice = $totalPrice - $totalKickBack - $promotionPrice;

        return [
            'category' => 'standard',
            'offer_label' => __('messages.Standard room rate'),
            'sort_priority' => 3,
            'sort_price' => $finalPrice,
            'room' => $room,
            'room_name' => $room->rooms,
            'room_cover' => $room->cover,
            'occupancy' => $this->buildOccupancyMeta($room),
            'meta_label' => count($nightlyRates) . ' ' . __('messages.nightly rates'),
            'badges' => array_values(array_filter([
                [
                    'label' => __('messages.Standard rate'),
                    'class' => 'availability-badge availability-badge--standard',
                ],
                $totalKickBack > 0 ? [
                    'label' => __('messages.Kick Back') . ' $ ' . $totalKickBack,
                    'class' => 'availability-badge availability-badge--rebate',
                ] : null,
            ])),
            'inline_notes' => array_values(array_filter([
                $promotionPrice > 0 ? $promotionName . ' ' . __('messages.Discount') . ' ' . currencyFormatUsd($promotionPrice) : null,
                $bookingCodeDiscount ? __('messages.Booking code') . ' | $' . $bookingCodeDiscount : null,
            ])),
            'details_title' => null,
            'detail_partials' => $room->include ? [
                [
                    'label' => null,
                    'view' => 'partials.modal-hotel-normal-include',
                    'data' => ['normal_price_rooms' => $room],
                ],
            ] : [],
            'nightly_rates' => $nightlyRates,
            'totals' => array_values(array_filter([
                ($totalKickBack > 0 || $promotionPrice > 0) ? [
                    'label' => __('messages.Published total'),
                    'value' => $totalPrice,
                    'muted' => true,
                    'strikethrough' => true,
                ] : null,
                [
                    'label' => __('messages.Final stay price'),
                    'value' => $finalPrice,
                    'muted' => false,
                    'strikethrough' => false,
                ],
            ])),
            'price_heading' => __('messages.Nightly breakdown'),
            'panel_variant' => 'default',
            'footnote' => null,
            'booking_action' => [
                'route' => 'view.order-hotel-normal',
                'route_parameter' => $room->id,
                'method' => 'POST',
                'label' => __('messages.Reserve now'),
                'helper' => __('messages.Review stay details and continue to guest information.'),
                'fields' => [
                    'hotel_id' => $room->hotels_id,
                    'checkin' => $checkin,
                    'checkout' => date('Y-m-d', strtotime('+' . $duration . ' days', strtotime($checkin))),
                    'duration' => $duration,
                    'price_pax' => $totalPrice,
                    'normal_price' => $totalPrice,
                    'kick_back' => $totalKickBack,
                    'kick_back_per_pax' => $duration > 0 ? (int) round($totalKickBack / $duration) : 0,
                    'service' => 'Hotel Normal',
                    'final_price' => $finalPrice,
                    'promo_id' => $promotionPrice > 0 ? json_encode([]) : null,
                    'price_list' => json_encode(array_map(fn ($night) => $night['price'], $nightlyRates)),
                    'bookingcode' => session('bookingcode.code'),
                ],
            ],
        ];
    }

    private function decorateHotelForLocale($hotel): void
    {
        $hotel->localized_description = $this->localizedModelField($hotel, 'description');
        $hotel->localized_facility = $this->localizedModelField($hotel, 'facility');
        $hotel->localized_benefits = $this->localizedModelField($hotel, 'benefits');
        $hotel->localized_additional_info = $this->localizedModelField($hotel, 'additional_info');
        $hotel->localized_cancellation_policy = $this->localizedModelField($hotel, 'cancellation_policy');
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

    private function translateMessageLabel(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $translationKey = 'messages.' . $value;
        $translated = __($translationKey);

        return $translated === $translationKey ? $value : $translated;
    }

    private function parseCheckInOut($checkincout)
    {
        $parts = array_map('trim', explode(' - ', (string) $checkincout));

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid check-in/check-out range.');
        }

        [$check_in, $check_out] = $parts;
        $checkinTimestamp = strtotime($check_in);
        $checkoutTimestamp = strtotime($check_out);

        if ($checkinTimestamp === false || $checkoutTimestamp === false) {
            throw new \InvalidArgumentException('Invalid check-in/check-out date.');
        }

        return [
            date('Y-m-d', $checkinTimestamp),
            date('Y-m-d', $checkoutTimestamp)
        ];
    }

    private function extractStayDates(Request $request): array
    {
        $stayRange = trim((string) $request->input('stay_range'));
        $checkInOut = $stayRange !== '' ? $stayRange : trim((string) $request->input('checkincout'));

        if ($checkInOut !== '') {
            try {
                [$checkin, $checkout] = $this->parseCheckInOut($checkInOut);

                if ($this->hasValidStayDates($checkin, $checkout)) {
                    return [$checkin, $checkout];
                }
            } catch (\InvalidArgumentException $exception) {
                // Fall back to hidden fields below when the visible range is malformed.
            }
        }

        $checkin = trim((string) $request->input('checkin'));
        $checkout = trim((string) $request->input('checkout'));

        if ($this->hasValidStayDates($checkin, $checkout)) {
            return [$checkin, $checkout];
        }

        return $this->parseCheckInOut($checkInOut);
    }

    private function resolveBookingCodeForCurrentUser(?string $bookingCodeInput): array
    {
        $bookingCodeValue = strtoupper(trim((string) $bookingCodeInput));

        if ($bookingCodeValue === '') {
            return [null, 'Invalid'];
        }

        $bookingCode = BookingCode::where('code', $bookingCodeValue)
            ->where('status', 'Active')
            ->first();

        if (!$bookingCode) {
            return [null, 'Invalid'];
        }

        $hasUsedCode = Orders::where('user_id', Auth::id())
            ->where('bookingcode', $bookingCodeValue)
            ->where('status', '!=', 'Rejected')
            ->exists();

        if ($hasUsedCode) {
            return [null, 'Used'];
        }

        $usedCount = Orders::where('bookingcode', $bookingCodeValue)
            ->where('status', '!=', 'Rejected')
            ->count();

        if ($usedCount >= (int) $bookingCode->amount) {
            return [null, 'Expired'];
        }

        if (Carbon::parse($bookingCode->expired_date)->startOfDay()->lt(Carbon::now()->startOfDay())) {
            return [null, 'Expired'];
        }

        return [$bookingCode, 'Valid'];
    }

    private function storeBookingCodeSession(BookingCode $bookingCode): void
    {
        session([
            'bookingcode' => [
                'id' => $bookingCode->id,
                'code' => $bookingCode->code,
                'discounts' => $bookingCode->discounts,
                'expired_date' => $bookingCode->expired_date,
                'status' => $bookingCode->status,
            ],
        ]);
    }

    private function forgetBookingCodeSession(): void
    {
        session()->forget('bookingcode');
    }

    private function bookingCodeStatusMessage(?string $status): string
    {
        return match ($status) {
            'Used' => 'The booking code that you entered has been used!',
            'Expired' => 'Booking code has expired!',
            default => 'Booking code is invalid!',
        };
    }

    // Hotel Price Booking code =========================================================================================>
    public function hotel_price_bookingcode(Request $request, $code,$bcode){
        [$bookingCode, $bookingCodeStatus] = $this->resolveBookingCodeForCurrentUser($bcode);

        if (!$bookingCode) {
            $this->forgetBookingCodeSession();

            return redirect()
                ->route('view.accommodation-detail', ['code' => $code, 'check_price' => 1])
                ->with('danger', $this->bookingCodeStatusMessage($bookingCodeStatus));
        }

        $this->storeBookingCodeSession($bookingCode);
        try {
            [$checkin, $checkout] = $this->extractStayDates($request);
        } catch (\InvalidArgumentException $exception) {
            return redirect()
                ->route('view.accommodation-detail', ['code' => $code, 'check_price' => 1])
                ->with('error', __('messages.Please select stay dates to continue.'));
        }

        return redirect()->route('view.hotel-prices.page', [
            'code' => $code,
            'checkin' => $checkin,
            'checkout' => $checkout,
        ]);
    }


// Function add optional rate to Order ======================================================================================= ==>
    public function func_add_optional_rate_order(Request $request){
        $service_date = date("Y-m-d", strtotime($request->service_date));
        $hotels_id= $request->hotels_id;
        $code = $request->code;
        $checkin = $request->checkin;
        $checkout = $request->checkout;
        $order =new OptionalRateOrder([
            
            "order_id"=>$request->order_id,
            "type"=>$request->type,
            "name"=>$request->name,
            "qty"=>$request->qty,
            "price_unit"=>$request->price_unit,
            "description" =>$request->description,
            "note"=>$request->note,
            "status"=>$request->status,
            "author"=>$request->author,
            "service_date"=>$service_date,
            "optional_rate_id"=>$request->optional_rate_id,
        ]);
        $order->save();
        // USER LOG
        $note = "Add Optional Rate Order";
        $user_log =new UserLog([
            "action"=>$request->action,
            "service"=>$request->service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "page"=>$request->page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        // @dd($order);
        return redirect("/orders")->with('success','Your order has been submited, and we will validate your order',[

       
            'hotels_id'=> $hotels_id,
            'code'=> $code,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
        ])->with('success', 'Package added successfully');
    }

}
