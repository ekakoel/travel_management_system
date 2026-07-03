@push('styles')
    @once
        <link rel="stylesheet" href="{{ asset('css/pages/frontend-home-services.css') }}">
    @endonce
@endpush

@php
    $services = [
        [
            'delay' => '0.1s',
            'href' => route('view.accommodation-service'),
            'image' => getThumbnail($homeServiceImages['accommodations'] ?? 'images/default.webp', 760, 520),
            'title' => __('home.services.items.accommodations.title'),
            'accent' => __('home.services.items.accommodations.accent'),
            'description' => __('home.services.items.accommodations.description'),
            'image_class' => 'home-service-card__image--cover home-service-card__image--accommodation',
        ],
        [
            'delay' => '0.2s',
            'href' => route('view.transport-service'),
            'image' => getThumbnail($homeServiceImages['transportation'] ?? 'images/default.webp', 760, 520),
            'title' => __('home.services.items.transportation.title'),
            'accent' => __('home.services.items.transportation.accent'),
            'description' => __('home.services.items.transportation.description'),
            'image_class' => 'home-service-card__image--cover home-service-card__image--transport',
        ],
        [
            'delay' => '0.3s',
            'href' => route('tour-package-service'),
            'image' => getThumbnail($homeServiceImages['tours'] ?? 'images/default.webp', 760, 520),
            'title' => __('home.services.items.tours.title'),
            'accent' => __('home.services.items.tours.accent'),
            'description' => __('home.services.items.tours.description'),
            'image_class' => 'home-service-card__image--cover home-service-card__image--tour',
        ],
    ];
@endphp

<!-- Service Start -->
<section class="home-section home-services-section container-xxl">
    <div class="container">
        <div class="home-services-heading text-center mx-auto wow fadeInUp" data-wow-delay="0.1s">
            <span class="home-services-heading__eyebrow">{{ __('home.services.eyebrow') }}</span>
            <h2 class="home-services-heading__title">{{ __('home.services.title') }}</h2>
            <p class="home-services-heading__subtitle">
                {{ __('home.services.subtitle') }}
            </p>
        </div>

        <div class="row g-4">
            @foreach ($services as $service)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $service['delay'] }}">
                    <article class="home-service-card service-item h-100">
                        <a class="home-service-card__link" href="{{ $service['href'] }}">
                            <div class="home-service-card__media icon-container">
                                <img
                                    class="img-fluid hover-effect home-service-card__image {{ $service['image_class'] }}"
                                    src="{{ $service['image'] }}"
                                    alt="{{ $service['title'] }}"
                                    loading="lazy"
                                >

                                <div class="home-service-card__badge-wrap">
                                    <span class="home-service-card__accent">{{ $service['accent'] }}</span>
                                </div>
                            </div>

                            <div class="home-service-card__body">
                                <h3 class="home-service-card__title">{{ $service['title'] }}</h3>
                                <p class="home-service-card__description">{{ $service['description'] }}</p>

                                <div class="home-service-card__footer">
                                    <span class="home-service-card__line"></span>
                                    <span class="home-service-card__action" aria-hidden="true">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Service End -->
