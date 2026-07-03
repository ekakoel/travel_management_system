@php
    $partnerFlow = [
        [
            'step' => '01',
            'title' => __('home.flow.steps.apply.title'),
            'description' => __('home.flow.steps.apply.description'),
            'icon' => 'fas fa-file-signature',
        ],
        [
            'step' => '02',
            'title' => __('home.flow.steps.inventory.title'),
            'description' => __('home.flow.steps.inventory.description'),
            'icon' => 'fas fa-chart-line',
        ],
        [
            'step' => '03',
            'title' => __('home.flow.steps.confirm.title'),
            'description' => __('home.flow.steps.confirm.description'),
            'icon' => 'fas fa-check-circle',
        ],
    ];
@endphp

<section class="home-section home-flow-section">
    <div class="container">
        <div class="home-section-heading text-center mx-auto wow fadeInUp" data-wow-delay="0.1s">
            <span class="home-section-heading__eyebrow">{{ __('home.flow.eyebrow') }}</span>
            <h2 class="home-section-heading__title">{{ __('home.flow.title') }}</h2>
            <p class="home-section-heading__subtitle">
                {{ __('home.flow.subtitle') }}
            </p>
        </div>

        <div class="row g-4 home-flow-grid">
            @foreach ($partnerFlow as $item)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ 0.1 + ($loop->index * 0.1) }}s">
                    <article class="home-flow-card h-100">
                        <div class="home-flow-card__step">{{ $item['step'] }}</div>
                        <div class="home-flow-card__icon">
                            <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <h3 class="home-flow-card__title">{{ $item['title'] }}</h3>
                        <p class="home-flow-card__description">{{ $item['description'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
