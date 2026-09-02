@extends('frontend.layouts.app')

@section('title', __('messages.Contact Us'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/contact-page-entry.css') }}">
@endpush

@section('content')
    <main class="frontend-page-shell contact-page">
        <section class="container-fluid frontend-page-topband contact-topband py-5">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['label' => __('messages.Contact Us')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="frontend-page-intro contact-hero">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('messages.Let’s coordinate your next Indonesia travel request')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Connect with our team for B2B partnership support, curated service recommendations, booking assistance, and operational coordination across Indonesia.')
                        </p>
                    </div>

                    <div class="frontend-page-summary contact-hero__summary">
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Company')</span>
                            <strong>{{ data_get($contactData, 'company_name') }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Focus')</span>
                            <strong>{{ data_get($contactData, 'company_type') }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Response')</span>
                            <strong>@lang('messages.Business-day support')</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Location')</span>
                            <strong>Bali, Indonesia</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--channels">
            <div class="container">
                <div class="contact-section-heading">
                    <span class="contact-eyebrow">@lang('messages.Direct Contact Channels')</span>
                    <h2>@lang('messages.Choose the most convenient way to reach our reservation and partnership team.')</h2>
                </div>

                <div class="contact-channel-grid">
                    <article class="contact-channel-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </div>
                        <span>@lang('messages.Email')</span>
                        <h3>{{ data_get($contactData, 'email') }}</h3>
                        <p>@lang('messages.Best for partnership inquiries, quotation requests, invoice coordination, and formal documentation.')</p>
                        <a href="mailto:{{ data_get($contactData, 'email') }}">@lang('messages.Send Email')</a>
                    </article>

                    <article class="contact-channel-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        </div>
                        <span>@lang('messages.Phone')</span>
                        <h3>{{ data_get($contactData, 'phone') }}</h3>
                        <p>@lang('messages.Use phone support for urgent coordination related to active bookings or service arrangements.')</p>
                        <a href="tel:{{ data_get($contactData, 'phone_href') }}">@lang('messages.Call Office')</a>
                    </article>

                    <article class="contact-channel-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-globe" aria-hidden="true"></i>
                        </div>
                        <span>@lang('messages.Website')</span>
                        <h3>{{ data_get($contactData, 'website') }}</h3>
                        <p>@lang('messages.Access our B2B platform, explore services, and continue your reservation workflow online.')</p>
                        <a href="{{ data_get($contactData, 'website_url') }}" target="_blank" rel="noopener noreferrer">@lang('messages.Open Website')</a>
                    </article>
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--support">
            <div class="container">
                <div class="contact-support-layout">
                    <div class="contact-support-copy">
                        <span class="contact-eyebrow">@lang('messages.How We Can Help')</span>
                        <h2>@lang('messages.Professional assistance for travel agents and business partners')</h2>
                        <p>
                            @lang("messages.Whether you're interested in exploring a partnership, have questions about our services, or need assistance with an existing booking, our team is here to help. We are committed to providing timely and professional support for all your needs. Reach out to us through the contact form, email, or phone, we're ready to assist you every step of the way.")
                        </p>
                    </div>

                    <div class="contact-support-list">
                        <div class="contact-support-item">
                            <strong>@lang('messages.New Partnership')</strong>
                            <span>@lang('messages.Agent registration, company profile review, and platform access guidance.')</span>
                        </div>
                        <div class="contact-support-item">
                            <strong>@lang('messages.Reservation Support')</strong>
                            <span>@lang('messages.Assistance for accommodation, transport, tour package, activity, villa, and group requests.')</span>
                        </div>
                        <div class="contact-support-item">
                            <strong>@lang('messages.Payment Coordination')</strong>
                            <span>@lang('messages.Invoice questions, payment proof guidance, and order validation support.')</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--map">
            <div class="container">
                <div class="contact-map-card">
                    <div class="contact-map-card__info">
                        <span class="contact-eyebrow">@lang('messages.Office Location')</span>
                        <h2>@lang('messages.Visit or reference our Bali office')</h2>
                        <p>{{ data_get($contactData, 'address') }}</p>
                        <div class="contact-office-meta">
                            <span>@lang('messages.Indonesia-based destination support')</span>
                            <span>@lang('messages.B2B travel partner service desk')</span>
                        </div>
                    </div>
                    <div class="contact-map-card__frame">
                        <iframe
                            src="{{ data_get($contactData, 'map_src') }}"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                            title="@lang('messages.Bali Kami Tour office location map')"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section contact-section--cta">
            <div class="container">
                <div class="contact-cta">
                    <div>
                        <span class="contact-eyebrow">@lang('messages.Ready to collaborate?')</span>
                        <h2>@lang('messages.Send your inquiry with travel date, service type, guest count, and preferred destination for faster handling.')</h2>
                    </div>
                    <div class="contact-cta__actions">
                        <a href="mailto:{{ data_get($contactData, 'email') }}?subject=Travel%20Service%20Inquiry" class="btn btn-primary">@lang('messages.Start Inquiry')</a>
                        <a href="{{ route('services') }}" class="contact-hero__secondary">@lang('messages.Explore Services')</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
