@extends('layouts.app')
@section('title', 'About Us')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid hero-header bg-light">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 animated fadeIn">
                    <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.About Us')</h1>
                    <p class="text-primary fs-5 mb-4 animated slideInDown">@lang('messages.Discover who we are, what we stand for, and how we help you grow in the luxury travel market.')</p>
                </div>
                <div class="col-lg-6 animated fadeIn">
                    <img class="img-fluid animated pulse infinite img-heading" style="animation-duration: 3s; width:600px" src="landing-page/img/main-bg.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <div class="container-fluid py-5">
        <div class="container my-5">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <img class="img-fluid post-card-img" src="landing-page/img/bali-kami-office.avif" alt="">
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="h-100">
                        <h1 class="display-6">@lang('messages.Who We Are')</h1>
                        <p>
                            @lang('messages.Bali Kami Tour is a Bali based B2B travel company dedicated to delivering high end travel experiences across Indonesia. With a strong foundation in the luxury tourism sector, we specialize in providing exceptional services tailored for discerning clients and professional travel partners. Our offerings include premium accommodations at world class five star hotels and private, handpicked villas, ensuring comfort, privacy, and elegance. We also provide a full suite of luxury transportation options from executive class vehicles and VIP airport transfers to private helicopter charters for a truly elevated journey. In addition, our bespoke private tour packages are curated with precision, combining cultural richness, natural beauty, and personalized attention to meet the highest international standards. Our commitment to quality, reliability, and seamless service makes us a trusted partner for travel agents, tour operators, and agencies seeking superior travel solutions in Indonesia. At Bali Kami Tour, we don’t just arrange travel we craft unforgettable experiences.')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About Start -->
    <div class="container-fluid bg-light py-5">
        <div class="container my-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-12 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="h-100">
                        <h1 class="display-6">@lang('messages.About Aur Platform')</h1>
                        <p>
                            @lang('messages.At Bali Kami Tour, we are committed to redefining travel experiences through innovation, reliability, and personalized service. As part of this commitment, we’ve developed a sophisticated online platform at online.balikamitour.com, designed exclusively for our official partners. This dedicated system offers direct access to our premium travel services, ensuring partners can operate with greater autonomy, efficiency, and confidence. Through our secure partner login, users can seamlessly explore and book a curated selection of high-end accommodations, ranging from 5-star hotels to luxury private villas. The platform also offers a comprehensive suite of transportation options, including VIP vehicles and professional chauffeurs, tailored to meet the standards of discerning travelers. Furthermore, partners have the flexibility to create personalized tour packages, aligning with specific client needs and expectations. With a focus on quality, transparency, and ease of use, our online system empowers partners to deliver world-class travel solutions under the trusted banner of Bali Kami Tour.')
                        </p>
                        <h4>@lang('messages.Benefits for Our Agents'):</h4>
                        <p>
                            <b>
                                @lang('messages.Direct and Efficient Access to Premium Services')
                            </b>
                        </p>
                        <p>
                            @lang('messages.Our partner platform offers instant access to premium services with real time availability and pricing no intermediaries, no delays. From 5 star hotels to private villas and luxury transport, every option is quality verified and easy to book. The intuitive system simplifies operations, enabling fast, accurate bookings while reducing admin tasks. With Bali Kami Tour, partners stay efficient, responsive, and ready to deliver top tier travel experiences.')
                        </p>
                        <p>
                            <b>
                                @lang('messages.Competitive Pricing and Best Value')
                            </b>
                        </p>
                        <p>
                            @lang('messages.Bali Kami Tour offers exclusive partner rates on premium services, secured through strong collaborations with trusted hotels and transport providers. Our platform features transparent, real-time pricing with no hidden fees, allowing partners to confidently create competitive packages. Dynamic pricing tools reflect seasonal trends, helping partners access the best value at any time. We combine affordability and quality to help our partners maximize profits and exceed client expectations.')
                        </p>
                        <p>
                            <b>
                                @lang('messages.Professional and Reliable Support')
                            </b>
                        </p>
                        <p>
                            @lang('messages.Bali Kami Tour provides responsive and professional support to ensure seamless partner operations. Our experienced team is ready to assist with bookings, platform navigation, and tailored solutions for any travel needs. Accessible via chat, email, or phone, we guarantee timely, accurate assistance especially during high demand periods, such as event season or exclusive tour launches. With us, partners receive more than just service they gain a committed travel ally.')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-5">
        <div class="container my-5">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-6">@lang('messages.Why Partner With Us!')</h1>
                <p class="text-primary fs-5 mb-5">@lang('messages.Experience Unmatched Value and Effortless Luxury Travel Solutions.')</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.Competitive Pricing')</h5>
                            <span>@lang('messages.We offer highly competitive rates without compromising on quality, ensuring the best value for premium services.')</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.User Friendly System')</h5>
                            <span>@lang('messages.Our online platform is designed for ease of use, providing partners with a seamless, efficient booking experience.')</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.Direct Service Booking')</h5>
                            <span>@lang('messages.Partners can book services directly, eliminating intermediaries, saving time, and ensuring convenience.')</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.Comprehensive Service Data')</h5>
                            <span>@lang('messages.All necessary information regarding accommodations, transportation, and tour packages is readily available within the system, enabling partners to make informed decisions')</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.Experienced Guides and Drivers')</h5>
                            <span>@lang('messages.We provide highly trained and experienced guides and drivers, ensuring smooth and enjoyable journeys for guests.')</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-start">
                        <div class="content-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-3">@lang('messages.24/7 Customer Support')</h5>
                            <span>@lang('messages.Our dedicated support team is available around the clock, offering swift and responsive assistance to both partners and clients.')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
@endsection