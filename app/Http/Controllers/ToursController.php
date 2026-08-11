<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Concerns\BuildsTourLocationItinerary;
use App\Models\Tours;
use App\Models\Orders;
use App\Models\Promotion;
use App\Models\TourPrices;
use App\Models\BookingCode;
use App\Models\ToursImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Services\Tours\TourPackagePricingService;
use App\Support\MoneyFormatter;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoretoursRequest;
use App\Http\Requests\UpdatetoursRequest;

class ToursController extends Controller

{   
    use BuildsTourLocationItinerary;

    public function __construct()
    {
        $this->middleware(['auth','verified'])->except(['view_tour_detail']);
    }
    public function index()
    {
        return redirect()->route('view.tour-packages-service');
    }
    public function loadMore(Request $request)
    {
        $tours = $this->getToursQuery($request)->paginate(12);
        $typeField = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $tourNameField = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $html = view('frontend.tours.partials.tour-list', compact('tours','typeField','tourNameField'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $tours->hasMorePages()
        ]);
    }

    private function getToursQuery(Request $request)
    {
        $toursQuery = Tours::where('status', 'Active')
            ->with(['images','prices']);

        if ($request->filled('tour_type')) {
            $toursQuery->where('type_id', $request->tour_type);
        }
        return $toursQuery;
    }
    // Search Tours =========================================================================================>
    public function search_tour(Request $request){
        return redirect()->route('view.tour-packages-service', array_filter([
            'search_area' => $request->input('tour_location'),
            'search_type' => $request->input('tour_type'),
        ], fn ($value) => filled($value)));
    }
    public function view_tour_detail($slug)
    {
        $user = Auth::user();
        $canViewTourRates = $this->canViewTourRates($user);
        $tourRateAccess = $this->tourRateAccessState($user);
        $now = Carbon::now();
        $defaultTravelDate = $now->copy()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');
        $prefillTravelDate = old('travel_date');

        if (filled($prefillTravelDate)) {
            try {
                $prefillTravelDate = Carbon::parse($prefillTravelDate)->format('Y-m-d\TH:i');
            } catch (\Throwable $exception) {
                $prefillTravelDate = $defaultTravelDate;
            }
        } else {
            $prefillTravelDate = $defaultTravelDate;
        }

        $selectedServiceDate = Carbon::parse($prefillTravelDate);
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));

        $tour = Tours::with(['images','activeLocations'])
            ->where('slug', $slug)
            ->where('status', 'Active')
            ->firstOrFail();
        $prices = collect();
        $priceUnavailable = false;
        $pricingAvailability = null;
        $bookingcode = session('bookingcode');
        $bookingcode_disc = session('bookingcode.disc', 0);
        $bookingCodeValue = is_object($bookingcode)
            ? ($bookingcode->code ?? null)
            : (is_array($bookingcode) ? ($bookingcode['code'] ?? null) : null);

        if ($canViewTourRates) {
            $pricingService = app(TourPackagePricingService::class);
            $formatter = app(MoneyFormatter::class);
            $tierReport = $pricingService->quoteEachTierReport(
                $tour,
                $selectedServiceDate,
                null,
                $bookingCodeValue,
                $user?->id
            );
            $tierQuotes = $tierReport['quotes'];
            $prices = $tierQuotes->map(function (array $tier) use ($formatter) {
                $price = $tier['price'];
                $quote = $tier['quote'];

                return [
                    'id' => $price->id,
                    'min_qty' => $price->min_qty,
                    'max_qty' => $price->max_qty,
                    'valid_from' => optional($price->valid_from)->toDateString(),
                    'valid_until' => optional($price->valid_until)->toDateString(),
                    'unit_price_usd_minor' => $quote->unitPriceUsdMinor(),
                    'unit_price_usd' => $formatter->decimal(Money::usdCents($quote->unitPriceUsdMinor())),
                ];
            });
            $priceUnavailable = $prices->isEmpty();
            $pricingAvailability = $this->tourPricingAvailability(
                $tierReport,
                $selectedServiceDate,
            );
        }

        $neartours = Tours::where('status',"Active")
        ->where('slug','!=',$slug)
        ->where('type_id',$tour->type_id)
        ->take(4)
        ->get();

        $promotions = Promotion::where('status', 'Active')
            ->where('pricing_data_status', 'ready')
            ->where('service_scope', 'Tour Package')
            ->get();
        $promotion_price = 0;
        $count_promotions = $promotions->count();
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langPackageHighlights = match (config('app.locale')) {
            'zh' => 'package_highlights_traditional',
            'zh-CN' => 'package_highlights_simplified',
            default => 'package_highlights',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        $tourMapLocations = $this->formatTourMapLocations($tour);
        $tourGeneratedItinerary = $this->buildTourLocationItineraryHtml(
            $tour,
            trim((string) ($tour->$langItinerary ?: $tour->itinerary))
        );
        $tourOrderForm = [
            'default_travel_date' => $defaultTravelDate,
            'minimum_travel_date' => $now->copy()->startOfDay()->format('Y-m-d\TH:i'),
            'prefill' => [
                'travel_date' => $prefillTravelDate,
            ],
        ];

        return view('frontend.landing-page.tours.detail',[
            'tour'=>$tour,
            'neartours'=>$neartours,
            'now'=>$now,
            'business'=>$business,
            'bookingcode'=>$bookingcode,
            'bookingcode_disc'=>$bookingcode_disc,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'count_promotions'=>$count_promotions,
            'langType'=>$langType,
            'langName'=>$langName,
            'langShortDescription'=>$langShortDescription,
            'langDescription'=>$langDescription,
            'langPackageHighlights'=>$langPackageHighlights,
            'langItinerary'=>$langItinerary,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
            'prices'=>$prices,
            'tourGeneratedItinerary'=>$tourGeneratedItinerary,
            'tourMapLocations'=>$tourMapLocations,
            'canViewTourRates'=>$canViewTourRates,
            'tourRateAccess'=>$tourRateAccess,
            'tourOrderForm'=>$tourOrderForm,
            'priceUnavailable'=>$priceUnavailable,
            'pricingAvailability'=>$pricingAvailability,


        ]);
    }

    private function canViewTourRates($user): bool
    {
        return $user
            && $user->hasVerifiedEmail()
            && (bool) $user->is_approved
            && $user->status === 'Active'
            && filled($user->name)
            && filled($user->phone)
            && filled($user->office)
            && filled($user->address)
            && filled($user->country);
    }

    private function tourRateAccessState($user): array
    {
        if (!$user) {
            return [
                'title' => __('tour-detail.login_required_title'),
                'message' => __('tour-detail.login_required_message'),
                'button' => __('tour-detail.login_to_view_rates'),
                'url' => route('login'),
            ];
        }

        if (!$user->hasVerifiedEmail()) {
            return [
                'title' => __('tour-detail.verify_required_title'),
                'message' => __('tour-detail.verify_required_message'),
                'button' => __('tour-detail.verify_account'),
                'url' => route('verification.notice'),
            ];
        }

        if (!(bool) $user->is_approved) {
            return [
                'title' => __('tour-detail.approval_required_title'),
                'message' => __('tour-detail.approval_required_message'),
                'button' => __('tour-detail.view_approval_status'),
                'url' => route('approval.pending'),
            ];
        }

        return [
            'title' => __('tour-detail.profile_required_title'),
            'message' => __('tour-detail.profile_required_message'),
            'button' => __('tour-detail.complete_profile'),
            'url' => route('profile'),
        ];
    }

    private function tourPricingAvailability(array $tierReport, Carbon $serviceDate): array
    {
        $eligiblePrices = collect($tierReport['eligible_prices'] ?? []);
        $failureCodes = collect($tierReport['failure_codes'] ?? []);
        $quotes = collect($tierReport['quotes'] ?? []);
        $rateCode = $failureCodes->first(
            fn (string $code) => str_starts_with($code, 'PRICING_RATE_')
        );
        $taxCode = $failureCodes->first(
            fn (string $code) => str_starts_with($code, 'PRICING_TAX_')
        );
        $quoteCode = $failureCodes->first(
            fn (string $code) => ! str_starts_with($code, 'PRICING_RATE_')
                && ! str_starts_with($code, 'PRICING_TAX_')
        );
        $tierLabels = $eligiblePrices->map(function (TourPrices $price) {
            return __('tour-detail.pricing_tier_label', [
                'min' => $price->min_qty,
                'max' => $price->max_qty,
                'from' => optional($price->valid_from)->toDateString(),
                'until' => optional($price->valid_until)->toDateString(),
            ]);
        })->values();

        return [
            'service_date' => $serviceDate->toDateString(),
            'tier_labels' => $tierLabels,
            'ready' => $quotes->isNotEmpty(),
            'requirements' => [
                [
                    'ready' => $eligiblePrices->isNotEmpty(),
                    'label' => __('tour-detail.pricing_requirement_tier'),
                    'detail' => $eligiblePrices->isNotEmpty()
                        ? trans_choice(
                            'tour-detail.pricing_tiers_ready',
                            $eligiblePrices->count(),
                            ['count' => $eligiblePrices->count()]
                        )
                        : __('tour-detail.pricing_tiers_missing', [
                            'date' => $serviceDate->toDateString(),
                        ]),
                ],
                [
                    'ready' => $rateCode === null,
                    'label' => __('tour-detail.pricing_requirement_rate'),
                    'detail' => $this->tourPricingFailureMessage($rateCode, 'rate'),
                ],
                [
                    'ready' => $taxCode === null,
                    'label' => __('tour-detail.pricing_requirement_tax'),
                    'detail' => $this->tourPricingFailureMessage($taxCode, 'tax'),
                ],
                [
                    'ready' => $quotes->isNotEmpty(),
                    'label' => __('tour-detail.pricing_requirement_quote'),
                    'detail' => $quotes->isNotEmpty()
                        ? __('tour-detail.pricing_quote_ready')
                        : $this->tourPricingFailureMessage($quoteCode, 'quote'),
                ],
            ],
        ];
    }

    private function tourPricingFailureMessage(?string $code, string $group): string
    {
        if ($code === null) {
            return match ($group) {
                'rate' => __('tour-detail.pricing_rate_ready'),
                'tax' => __('tour-detail.pricing_tax_ready'),
                default => __('tour-detail.pricing_quote_waiting'),
            };
        }

        return match ($code) {
            'PRICING_RATE_STALE' => __('tour-detail.pricing_rate_stale'),
            'PRICING_RATE_MISSING' => __('tour-detail.pricing_rate_missing'),
            'PRICING_RATE_AMBIGUOUS' => __('tour-detail.pricing_rate_ambiguous'),
            'PRICING_RATE_INVALID' => __('tour-detail.pricing_rate_invalid'),
            'PRICING_TAX_MISSING' => __('tour-detail.pricing_tax_missing'),
            'PRICING_TAX_AMBIGUOUS' => __('tour-detail.pricing_tax_ambiguous'),
            'PRICING_TAX_INVALID' => __('tour-detail.pricing_tax_invalid'),
            'PRICING_BOOKING_CODE_INVALID' => __('tour-detail.pricing_booking_code_invalid'),
            'PRICING_PROMOTION_INVALID' => __('tour-detail.pricing_promotion_invalid'),
            'PRICING_PAX_TIER_AMBIGUOUS' => __('tour-detail.pricing_tier_ambiguous'),
            default => __('tour-detail.pricing_quote_unavailable'),
        };
    }

    private function formatTourMapLocations(Tours $tour): array
    {
        $markerImage = $tour->cover
            ? getThumbnail('/tours/tours-cover/' . $tour->cover, 180, 180)
            : asset('images/default.webp');

        $dayCounters = [];

        return $tour->activeLocations
            ->filter(function ($location) {
                return is_numeric($location->latitude)
                    && is_numeric($location->longitude)
                    && (float) $location->latitude >= -90
                    && (float) $location->latitude <= 90
                    && (float) $location->longitude >= -180
                    && (float) $location->longitude <= 180;
            })
            ->values()
            ->map(function ($location, $index) use ($markerImage, &$dayCounters) {
                $markerStyle = $this->tourLocationMarkerStyle($location->location_type);
                $dayNumber = (int) $location->day_number;
                $dayCounters[$dayNumber] = ($dayCounters[$dayNumber] ?? 0) + 1;
                $locationImage = $location->marker_image
                    ? asset('storage/tours/tour-location-markers/' . $location->marker_image)
                    : null;

                return [
                    'order' => $index + 1,
                    'day' => $dayNumber,
                    'display_order' => $dayCounters[$dayNumber],
                    'visit_order' => (int) $location->visit_order,
                    'visit_time' => $location->visit_time ? Carbon::parse($location->visit_time)->format('H:i') : null,
                    'name' => $location->destination_name,
                    'type' => $location->location_type ?: 'Attraction',
                    'icon' => $markerStyle['icon'],
                    'color' => $markerStyle['color'],
                    'label' => $markerStyle['label'],
                    'description' => trim(strip_tags((string) $location->description)),
                    'lat' => (float) $location->latitude,
                    'lng' => (float) $location->longitude,
                    'location_image_url' => $locationImage,
                    'image_url' => $locationImage ?: $markerImage,
                    'google_maps_url' => $this->isTrustedMapUrl($location->google_maps_url) ? $location->google_maps_url : null,
                ];
            })
            ->all();
    }

    private function tourLocationMarkerStyle(?string $type): array
    {
        return match ($type) {
            'Activity' => [
                'icon' => 'fa-route',
                'color' => '#f97316',
                'label' => __('tour-map.location_type_activity'),
            ],
            'F&B' => [
                'icon' => 'fa-utensils',
                'color' => '#dc2626',
                'label' => __('tour-map.location_type_food_beverage'),
            ],
            'Pickup/Dropoff' => [
                'icon' => 'fa-hotel',
                'color' => '#2563eb',
                'label' => __('tour-map.location_type_pickup_dropoff'),
            ],
            default => [
                'icon' => 'fa-landmark',
                'color' => '#0f766e',
                'label' => __('tour-map.location_type_attraction'),
            ],
        };
    }

    private function isTrustedMapUrl(?string $url): bool
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), [
            'google.com',
            'www.google.com',
            'maps.google.com',
            'maps.app.goo.gl',
            'goo.gl',
        ], true);
    }
    public function tour_check_code(Request $request){
        $now = Carbon::now();
        $tour = Tours::where('id',$request->tour_id)->first();
        $bcode = $request->bookingcode;
        $user_id = Auth::user()->id;
        $orders = Orders::where('user_id', $user_id)->get();
        $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
        if (isset($bk_code)) {
            if ($bk_code->used < $bk_code->amount) {
                if (isset($orders)) {
                    $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                    if (isset($usedcode)) {
                        $bookingcode_status = "Used";
                        $bookingcode = null;
                    }else{
                        if ($bk_code->expired_date >= $now) {
                            $bookingcode_status = "Valid";
                            $bookingcode = $bk_code;
                        }else{
                            $bookingcode_status = "Expired";
                            $bookingcode = null ;
                        }
                    }
                }else{
                    if ($bk_code->expired_date >= $now) {
                        $bookingcode_status = "Valid";
                        $bookingcode = $bk_code;
                    }else{
                        $bookingcode_status = "Expired";
                        $bookingcode = null ;
                    }
                }
            }else{
                $bookingcode_status = "Expired";
                $bookingcode = null ;
            }
        }else{
            $bookingcode_status = 'Invalid';
            $bookingcode = null;
        }
        if (isset($bookingcode)) {
            return redirect()
                ->route('view.tour-detail', ['slug' => $tour->slug])
                ->with('bookingcode', $bookingcode);
        }else{
            return redirect()
                ->route('view.tour-detail', ['slug' => $tour->slug])
                ->with('danger', __('tour-detail.booking_code_status', [
                    'status' => __('tour-detail.booking_code_statuses.'.strtolower($bookingcode_status)),
                ]));
        }
    }

    public function view_tour_detail_bookingcode($code,$bcode)
    {
        $tour = Tours::query()->where('status', 'Active')->where('code', $code)->firstOrFail();
        $bookingCode = BookingCode::query()
            ->where('code', $bcode)
            ->where('status', 'Active')
            ->first();

        $redirect = redirect()->route('view.tour-detail', ['slug' => $tour->slug]);

        return $bookingCode !== null
            ? $redirect->with('bookingcode', $bookingCode)
            : $redirect->with('danger', __('tour-detail.booking_code_invalid'));
    }

    
}
