 <!-- Service Start -->
 <div class="container-xxl py-5 ">
    <div class="container">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6">@lang('messages.Services')</h1>
            <p class="text-primary fs-5 mb-5">@lang('messages.Seamless Business Travel, Unmatched Professionalism.')</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item text-center">
                    <a href="{{ route('view.accommodation-service') }}">
                        <div class="icon-container">
                            <img class="img-fluid hover-effect mb-4" src="/landing-page/img/luxury_accommodation.png" alt="">
                        </div>
                        <h5 class="mb-3">@lang('messages.Premium Accommodations')</h5>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item text-center">
                    <a href="{{ route('view.transport-service') }}">
                        <div class="icon-container">
                            <img class="img-fluid hover-effect mb-4" src="/landing-page/img/luxury_transport.png" alt="">
                            <h5 class="mb-3">@lang('messages.Luxury Transportation')</h5>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item text-center">
                    <a href="{{ route('tour-package-service') }}">
                        <div class="icon-container">
                        <img class="img-fluid hover-effect mb-4" src="/landing-page/img/luxury_tour.png" alt="">
                        </div>
                        <h5 class="mb-3">@lang('messages.Customized Tour Packages')</h5>
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- Service End -->