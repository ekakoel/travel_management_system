@php
    $platformHighlights = [
        __('home.platform.highlights.worldwide'),
        __('home.platform.highlights.inventory'),
        __('home.platform.highlights.workflow'),
    ];
@endphp

<section class="home-section home-platform-section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 order-lg-2 wow fadeInUp" data-wow-delay="0.1s">
                <div class="home-platform__visual">
                    <img class="home-platform__image" src="{{ asset('landing-page/img/business_partner.png') }}" alt="{{ __('home.platform.image_alt') }}">
                </div>
            </div>

            <div class="col-lg-6 order-lg-1 wow fadeInUp" data-wow-delay="0.2s">
                <div class="home-platform__content">
                    <span class="home-section-heading__eyebrow">{{ __('home.platform.eyebrow') }}</span>
                    <h2 class="home-section-heading__title">{{ __('home.platform.title') }}</h2>
                    <p class="home-section-heading__subtitle home-platform__subtitle">
                        {{ __('home.platform.subtitle') }}
                    </p>

                    <div class="home-platform__highlights">
                        @foreach ($platformHighlights as $highlight)
                            <div class="home-platform__highlight">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                <span>{{ $highlight }}</span>
                            </div>
                        @endforeach
                    </div>

                    <a class="home-platform__link" href="{{ route('about-us') }}">
                        {{ __('home.platform.link') }}
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
