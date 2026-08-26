@extends('frontend.layouts.app')

@section('title', __('messages.About Us'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/about-page-entry.css') }}">
@endpush

@php
    $businessProfile = $businessProfile ?? null;
    $profileValue = function (string $field, $fallback = null) use ($businessProfile) {
        if (!$businessProfile) {
            return $fallback;
        }

        $value = trim((string) ($businessProfile->{$field} ?? ''));

        return $value !== '' ? $value : $fallback;
    };
    $localizedProfileValue = function (string $field, $fallback = null) use ($profileValue) {
        $localizedField = match (app()->getLocale()) {
            'zh' => $field . '_traditional',
            'zh-CN' => $field . '_simplified',
            default => $field,
        };

        return $profileValue($localizedField, $profileValue($field, $fallback));
    };
    $companyName = $profileValue('nickname', $profileValue('name', config('app.business', config('app.name', 'Bali Kami Tour'))));
    $companyLegalName = $profileValue('name', $companyName);
    $companyType = $profileValue('type', __('messages.Partner focused platform'));
    $companyAddress = $profileValue('address', config('app.bali_contact_office', 'Bali'));
    $companyPhone = collect([
        $profileValue('phone'),
        $profileValue('phone_2'),
        $profileValue('phone_3'),
    ])->filter()->implode(' / ');
    $companyEmail = $profileValue('email', config('app.administrator_mail'));
    $companyWebsite = $profileValue('website', config('app.app_url'));
    $companyTagline = $localizedProfileValue('public_tagline', __('messages.Discover who we are, what we stand for, and how we help you grow in the luxury travel market.'));
    $companyDescription = $localizedProfileValue('public_description', __('messages.Bali Kami Tour is a Bali based B2B travel company dedicated to delivering high end travel experiences across Indonesia. With a strong foundation in the luxury tourism sector, we specialize in providing exceptional services tailored for discerning clients and professional travel partners. Our offerings include premium hotels at world class five star hotels and private, handpicked villas, ensuring comfort, privacy, and elegance. We also provide a full suite of luxury transportation options from executive class vehicles and VIP airport transfers to private helicopter charters for a truly elevated journey. In addition, our bespoke private tour packages are curated with precision, combining cultural richness, natural beauty, and personalized attention to meet the highest international standards. Our commitment to quality, reliability, and seamless service makes us a trusted partner for travel agents, tour operators, and agencies seeking superior travel solutions in Indonesia. At Bali Kami Tour, we don’t just arrange travel we craft unforgettable experiences.'));

    $heroStats = [
        ['value' => $companyType, 'label' => __('messages.Partner focused platform')],
        ['value' => $companyAddress, 'label' => __('messages.Indonesia travel services')],
        ['value' => $companyPhone ?: '24/7', 'label' => __('messages.Operational support')],
        ['value' => $companyEmail ?: $companyWebsite, 'label' => __('messages.Partner Portal')],
    ];

    $servicePillars = [
        [
            'icon' => 'fas fa-hotel',
            'title' => __('messages.Premium Accommodations'),
            'description' => __('messages.We offer exclusive reservations for five-star hotels and luxurious private villas, ensuring your clients experience the highest level of comfort, elegance, and world-class amenities. Each hotel is carefully selected to meet the most discerning standards, providing a refined and unforgettable stay.'),
        ],
        [
            'icon' => 'fas fa-car-side',
            'title' => __('messages.Luxury Transportation'),
            'description' => __('messages.Our luxury transportation services include a fleet of executive vehicles and private transport options, from high-end cars to private chauffeurs. We guarantee smooth, comfortable, and secure travel experiences tailored to the specific needs of your clients, ensuring they arrive in style and convenience.'),
        ],
        [
            'icon' => 'fas fa-map-marked-alt',
            'title' => __('messages.Customized Tour Packages'),
            'description' => __('messages.Bali Kami Tour specializes in creating personalized tour packages that offer exclusive, tailored experiences. Whether your clients are seeking cultural excursions, adventure activities, or relaxing retreats, we design bespoke itineraries that reflect the uniqueness of each destination, ensuring memorable, one of a kind experiences.'),
        ],
    ];

    $agentBenefits = [
        [
            'icon' => 'fas fa-bolt',
            'title' => __('messages.Direct and Efficient Access to Premium Services'),
            'description' => __('messages.Our partner platform offers instant access to premium services with real time availability and pricing no intermediaries, no delays. From 5 star hotels to private villas and luxury transport, every option is quality verified and easy to book. The intuitive system simplifies operations, enabling fast, accurate bookings while reducing admin tasks. With Bali Kami Tour, partners stay efficient, responsive, and ready to deliver top tier travel experiences.'),
        ],
        [
            'icon' => 'fas fa-hand-holding-usd',
            'title' => __('messages.Competitive Pricing and Best Value'),
            'description' => __('messages.Bali Kami Tour offers exclusive partner rates on premium services, secured through strong collaborations with trusted hotels and transport providers. Our platform features transparent, real-time pricing with no hidden fees, allowing partners to confidently create competitive packages. Dynamic pricing tools reflect seasonal trends, helping partners access the best value at any time. We combine affordability and quality to help our partners maximize profits and exceed client expectations.'),
        ],
        [
            'icon' => 'fas fa-headset',
            'title' => __('messages.Professional and Reliable Support'),
            'description' => __('messages.Bali Kami Tour provides responsive and professional support to ensure seamless partner operations. Our experienced team is ready to assist with bookings, platform navigation, and tailored solutions for any travel needs. Accessible via chat, email, or phone, we guarantee timely, accurate assistance especially during high demand periods, such as event season or exclusive tour launches. With us, partners receive more than just service they gain a committed travel ally.'),
        ],
    ];

    $whyUs = [
        [
            'icon' => 'fas fa-chart-line',
            'title' => __('messages.Competitive Pricing'),
            'description' => __('messages.We offer highly competitive rates without compromising on quality, ensuring the best value for premium services.'),
        ],
        [
            'icon' => 'fas fa-user-check',
            'title' => __('messages.User Friendly System'),
            'description' => __('messages.Our online platform is designed for ease of use, providing partners with a seamless, efficient booking experience.'),
        ],
        [
            'icon' => 'fas fa-calendar-check',
            'title' => __('messages.Direct Service Booking'),
            'description' => __('messages.Partners can book services directly, eliminating intermediaries, saving time, and ensuring convenience.'),
        ],
        [
            'icon' => 'fas fa-database',
            'title' => __('messages.Comprehensive Service Data'),
            'description' => __('messages.All necessary information regarding hotels, transportation, and tour packages is readily available within the system, enabling partners to make informed decisions'),
        ],
        [
            'icon' => 'fas fa-user-tie',
            'title' => __('messages.Experienced Guides and Drivers'),
            'description' => __('messages.We provide highly trained and experienced guides and drivers, ensuring smooth and enjoyable journeys for guests.'),
        ],
        [
            'icon' => 'fas fa-life-ring',
            'title' => __('messages.24/7 Customer Support'),
            'description' => __('messages.Our dedicated support team is available around the clock, offering swift and responsive assistance to both partners and clients.'),
        ],
    ];
@endphp

@section('content')
    <main class="frontend-page-shell about-page">
        <section class="container-fluid frontend-page-topband about-topband py-5">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['label' => __('messages.About Us')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="frontend-page-intro about-hero">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">{{ $companyName }}</h1>
                        <p class="frontend-page-intro__text">
                            {{ $companyTagline }}
                        </p>
                    </div>

                    <div class="frontend-page-summary about-hero__summary">
                        @foreach ($heroStats as $stat)
                            <div class="frontend-page-summary__item">
                                <span>{{ $stat['label'] }}</span>
                                <strong>{!! $stat['value'] !!}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section about-section--story">
            <div class="container">
                <div class="about-story">
                    <div class="about-story__media">
                        <img src="{{ asset('landing-page/img/bali-kami-office.avif') }}" alt="@lang('messages.Bali Kami Tour office and partner support team')">
                    </div>
                    <div class="about-story__content">
                        <span class="about-section__eyebrow">@lang('messages.Who We Are')</span>
                        <h2>{{ $companyLegalName }}</h2>
                        <p>{!! $companyDescription !!}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <div class="about-section-heading">
                    <span class="about-section__eyebrow">@lang('messages.Our Services')</span>
                    <h2>@lang('messages.International standard booking support for professional travel partners with clear service access and trusted local expertise.')</h2>
                </div>

                <div class="about-pillar-grid">
                    @foreach ($servicePillars as $pillar)
                        <article class="about-pillar-card">
                            <div class="about-card-icon">
                                <i class="{{ $pillar['icon'] }}" aria-hidden="true"></i>
                            </div>
                            <h3>{{ $pillar['title'] }}</h3>
                            <p>{{ $pillar['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="about-section about-section--platform">
            <div class="container">
                <div class="about-platform">
                    <div class="about-platform__content">
                        <span class="about-section__eyebrow">@lang('messages.About Our Platform')</span>
                        <h2>@lang('messages.Platform built for faster partner operations')</h2>
                        <p>@lang('messages.At Bali Kami Tour, we are committed to redefining travel experiences through innovation, reliability, and personalized service. As part of this commitment, we’ve developed a sophisticated online platform at online.balikamitour.com, designed exclusively for our official partners. This dedicated system offers direct access to our premium travel services, ensuring partners can operate with greater autonomy, efficiency, and confidence. Through our secure partner login, users can seamlessly explore and book a curated selection of high-end hotels, ranging from 5-star hotels to luxury private villas. The platform also offers a comprehensive suite of transportation options, including VIP vehicles and professional chauffeurs, tailored to meet the standards of discerning travelers. Furthermore, partners have the flexibility to create personalized tour packages, aligning with specific client needs and expectations. With a focus on quality, transparency, and ease of use, our online system empowers partners to deliver world-class travel solutions under the trusted banner of Bali Kami Tour.')</p>
                    </div>
                    <div class="about-platform__visual">
                        <img src="{{ asset('landing-page/img/business_partner.png') }}" alt="@lang('messages.Bali Kami Tour partner platform access')">
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <div class="about-section-heading about-section-heading--center">
                    <span class="about-section__eyebrow">@lang('messages.Benefits for Our Agents')</span>
                    <h2>@lang('messages.Experience Unmatched Value and Effortless Luxury Travel Solutions.')</h2>
                </div>

                <div class="about-benefit-stack">
                    @foreach ($agentBenefits as $benefit)
                        <article class="about-benefit-row">
                            <div class="about-card-icon">
                                <i class="{{ $benefit['icon'] }}" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>{{ $benefit['title'] }}</h3>
                                <p>{{ $benefit['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="about-section about-section--why">
            <div class="container">
                <div class="about-section-heading about-section-heading--center">
                    <span class="about-section__eyebrow">@lang('messages.Why Partner With Us!')</span>
                    <h2>@lang('messages.Empowering Partners to Deliver Exceptional Travel Experiences.')</h2>
                </div>

                <div class="about-why-grid">
                    @foreach ($whyUs as $item)
                        <article class="about-why-card">
                            <div class="about-card-icon about-card-icon--small">
                                <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                            </div>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="about-section about-section--cta">
            <div class="container">
                <div class="about-cta">
                    <div>
                        <span class="about-section__eyebrow">@lang('messages.Partner Portal')</span>
                        <h2>@lang('messages.Access the booking portal below to reserve services for your clients quickly and efficiently.')</h2>
                    </div>
                    <div class="about-cta__actions">
                        <a href="{{ route('login') }}" class="btn btn-primary">@lang('messages.Book as Partner')</a>
                        <a href="{{ route('contact-us') }}" class="about-hero__secondary">@lang('messages.Contact Us')</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
