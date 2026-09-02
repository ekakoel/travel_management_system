<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Activities;
use App\Models\Tours;
use App\Models\Tax;
use App\Models\User;
use App\Models\Hotels;
use App\Models\UsdRates;
use App\Models\Promotion;
use App\Models\HotelPromo;
use App\Models\Transports;
use App\Models\BookingCode;
use App\Services\TransportOrderNumberService;
use Illuminate\Http\Request;
use App\Services\BusinessProfileService;
use App\Services\PublicFaqService;
use Illuminate\Support\Facades\Schema;
use App\Models\HomeSlider;

class HomeController extends Controller
{
    private function getBookingAgents()
    {
        return User::where('status', 'Active')
            ->whereNotNull('email_verified_at')
            ->where('is_approved', true)
            ->get();
    }
    
    public function index(Request $request, PublicFaqService $publicFaqService)
    {
        $now = Carbon::now();
        $promos = HotelPromo::with('hotels')
            ->active()
            ->validForBooking($now)
            ->orderBy('book_periode_start', 'asc')
            ->get()
            ->unique('hotels_id');
        $homeFaqItems = $publicFaqService->items();
        $sliders = HomeSlider::active()
            ->orderBy('sort_order')
            ->get();

        return view('frontend.home.index', compact('promos', 'homeFaqItems','sliders'));
    }

    public function about_us(Request $request, BusinessProfileService $businessProfileService)
    {
        return view('frontend.landing-page.about.index', [
            'businessProfile' => $businessProfileService->primary(),
        ]);
    }

    public function contact_us(Request $request, BusinessProfileService $businessProfileService)
    {
        $businessProfile = $businessProfileService->primary();
        $profileValue = function (string $field, $fallback = null) use ($businessProfile) {
            $value = trim((string) ($businessProfile->{$field} ?? ''));

            return $value !== '' && $value !== '-' ? $value : $fallback;
        };

        $phone = collect([
            $profileValue('phone'),
            $profileValue('phone_2'),
            $profileValue('phone_3'),
        ])->filter()->implode(' / ');
        $email = $profileValue('email', config('app.administrator_mail', 'e-admin@balikamitour.com'));
        $website = $profileValue('website', config('app.app_url', 'online.balikamitour.com'));
        $websiteUrl = str_starts_with($website, 'http') ? $website : 'https://' . $website;
        $whatsapp = $profileValue('whatsapp', $profileValue('phone'));
        $whatsappNumber = preg_replace('/[^0-9]/', '', (string) $whatsapp);
        $map = $profileValue('map');
        $mapSrc = $this->normalizeGoogleMapEmbedUrl($map);

        return view('frontend.landing-page.contact.index', [
            'businessProfile' => $businessProfile,
            'contactData' => [
                'company_name' => $profileValue('nickname', $profileValue('name', config('app.business', config('app.name', 'Bali Kami Tour')))),
                'company_type' => $profileValue('type', 'B2B Travel Partner'),
                'address' => $profileValue('address', config('app.bali_contact_office', 'Bali, Indonesia')),
                'phone' => $phone ?: '(+62 361) 710661 / 710663 / 710664',
                'phone_href' => preg_replace('/[^0-9+]/', '', explode('/', $phone)[0] ?? $phone),
                'email' => $email,
                'website' => $website,
                'website_url' => $websiteUrl,
                'whatsapp_url' => $whatsappNumber ? 'https://wa.me/' . $whatsappNumber : null,
                'map_src' => $mapSrc,
            ],
        ]);
    }

    private function normalizeGoogleMapEmbedUrl(?string $map): string
    {
        $defaultMap = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.821050968989!2d115.22173570000001!3d-8.708537300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24112fd4d0b17%3A0x6c3c0731cda9e79!2sBali%20Kami%20Tour%20and%20Wedding!5e0!3m2!1sen!2sid!4v1745998344458!5m2!1sen!2sid';
        $map = trim((string) $map);

        if ($map === '' || $map === '-') {
            return $defaultMap;
        }

        if (str_contains($map, 'google.com/maps/embed')) {
            return $map;
        }

        return $defaultMap;
    }

