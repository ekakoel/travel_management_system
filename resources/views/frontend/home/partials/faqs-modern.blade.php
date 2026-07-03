@php
    $faqItems = [
        [
            'question' => __('messages.How do I register on the system?'),
            'answer' => __('messages.To initiate a partnership, agents are required to submit several business-related documents, including a valid business license, tax identification number, and other supporting materials. Once prepared, these documents should be sent via email to e-admin@balikamitour.com with the subject line â€œNew Agent Registration Request.â€ Our team will review the application and follow up accordingly.'),
        ],
        [
            'question' => __('messages.Who can become a partner agent of Bali Kami Tour?'),
            'answer' => __('messages.We welcome licensed travel agencies, tour operators, and corporate travel managers to join our network. All applicants must provide valid business credentials and undergo our verification process.'),
        ],
        [
            'question' => __('messages.How long does the registration approval process take?'),
            'answer' => __('messages.Once all required documents are received, the approval process typically takes between 2 to 5 business days. Our team will notify you via email regarding the status of your application.'),
        ],
        [
            'question' => __('messages.How do I access hotel promotions and special rates?'),
            'answer' => __('messages.After your registration is approved, you will receive login credentials to access our online system. From there, you can view available hotel promotions, special rates, and real-time availability tailored for partners.'),
        ],
        [
            'question' => __('messages.What kind of support is available for agents?'),
            'answer' => __('messages.Our dedicated support team is available 24/7 to assist you with technical issues, booking inquiries, and any operational concerns. We are committed to helping our partners deliver exceptional service.'),
        ],
        [
            'question' => __('messages.Can I track my booking history and invoices through the system?'),
            'answer' => __('messages.Absolutely. Once logged in, agents can view their full booking history, track current reservations, and download invoices directly from their dashboard.'),
        ],
    ];
@endphp

@php
    $faqItems = [
        [
            'question' => 'How do I register on the system?',
            'answer' => 'To begin the partnership process, please prepare your company profile, business license, tax identification number, and supporting business documents. Send them to e-admin@balikamitour.com with the subject line "New Agent Registration Request" and our team will review your submission.',
        ],
        [
            'question' => 'Who can become a partner agent of Bali Kami Tour?',
            'answer' => 'Licensed travel agencies, tour operators, wholesalers, and corporate travel managers are welcome to apply. We review each application to make sure the partnership is commercially aligned and professionally managed.',
        ],
        [
            'question' => 'How long does the registration approval process take?',
            'answer' => 'Once the required documents are complete, approval usually takes around 2 to 5 business days. We will update you by email as soon as the review is complete.',
        ],
        [
            'question' => 'How do I access hotel promotions and special rates?',
            'answer' => 'After your account is approved, you will receive access to the partner system where live promotions, special rates, and available inventory can be reviewed in one place.',
        ],
        [
            'question' => 'What kind of support is available for agents?',
            'answer' => 'Our team is available to support booking coordination, operational questions, platform guidance, and service clarification whenever your team needs quick and reliable assistance.',
        ],
        [
            'question' => 'Can I track booking history and invoices through the system?',
            'answer' => 'Yes. Approved partners can review booking activity, monitor active reservations, and access invoice records directly from the platform.',
        ],
    ];
@endphp

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
