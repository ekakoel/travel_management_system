<section class="home-hero">
    <div class="container">
        <div class="home-hero__surface">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="home-hero__content wow fadeInUp" data-wow-delay="0.1s">
                        <span class="home-hero__eyebrow">{{ __('home.hero.eyebrow') }}</span>
                        <div class="home-hero__markets" aria-label="{{ __('home.hero.markets_aria') }}">
                            <span class="home-hero__market">{{ __('home.hero.markets.worldwide_agents') }}</span>
                            <span class="home-hero__market">{{ __('home.hero.markets.b2b_access') }}</span>
                            <span class="home-hero__market">{{ __('home.hero.markets.indonesia_specialist') }}</span>
                        </div>
                        <h1 class="home-hero__title">
                            {{ __('home.hero.title') }}
                        </h1>
                        <p class="home-hero__lead">
                            {{ __('home.hero.lead') }}
                        </p>

                        <div class="home-hero__actions">
                            <a href="{{ route('contact-us') }}" class="btn btn-primary home-hero__primary">
                                {{ __('home.hero.primary_cta') }}
                            </a>
                            <a href="{{ route('about-us') }}" class="home-hero__secondary">
                                {{ __('home.hero.secondary_cta') }}
                            </a>
                        </div>

                        <div class="home-hero__meta">
                            <div class="home-hero__meta-item">
                                <span class="home-hero__meta-value">{{ $homeStats['active_hotels'] }}+</span>
                                <span class="home-hero__meta-label">{{ __('home.hero.stats.active_hotels') }}</span>
                            </div>
                            <div class="home-hero__meta-item">
                                <span class="home-hero__meta-value">{{ $homeStats['active_transports'] }}+</span>
                                <span class="home-hero__meta-label">{{ __('home.hero.stats.transportation_choices') }}</span>
                            </div>
                            <div class="home-hero__meta-item">
                                <span class="home-hero__meta-value">{{ $homeStats['live_promotions'] }}+</span>
                                <span class="home-hero__meta-label">{{ __('home.hero.stats.live_promotions') }}</span>
                            </div>
                            <div class="home-hero__meta-item">
                                <span class="home-hero__meta-value">{{ $homeStats['support_label'] }}</span>
                                <span class="home-hero__meta-label">{{ __('home.hero.stats.partner_support') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="home-hero__visual wow fadeInUp" data-wow-delay="0.2s">
                        <div class="home-hero__visual-card home-hero__visual-card--primary">
                            <img src="{{ asset('landing-page/img/business_partner.jpeg') }}" alt="{{ __('home.hero.image_alt') }}">
                        </div>
                        <div class="home-hero__signal">
                            <span class="home-hero__signal-label">{{ __('home.hero.signal_label') }}</span>
                            <strong>{{ __('home.hero.signal_text') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
