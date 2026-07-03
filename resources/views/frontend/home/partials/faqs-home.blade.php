@php
    $faqItems = [
        [
            'question' => __('home.faq.items.register.question'),
            'answer' => __('home.faq.items.register.answer'),
        ],
        [
            'question' => __('home.faq.items.partner.question'),
            'answer' => __('home.faq.items.partner.answer'),
        ],
        [
            'question' => __('home.faq.items.approval.question'),
            'answer' => __('home.faq.items.approval.answer'),
        ],
        [
            'question' => __('home.faq.items.promotions.question'),
            'answer' => __('home.faq.items.promotions.answer'),
        ],
        [
            'question' => __('home.faq.items.support.question'),
            'answer' => __('home.faq.items.support.answer'),
        ],
        [
            'question' => __('home.faq.items.history.question'),
            'answer' => __('home.faq.items.history.answer'),
        ],
    ];
@endphp

<section class="home-section home-faq-section">
    <div class="container">
        <div class="home-section-heading text-center mx-auto wow fadeInUp" data-wow-delay="0.1s">
            <span class="home-section-heading__eyebrow">{{ __('home.faq.eyebrow') }}</span>
            <h2 class="home-section-heading__title">{{ __('home.faq.title') }}</h2>
            <p class="home-section-heading__subtitle">
                {{ __('home.faq.subtitle') }}
            </p>
        </div>

        <div class="row g-4 align-items-start home-faq-layout">
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="home-faq-aside">
                    <span class="home-faq-aside__eyebrow">{{ __('home.faq.aside.eyebrow') }}</span>
                    <h3 class="home-faq-aside__title">{{ __('home.faq.aside.title') }}</h3>
                    <p class="home-faq-aside__text">
                        {{ __('home.faq.aside.text') }}
                    </p>

                    <div class="home-faq-aside__points" aria-label="{{ __('home.faq.aside.points_aria') }}">
                        <span>{{ __('home.faq.aside.points.onboarding') }}</span>
                        <span>{{ __('home.faq.aside.points.access') }}</span>
                        <span>{{ __('home.faq.aside.points.support') }}</span>
                    </div>

                    <a class="home-faq-aside__link" href="{{ route('contact-us') }}">
                        {{ __('home.faq.aside.link') }}
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="accordion home-faq-accordion" id="homeFaqAccordion">
                    @foreach ($faqItems as $item)
                        <div class="accordion-item wow fadeInUp" data-wow-delay="{{ 0.1 + ($loop->index * 0.1) }}s">
                            <h2 class="accordion-header" id="homeFaqHeading{{ $loop->index }}">
                                <button
                                    class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#homeFaqCollapse{{ $loop->index }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="homeFaqCollapse{{ $loop->index }}"
                                >
                                    <span class="home-faq-accordion__index">{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="home-faq-accordion__question">{{ $item['question'] }}</span>
                                </button>
                            </h2>
                            <div
                                id="homeFaqCollapse{{ $loop->index }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                aria-labelledby="homeFaqHeading{{ $loop->index }}"
                                data-bs-parent="#homeFaqAccordion"
                            >
                                <div class="accordion-body">
                                    <p>{{ $item['answer'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
