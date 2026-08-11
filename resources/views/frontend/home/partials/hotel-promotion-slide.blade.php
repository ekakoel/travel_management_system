<div class="swiper-slide">
    <a class="home-promo-card" href="{{ route('view.hotel-detail', $promo->hotels?->code) }}" aria-label="{{ ($promo->hotels?->name ?? __('home.promo.default_hotel')) . ' ' . __('home.promo.card_aria_suffix', ['index' => $swiperIndex + 1]) }}">
        <div class="home-promo-card__media">
            <img src="{{ getThumbnail('hotels/hotels-cover/' . $promo->hotels->cover, 600, 900) }}" alt="{{ ($promo->hotels?->name ?? __('home.promo.default_hotel')) . ' ' . __('home.promo.image_suffix') }}">
            <span class="home-promo-card__badge">{{ $promo->promotion_type ?: __('home.promo.default_badge') }}</span>
        </div>
        <div class="home-promo-card__body">
            <div class="home-promo-card__eyebrow">
                <span class="home-promo-card__region">
                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                    {{ $promo->hotels?->region ?? __('home.promo.default_region') }}
                </span>
                @if (!empty($promo->minimum_stay))
                    <span class="home-promo-card__stay">{{ trans_choice('home.promo.minimum_stay', (int) $promo->minimum_stay, ['count' => $promo->minimum_stay]) }}</span>
                @endif
            </div>
            <h3 class="home-promo-card__title">{{ $promo->hotels?->name ?? __('home.promo.featured_title') }}</h3>
            <div class="home-promo-card__meta">
                <span>
                    <i class="far fa-calendar-alt" aria-hidden="true"></i>
                    {{ __('home.promo.stay_period') }}: {{ dateFormat($promo->periode_start) }} - {{ dateFormat($promo->periode_end) }}
                </span>
            </div>
            {{-- BAGIAN YANG TIDAK DIPERLUKAN --}}
            {{-- @if (trim(localized_model_field($promo, 'benefits')) !== '')
                <p class="home-promo-card__summary">{{ \Illuminate\Support\Str::limit(trim(strip_tags(localized_model_field($promo, 'benefits'))), 110) }}</p>
            @endif
            <span class="home-promo-card__link">
                {{ __('home.promo.view_promotion') }}
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </span> --}}
        </div>
    </a>
</div>
