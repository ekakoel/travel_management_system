@php
    $benefits = [
        [
            'icon' => 'fas fa-hand-holding-usd',
            'title' => __('home.benefits.items.commercial.title'),
            'description' => __('home.benefits.items.commercial.description'),
        ],
        [
            'icon' => 'fas fa-layer-group',
            'title' => __('home.benefits.items.visibility.title'),
            'description' => __('home.benefits.items.visibility.description'),
        ],
        [
            'icon' => 'fas fa-bolt',
            'title' => __('home.benefits.items.flow.title'),
            'description' => __('home.benefits.items.flow.description'),
        ],
        [
            'icon' => 'fas fa-headset',
            'title' => __('home.benefits.items.support.title'),
            'description' => __('home.benefits.items.support.description'),
        ],
    ];
@endphp

<section class="home-section home-benefits-section">
    <div class="container">
        <div class="home-section-heading text-center mx-auto wow fadeInUp" data-wow-delay="0.1s">
            <span class="home-section-heading__eyebrow">{{ __('home.benefits.eyebrow') }}</span>
            <h2 class="home-section-heading__title">{{ __('home.benefits.title') }}</h2>
            <p class="home-section-heading__subtitle">
                {{ __('home.benefits.subtitle') }}
            </p>
        </div>

        <div class="row g-4">
            @foreach ($benefits as $item)
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ 0.1 + ($loop->index * 0.1) }}s">
                    <article class="home-benefit-card h-100">
                        <div class="home-benefit-card__icon">
                            <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <h3 class="home-benefit-card__title">{{ $item['title'] }}</h3>
                        <p class="home-benefit-card__description">{{ $item['description'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
