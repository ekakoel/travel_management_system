<!-- Footer Start -->
<div class="container-fluid bg-light footer pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6">
                <img class="logo-footer" src="{{ asset('storage/logo/'.config('app.logo_img_color')) }}" alt="Logo Bali Kami Tour"><br>
                <span>@lang('messages.Bali Kami Tour is a Bali based B2B travel company specializing in luxury travel services across Indonesia. We offer premium accommodation at five star hotels and exclusive villas, luxury transportation including executive cars and private helicopter charters, as well as bespoke private tour packages designed to meet the highest standards.')</span>
            </div>
            <div class="col-md-6">
                <h5 class="mb-4">@lang('messages.Newsletter')</h5>
                <p>@lang('messages.Don’t miss exclusive deals & updates, subscribe now and stay ahead in luxury travel!')</p>
                <div id="subscribe-alert" class="alert d-none" role="alert"></div>
                <div class="position-relative">
                    <form id="subscribe-form">
                        @csrf
                        <input class="form-control w-100 py-3 ps-4 pe-5" type="email" name="email" id="email" placeholder="Enter your email" required/>
                        <button type="submit"
                            class="btn btn-primary py-2 px-3 position-absolute top-0 end-0 mt-2 me-2">@lang('messages.Subscribe')</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">@lang('messages.Get In Touch')</h5>
                <p><i class="fa fa-map-marker-alt me-3"></i>Jl. Raya Sesetan Gg. Ikan Jangki 617e, Denpasar City, Bali 80222</p>
                <p><i class="fa fa-phone-alt me-3"></i>(+62361) 710661 / 710663 / 710664 / 723061</p>
                <p><i class="fa fa-envelope me-3"></i>e-admin@balikamitour.com</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">@lang('messages.Our Services')</h5>
                <a class="btn btn-link" href="{{ route('view.accommodation-service') }}">@lang('messages.Accommodations')</a>
                <a class="btn btn-link" href="{{ route('view.transport-service') }}">@lang('messages.Transports')</a>
                <a class="btn btn-link" href="{{ route('tour-package-service') }}">@lang('messages.Tour Packages')</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">@lang('messages.Quick Links')</h5>
                <a class="btn btn-link" href="{{ route('about-us') }}">@lang('messages.About Us')</a>
                <a class="btn btn-link" href="{{ route('contact-us') }}">@lang('messages.Contact Us')</a>
                <a class="btn btn-link" href="{{ route('services') }}">@lang('messages.Our Services')</a>
                <a class="btn btn-link" href="{{ route('terms-and-conditions') }}">@lang('messages.Terms & Conditions')</a>
                <a class="btn btn-link" href="{{ route('privacy-policy') }}">@lang('messages.Privacy Policy')</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">@lang('messages.Follow Us')</h5>
                <div class="d-flex">
                    <a class="btn btn-square rounded-circle me-1" href="https://www.facebook.com/BALIKAMITOUR/"><i
                        class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-square rounded-circle me-1" href="https://www.instagram.com/balikamitour"><i
                            class="fab fa-instagram"></i></a>
                    <a class="btn btn-square rounded-circle me-1" href="https://www.youtube.com/@balikamichannel"><i
                            class="fab fa-youtube"></i></a>
                    <a class="btn btn-square rounded-circle me-1" href="https://id.linkedin.com/company/bali-kami-group"><i
                            class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mb-3 mb-md-0">
                    &copy; <a href="#">online.balikamitour.com</a>, @lang('messages.All Right Reserved.')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
<script>
    document.getElementById('subscribe-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const alertBox = document.getElementById('subscribe-alert');

        fetch("{{ route('subscribe.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            alertBox.classList.remove('d-none', 'alert-danger');
            alertBox.classList.add('alert-success');
            alertBox.textContent = data.message;
            document.getElementById('subscribe-form').reset();

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertBox.classList.add('fade-out');

                // Tunggu animasi selesai (500ms), baru sembunyikan elemen
                setTimeout(() => {
                    alertBox.classList.add('d-none');
                    alertBox.classList.remove('fade-out', 'alert-success', 'alert-danger');
                }, 500); // harus sama dengan durasi di CSS
            }, 5000);
        })
        .catch(async error => {
            const errData = await error.response?.json?.() ?? {};
            const message = errData?.errors?.email?.[0] ?? 'An error occurred.';
            alertBox.classList.remove('d-none', 'alert-success');
            alertBox.classList.add('alert-danger');
            alertBox.textContent = message;

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 5000);
        });
    });

</script>
    