    public function transportation_service(Request $request)
    {
        $searchName = trim((string) $request->input('search_name', ''));
        $searchType = trim((string) $request->input('search_type', $request->input('search_region', '')));
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

    public function hotels_service(Request $request)
    {
        return app(FrontEndController::class)->hotels_service($request);
    }

    public function services(Request $request)
    {
        $tourAreaColumn = Schema::hasColumn('tours', 'area')
            ? 'area'
            : (Schema::hasColumn('tours', 'region') ? 'region' : null);

        $serviceCards = [
            [
                'icon' => 'fas fa-hotel',
                'title' => __('messages.Accommodations'),
                'description' => __('messages.Curated hotels, villas, and premium stays for professional travel programs.'),
                'href' => route('view.hotels-service'),
            ],
            [
                'icon' => 'fas fa-car-side',
                'title' => __('messages.Transports'),
                'description' => __('messages.Airport shuttle and daily rent transport options for seamless guest mobility.'),
                'href' => route('view.transports-service'),
            ],
            [
                'icon' => 'fas fa-map-marked-alt',
                'title' => __('messages.Tour Packages'),
                'description' => __('messages.Private and curated Indonesia journeys designed for international travel agents.'),
                'href' => route('view.tour-packages-service'),
            ],
            [
                'icon' => 'fas fa-hiking',
                'title' => __('messages.Activities'),
                'description' => __('messages.Experiences, excursions, and add-on activity options for more complete client itineraries.'),
                'href' => route('view.activities-service'),
            ],
        ];

        $servicePreviews = [
            'accommodations' => Hotels::query()
                ->active()
                ->select(['id', 'name', 'code', 'region', 'cover', 'updated_at'])
                ->latest('updated_at')
                ->take(3)
                ->get()
                ->map(function ($hotel) {
                    return [
                        'title' => $hotel->name,
                        'meta' => $hotel->region ?: __('messages.Accommodation'),
                        'image' => $hotel->cover ? getThumbnail('/hotels/hotels-cover/' . $hotel->cover, 520, 340) : getThumbnail('/images/default.webp', 520, 340),
                        'href' => route('view.hotel-detail', $hotel->code),
                    ];
                }),
            'transports' => Transports::query()
                ->where('status', 'Active')
                ->select(['id', 'name', 'code', 'type', 'brand', 'capacity', 'cover', 'updated_at'])
                ->latest('updated_at')
                ->take(3)
                ->get()
                ->map(function ($transport) {
                    return [
                        'title' => trim($transport->brand . ' ' . $transport->name),
                        'meta' => trim(($transport->type ?: __('messages.Transport')) . ($transport->capacity ? ' / ' . $transport->capacity . ' pax' : '')),
                        'image' => $transport->cover ? getThumbnail('/transports/transports-cover/' . $transport->cover, 520, 340) : getThumbnail('/images/default.webp', 520, 340),
                        'href' => route('transport.show', $transport->id),
                    ];
                }),
            'tours' => Tours::query()
                ->where('status', 'Active')
                ->select(array_filter(['id', 'name', 'slug', 'cover', $tourAreaColumn, 'updated_at']))
                ->latest('updated_at')
                ->take(3)
                ->get()
                ->map(function ($tour) use ($tourAreaColumn) {
                    $area = $tourAreaColumn ? ($tour->{$tourAreaColumn} ?: __('messages.Tour Package')) : __('messages.Tour Package');

                    return [
                        'title' => $tour->name,
                        'meta' => $area,
                        'image' => $tour->cover ? getThumbnail('/tours/tours-cover/' . $tour->cover, 520, 340) : getThumbnail('/images/default.webp', 520, 340),
                        'href' => route('view.tour-detail', $tour->slug ?: $tour->id),
                    ];
                }),
            'activities' => Activities::query()
                ->published()
                ->select(['id', 'name', 'code', 'type', 'location', 'cover', 'updated_at'])
                ->latest('updated_at')
                ->take(3)
                ->get()
                ->map(function ($activity) {
                    return [
                        'title' => $activity->name,
                        'meta' => $activity->location ?: ($activity->type ?: __('messages.Activity')),
                        'image' => $activity->cover ? getThumbnail('/activities/activities-cover/' . $activity->cover, 520, 340) : getThumbnail('/images/default.webp', 520, 340),
                        'href' => route('view.activity-public-detail', $activity->code),
                    ];
                }),
        ];

        return view('frontend.landing-page.services.index', compact('serviceCards', 'servicePreviews'));
    }

    public function show($id)
    {
        $hotel = Hotels::findOrFail($id);

        return redirect()->route('view.hotel-detail', ['code' => $hotel->code]);
    }
    public function show_transport($id, TransportOrderNumberService $transportOrderNumberService)
    {
        $now = Carbon::now();
        $transport = Transports::with(['prices' => function ($query) {
            $query->orderBy('type')->orderBy('src')->orderBy('dst');
        }])->where('status', 'Active')->findOrFail($id);
        $usdrates = UsdRates::where('name', 'USD')->first();
        $tax = Tax::where('name', 'tax')->first() ?: Tax::find(1);
        $bookingCode = BookingCode::where('code', session('bookingcode.code'))->first();
        $bookingCodeDiscount = $bookingCode?->discounts ?? 0;
        $promotionDiscount = Promotion::where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->where('status', 'Active')
            ->sum('discounts');
        $prices = $transport->prices->map(function ($price) use ($usdrates, $tax) {
            $price->final_price = ($usdrates && $tax) ? $price->calculatePrice($usdrates, $tax) : null;
            return $price;
        });
        $priceGroups = $prices->groupBy('type');
        $similarTransports = Transports::where('status', 'Active')
            ->where('id', '!=', $transport->id)
            ->where('type', $transport->type)
            ->orderByDesc('capacity')
            ->take(3)
            ->get();
        $agents = auth()->check() ? $this->getBookingAgents() : collect();
        $selectedAgentId = (int) old('user_id', auth()->id());
        $selectedAgent = $agents->firstWhere('id', $selectedAgentId) ?: auth()->user();
        $orderNumber = auth()->check() && $selectedAgent
            ? $transportOrderNumberService->generate($selectedAgent, $now)
            : '';
        $transportOrderNumbersByAgent = $agents
            ->mapWithKeys(fn (User $agent) => [
                $agent->id => $transportOrderNumberService->generate($agent, $now),
            ]);
        

        return view('frontend.landing-page.transports.detail', compact(
            'usdrates',
            'prices',
            'tax',
            'transport',
            'priceGroups',
            'similarTransports',
            'orderNumber',
            'bookingCode',
            'bookingCodeDiscount',
            'promotionDiscount',
            'agents',
            'transportOrderNumbersByAgent'
        ));
    }
    public function show_tour_package($id)
    {
        $tour = Tours::with('images')->find($id);

        if (!$tour || $tour->status !== 'Active') {
            return redirect("/tour-package-service")->with('danger', "The tour can't be found!");
        }

        return redirect()->route('view.tour-detail', ['slug' => $tour->slug ?: $tour->id]);
    }



}
