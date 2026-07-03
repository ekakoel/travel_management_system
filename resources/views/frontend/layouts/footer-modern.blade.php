@push('scripts')
    @once
        <script src="{{ asset('frontend/js/components/frontend-footer-subscribe.js') }}?v={{ filemtime(public_path('frontend/js/components/frontend-footer-subscribe.js')) }}"></script>
    @endonce
@endpush

<footer class="site-footer footer wow fadeIn" data-wow-delay="0.1s">
    <div class="container site-footer__container">
        <div class="site-footer__top">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="site-footer__brand">
                        <a class="site-footer__logo" href="{{ url('/') }}" aria-label="{{ __('home.footer.logo_aria') }}">
                            <img src="{{ asset('storage/logo/' . config('app.logo_img_white')) }}" alt="Logo Bali Kami Tour">
                        </a>

                        <p class="site-footer__tagline">
                            {{ __('home.footer.tagline') }}
                        </p>

                        <p class="site-footer__description">
                            {{ __('home.footer.description') }}
                        </p>

                        <div class="site-footer__trust-list" aria-label="{{ __('home.footer.highlights_aria') }}">
                            <span>{{ __('home.footer.highlights.worldwide_agents') }}</span>
                            <span>{{ __('home.footer.highlights.indonesia_supply') }}</span>
                            <span>{{ __('home.footer.highlights.global_access') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Get In Touch')</span>
                        <h2 class="site-footer__title">@lang('messages.Get In Touch')</h2>

                        <ul class="site-footer__contact-list">
                            <li>
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                <span>{{ __('home.footer.address') }}</span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                <a href="tel:+62361710661">(+62 361) 710661 / 710663 / 710664 / 723061</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:e-admin@balikamitour.com">e-admin@balikamitour.com</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Newsletter')</span>
                        <h2 class="site-footer__title">@lang('messages.Newsletter')</h2>

                        <p class="site-footer__newsletter-copy">
                            @lang('messages.Stay informed with partner updates, curated offers, and service announcements.')
                        </p>

                        <div id="subscribe-alert" class="alert d-none site-footer__alert" role="alert"></div>

                        <form
                            id="subscribe-form"
                            class="site-footer__form"
                            data-subscribe-url="{{ route('subscribe.store') }}"
                            data-csrf-token="{{ csrf_token() }}"
                        >
                            @csrf
                            <label class="visually-hidden" for="footer-email">Email</label>
                            <input
                                class="site-footer__input"
                                type="email"
                                name="email"
                                id="footer-email"
                                placeholder="@lang('messages.Enter your email')"
                                required
                            >
                            <button type="submit" class="site-footer__submit">
                                @lang('messages.Subscribe')
                            </button>
                        </form>

                        <div class="site-footer__social-block">
                            <span class="site-footer__social-label">@lang('messages.Follow Us')</span>

                            <div class="site-footer__social-links">
                                <a class="site-footer__social-link" href="https://www.facebook.com/BALIKAMITOUR/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                </a>
                                <a class="site-footer__social-link" href="https://www.instagram.com/balikamitour" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                </a>
                                <a class="site-footer__social-link" href="https://www.youtube.com/@balikamichannel" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                    <i class="fab fa-youtube" aria-hidden="true"></i>
                                </a>
                                <a class="site-footer__social-link" href="https://id.linkedin.com/company/bali-kami-group" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-footer__links">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Our Services')</span>
                        <h2 class="site-footer__title">@lang('messages.Our Services')</h2>
                        <nav class="site-footer__nav-list" aria-label="Footer services">
                            <a href="{{ route('view.accommodation-service') }}">@lang('messages.Accommodations')</a>
                            <a href="{{ route('view.transport-service') }}">@lang('messages.Transports')</a>
                            <a href="{{ route('tour-package-service') }}">@lang('messages.Tour Packages')</a>
                        </nav>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Quick Links')</span>
                        <h2 class="site-footer__title">@lang('messages.Quick Links')</h2>
                        <nav class="site-footer__nav-list" aria-label="Footer quick links">
                            <a href="{{ route('about-us') }}">@lang('messages.About Us')</a>
                            <a href="{{ route('contact-us') }}">@lang('messages.Contact Us')</a>
                            <a href="{{ route('services') }}">@lang('messages.Our Services')</a>
                        </nav>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Policies')</span>
                        <h2 class="site-footer__title">@lang('messages.Policies')</h2>
                        <nav class="site-footer__nav-list" aria-label="Footer policies">
                            <a href="{{ route('terms-and-conditions') }}">@lang('messages.Terms & Conditions')</a>
                            <a href="{{ route('privacy-policy') }}">@lang('messages.Privacy Policy')</a>
                        </nav>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="site-footer__section">
                        <span class="site-footer__eyebrow">@lang('messages.Platform')</span>
                        <h2 class="site-footer__title">@lang('messages.Platform')</h2>
                        <p class="site-footer__small-copy">
                            {{ __('home.footer.platform_copy') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copyright">
                &copy; {{ date('Y') }}
                <a href="{{ url('/') }}">online.balikamitour.com</a>.
                @lang('messages.All Right Reserved.')
            </p>
        </div>
    </div>
</footer>
