<section class="home-section home-promo-section">
    <div class="container">
        <div class="home-promo-section__head wow fadeInUp" data-wow-delay="0.1s">
            <div class="home-section-heading home-promo-section__heading">
                <span class="home-section-heading__eyebrow">{{ __('home.promo.eyebrow') }}</span>
                <h2 class="home-section-heading__title">{{ __('home.promo.title') }}</h2>
                <p class="home-section-heading__subtitle">{{ __('home.promo.subtitle') }}</p>
            </div>

            <div class="home-promo-section__summary">
                <span class="home-promo-section__count">{{ trans_choice('home.promo.active_offers', $promos->count(), ['count' => $promos->count()]) }}</span>
                <a class="home-promo-section__link" href="{{ route('view.accommodation-service') }}">
                    {{ __('home.promo.explore_all') }}
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        @include('partials.frontend-loop-swiper', [
            'items' => $promos,
            'minimumRenderedSlides' => 24,
            'slidePartial' => 'frontend.home.partials.hotel-promotion-slide',
            'slideVariable' => 'promo',
            'swiperClass' => 'hotel-promo-swiper mySwiper',
            'wrapperClass' => 'fadeInUp',
            'wrapperDelay' => '0.1s',
            'swiperEffect' => 'slide',
            'swiperSpeed' => 650,
            'showNavigation' => true,
            'showPagination' => false,
            'emptyMessage' => __('home.promo.empty'),
            'emptyMessageClass' => 'text-center',
        ])
    </div>
</section>
