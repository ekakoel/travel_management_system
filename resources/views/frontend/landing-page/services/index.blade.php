@extends('frontend.layouts.app')

@section('title', __('messages.Our Services'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/contact-page-entry.css') }}">
@endpush

@section('content')
    <main class="frontend-page-shell contact-page services-hub-page">
        <section class="container-fluid frontend-page-topband contact-topband py-5">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['label' => __('messages.Our Services')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="frontend-page-intro contact-hero">
                    <div class="frontend-page-intro__copy">
                        <span class="contact-eyebrow">@lang('messages.Service Directory')</span>
                        <h1 class="frontend-page-intro__title">@lang('messages.Services for professional travel partners')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Explore our core B2B service categories and continue to the dedicated catalog for detailed availability, rates, and booking flow.')
                        </p>
                    </div>

                    <div class="frontend-page-summary contact-hero__summary">
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Accommodations')</span>
                            <strong>@lang('messages.Hotels and villas')</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Transports')</span>
                            <strong>@lang('messages.Airport and daily rent')</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Tour Packages')</span>
                            <strong>@lang('messages.Curated journeys')</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Activities')</span>
                            <strong>@lang('messages.Experiences and excursions')</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--channels">
            <div class="container">
                <div class="contact-section-heading">
                    <span class="contact-eyebrow">@lang('messages.Select a Service')</span>
                    <h2>@lang('messages.Choose the service category that matches your client request.')</h2>
                </div>

                <div class="contact-channel-grid">
                    @foreach ($serviceCards as $serviceCard)
                        <article class="contact-channel-card services-hub-card">
                            <div class="contact-card-icon">
                                <i class="{{ $serviceCard['icon'] }}" aria-hidden="true"></i>
                            </div>
                            <span>@lang('messages.Service')</span>
                            <h3>{{ $serviceCard['title'] }}</h3>
                            <p>{{ $serviceCard['description'] }}</p>
                            <a href="{{ $serviceCard['href'] }}">@lang('messages.View Service')</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="contact-section services-preview-section">
            <div class="container">
                <div class="contact-section-heading">
                    <span class="contact-eyebrow">@lang('messages.Featured Service Preview')</span>
                    <h2>@lang('messages.Browse a quick sample of available services before opening the full catalog.')</h2>
                </div>

                @php
                    $previewSections = [
                        [
                            'title' => __('messages.Accommodations'),
                            'description' => __('messages.Curated hotels, villas, and premium stays for professional travel programs.'),
                            'items' => data_get($servicePreviews, 'accommodations', collect()),
                            'href' => route('view.hotels-service'),
                        ],
                        [
                            'title' => __('messages.Transports'),
                            'description' => __('messages.Airport shuttle and daily rent transport options for seamless guest mobility.'),
                            'items' => data_get($servicePreviews, 'transports', collect()),
                            'href' => route('view.transports-service'),
                        ],
                        [
                            'title' => __('messages.Tour Packages'),
                            'description' => __('messages.Private and curated Indonesia journeys designed for international travel agents.'),
                            'items' => data_get($servicePreviews, 'tours', collect()),
                            'href' => route('view.tour-packages-service'),
                        ],
                        [
                            'title' => __('messages.Activities'),
                            'description' => __('messages.Experiences, excursions, and add-on activity options for more complete client itineraries.'),
                            'items' => data_get($servicePreviews, 'activities', collect()),
                            'href' => route('view.activities-service'),
                        ],
                    ];
                @endphp

                <div class="services-preview-stack">
                    @foreach ($previewSections as $previewSection)
                        <section class="services-preview-block">
                            <div class="services-preview-block__header">
                                <div>
                                    <h3>{{ $previewSection['title'] }}</h3>
                                    <p>{{ $previewSection['description'] }}</p>
                                </div>
                                <a href="{{ $previewSection['href'] }}">@lang('messages.View Full Catalog')</a>
                            </div>

                            <div class="services-preview-grid">
                                @forelse ($previewSection['items'] as $previewItem)
                                    <article class="services-preview-card">
                                        <a href="{{ $previewItem['href'] }}">
                                            <img src="{{ $previewItem['image'] }}" alt="{{ $previewItem['title'] }}" loading="lazy">
                                            <span>{{ $previewItem['meta'] }}</span>
                                            <h4>{{ $previewItem['title'] }}</h4>
                                        </a>
                                    </article>
                                @empty
                                    <div class="services-preview-empty">
                                        @lang('messages.Service preview is currently unavailable. Please open the full catalog or contact our team for assistance.')
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--cta">
            <div class="container">
                <div class="contact-cta">
                    <div>
                        <span class="contact-eyebrow">@lang('messages.Need tailored support?')</span>
                        <h2>@lang('messages.Contact our team if your request requires custom routing, group handling, or multi-service coordination.')</h2>
                    </div>
                    <div class="contact-cta__actions">
                        <a href="{{ route('contact-us') }}" class="btn btn-primary">@lang('messages.Contact Us')</a>
                        <a href="{{ route('home') }}" class="contact-hero__secondary">@lang('messages.Home')</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
