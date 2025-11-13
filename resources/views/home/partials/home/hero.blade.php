<!--<div class="container-fluid hero-header bg-light py-5">-->
<!--    <div class="container py-5">-->
<!--        <div class="row g-5 align-items-center">-->
<!--            <div class="col-lg-6">-->
<!--                <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.Your B2B Partner for Premium Travel Experiences')</h1>-->
<!--                <p class="animated slideInDown">@lang('messages.Bali Kami Tour is your trusted B2B partner for delivering luxury travel experiences, offering exclusive five star accommodations, premium executive transportation, and personalized tour services tailored to meet the needs of discerning corporate and VIP clients.')</p>-->
<!--                <a href="{{ route('about-us') }}" class="btn btn-primary py-3 px-4 animated slideInDown">@lang('messages.Explore More')</a>-->
<!--            </div>-->
<!--            <div class="col-lg-6 animated fadeIn">-->
<!--                <img class="img-fluid animated pulse infinite" style="animation-duration: 3s;" src="landing-page/img/hero-3.png"-->
<!--                    alt="Bali Kami Tour" />-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<section class="hero-slider">
    <div class="slide active" style="background-image: url('/landing-page/img/slide_business_partner.jpeg');">
      <div class="content-slider">
        <div class="bandage text-animate">
          <h4>online.balikamitour.com</h4>
        </div>
        <h1 class="text-animate">@lang('messages.Our Special Promotion only for exclusive partner')</h1>
        <a href="{{ route('about-us') }}" class="cta btn btn-primary py-3 px-4 animated slideInDown">@lang('messages.Explore More')</a>
      </div>
    </div>

    <div class="slide" style="background-image: url('/landing-page/img/slide_business_partner_2.jpeg');">
      <div class="content-slider">
        <div class="bandage text-animate">
          <h4>online.balikamitour.com</h4>
        </div>
        <h1 class="text-animate">@lang('messages.Our Special Promotion only for exclusive partner')</h1>
        <a href="{{ route('about-us') }}" class="cta btn btn-primary py-3 px-4 animated slideInDown">@lang('messages.Explore More')</a>
      </div>
    </div>

    <div class="slider-nav">
      <span class="dot active"></span>
      <span class="dot"></span>
    </div>
  </section>

  <script>
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let index = 0;

    function showSlide(i) {
      slides.forEach(slide => slide.classList.remove('active'));
      dots.forEach(dot => dot.classList.remove('active'));
      slides[i].classList.add('active');
      dots[i].classList.add('active');
    }

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        index = i;
        showSlide(index);
      });
    });

    setInterval(() => {
      index = (index + 1) % slides.length;
      showSlide(index);
    }, 12000); // slide setiap 8 detik
  </script>