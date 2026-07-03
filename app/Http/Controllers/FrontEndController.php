<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
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
        $searchName = $request->input('search_name');
        $searchRegion = $request->input('search_region');
        $hotels = Hotels::where('status','Active')->get();
        if ($searchName) {
            $hotels->where('name', 'LIKE', "%{$searchName}%");
        }
        if ($searchRegion) {
            $hotels->where('region', 'LIKE', "%{$searchRegion}%");
        }
        $regions = $hotels->pluck('region')->unique();
        return view('frontend.accommodations.index', compact('hotels','regions', 'searchName', 'searchRegion'));
    }

    public function accommodation_detail(Request $request, $code)
    {
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
                'rooms' => function ($query) {
                    $query->select(['id', 'hotels_id', 'rooms', 'cover'])
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
                'text' => __('messages.Login to continue to live accommodation pricing.'),
                'url' => route('login', ['redirect' => $redirectTarget]),
                'button_label' => __('messages.Login to Check Price'),
            ]];
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return [false, [
                'text' => __('messages.Verify your email to continue to live accommodation pricing.'),
                'url' => route('verification.notice'),
                'button_label' => __('messages.Verify Email'),
            ]];
        }

        if (!$this->isProfileComplete($user) || $user->status !== 'Active') {
            return [false, [
                'text' => __('messages.Complete your profile to continue to live accommodation pricing.'),
                'url' => route('profile'),
                'button_label' => __('messages.Complete Profile'),
            ]];
        }

        if (!$user->is_approved) {
            return [false, [
                'text' => __('messages.Your account approval is still pending. Open your profile to continue the approval flow before checking live accommodation pricing.'),
                'url' => route('approval.pending'),
                'button_label' => __('messages.Profile'),
            ]];
        }

        return [true, [
            'text' => __('messages.Continue to the dedicated accommodation check price page to view contract pricing, active promotions, and matching packages for your selected stay dates.'),
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
