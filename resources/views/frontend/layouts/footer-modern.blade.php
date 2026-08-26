<footer class="site-footer footer wow fadeIn" data-wow-delay="0.1s">
    <div class="container site-footer__container">
        <div class="site-footer__top">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="site-footer__brand">
                        <a class="site-footer__logo" href="{{ url('/') }}" aria-label="{{ data_get($footerData, 'brand.logo_aria') }}">
                            <img src="{{ data_get($footerData, 'brand.logo_url') }}" alt="{{ data_get($footerData, 'brand.name') }}">
                        </a>

                        <p class="site-footer__tagline">
                            {{ data_get($footerData, 'brand.tagline') }}
                        </p>

                        <p class="site-footer__description">
                            {!! data_get($footerData, 'brand.description') !!}
                        </p>

                        <div class="site-footer__trust-list" aria-label="{{ data_get($footerData, 'brand.trust_aria') }}">
                            @foreach (data_get($footerData, 'brand.trust_items', []) as $trustItem)
                                <span>{{ $trustItem }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">{{ data_get($footerData, 'contact.title') }}</span>

                        <ul class="site-footer__contact-list">
                            <li>
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                <span>{!! data_get($footerData, 'contact.address') !!}</span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                <a href="tel:{{ data_get($footerData, 'contact.phone_href') }}">{{ data_get($footerData, 'contact.phone') }}</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:{{ data_get($footerData, 'contact.email') }}">{{ data_get($footerData, 'contact.email') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">{{ data_get($footerData, 'newsletter.title') }}</span>

                        <p class="site-footer__newsletter-copy">
                            {{ data_get($footerData, 'newsletter.copy') }}
                        </p>

                        <div id="subscribe-alert" class="alert d-none site-footer__alert" role="alert"></div>

                        <form
                            id="subscribe-form"
                            class="site-footer__form"
                            data-subscribe-url="{{ route('subscribe.store') }}"
                            data-csrf-token="{{ csrf_token() }}"
                        >
                            @csrf
                            <label class="visually-hidden" for="footer-email">{{ data_get($footerData, 'newsletter.email_label') }}</label>
                            <input
                                class="site-footer__input"
                                type="email"
                                name="email"
                                id="footer-email"
                                placeholder="{{ data_get($footerData, 'newsletter.placeholder') }}"
                                required
                            >
                            <button type="submit" class="btn btn-primary">
                                {{ data_get($footerData, 'newsletter.button') }}
                            </button>
                        </form>

                        <div class="site-footer__social-block">
                            <span class="site-footer__social-label">{{ data_get($footerData, 'social.title') }}</span>

                            <div class="site-footer__social-links">
                                @foreach (data_get($footerData, 'social.links', []) as $socialLink)
                                    <a class="site-footer__social-link" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLink['label'] }}">
                                        <i class="{{ $socialLink['icon'] }}" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-footer__links">
            <div class="row g-4">
                @foreach (data_get($footerData, 'link_sections', []) as $linkSection)
                    <div class="col-sm-6 col-lg-3">
                        <div class="site-footer__section">
                            <span class="site-footer__eyebrow">{{ $linkSection['title'] }}</span>
                            <nav class="site-footer__nav-list" aria-label="{{ $linkSection['aria'] }}">
                                @foreach ($linkSection['links'] as $footerLink)
                                    <a
                                        href="{{ $footerLink['url'] }}"
                                        @if ($footerLink['target']) target="{{ $footerLink['target'] }}" @endif
                                        @if ($footerLink['rel']) rel="{{ $footerLink['rel'] }}" @endif
                                    >
                                        {{ $footerLink['label'] }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                @endforeach

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">{{ data_get($footerData, 'platform.title') }}</span>
                        <p class="site-footer__small-copy">
                            {{ data_get($footerData, 'platform.copy') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copyright">
                &copy; {{ data_get($footerData, 'copyright.year') }}
                <a href="{{ data_get($footerData, 'copyright.website_url') }}">{{ data_get($footerData, 'copyright.website_label') }}</a>.
                {{ data_get($footerData, 'copyright.suffix') }}
            </p>
        </div>
    </div>
</footer>
