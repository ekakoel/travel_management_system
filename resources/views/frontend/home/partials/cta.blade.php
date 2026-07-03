<section class="home-section home-cta-section">
    <div class="container">
        <div class="home-cta-surface wow fadeInUp" data-wow-delay="0.1s">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <span class="home-section-heading-dark__eyebrow">{{ __('home.cta.eyebrow') }}</span>
                    <h2 class="home-cta-surface__title">{{ __('home.cta.title') }}</h2>
                    <p class="home-cta-surface__text">
                        {{ __('home.cta.text') }}
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="home-cta-surface__actions">
                        <a href="{{ route('contact-us') }}" class="btn btn-primary home-cta-surface__primary">
                            {{ __('home.cta.primary_cta') }}
                        </a>
                        <a href="{{ route('about-us') }}" class="home-cta-surface__secondary">
                            {{ __('home.cta.secondary_cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
