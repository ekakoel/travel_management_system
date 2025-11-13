@extends('layouts.app')
@section('title', 'Contact Us')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid hero-header bg-light py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 animated fadeIn">
                    <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.Contact Us')</h1>
                    <p class="text-primary fs-5 mb-4 animated slideInDown">@lang('messages.We’d Love to Hear From You')</p>
                </div>
                <div class="col-lg-6 animated fadeIn">
                    <img class="img-fluid animated pulse infinite img-heading" style="animation-duration: 3s; width:600px" src="landing-page/img/contact-us.png" alt="Contact Us">
                </div>
            </div>
        </div>
    </div>
    <!-- Contact Start -->
    <div class="container-xxl my-5">
        <div class="container wow fadeInUp p-h-5 my-5" data-wow-delay="0.1s">
            <iframe class="map-frame w-100 mb-n2" style="height: 450px; margin-right:3rem;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.821050968989!2d115.22173570000001!3d-8.708537300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24112fd4d0b17%3A0x6c3c0731cda9e79!2sBali%20Kami%20Tour%20and%20Wedding!5e0!3m2!1sen!2sid!4v1745998344458!5m2!1sen!2sid" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
        </div>
        <div class="container">
            <div class="row g-5 mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <div class="col-lg-6">
                    <h1 class="display-6">@lang('messages.Reach Us Directly')</h1>
                    <p class="text-primary fs-5 mb-0">@lang("messages.Whether you're interested in exploring a partnership, have questions about our services, or need assistance with an existing booking, our team is here to help. We are committed to providing timely and professional support for all your needs. Reach out to us through the contact form, email, or phone, we're ready to assist you every step of the way.")</p>
                </div>
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" placeholder="Your Name">
                                    <label for="name">@lang('messages.Your Name')</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" placeholder="Your Email">
                                    <label for="email">@lang('messages.Your Email')</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" placeholder="Subject">
                                    <label for="subject">@lang('messages.Subject')</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave a message here" id="message" style="height: 100px"></textarea>
                                    <label for="message">@lang('messages.Message')</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary py-3 px-4" type="submit">@lang('messages.Send Message')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
@endsection