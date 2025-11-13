<section class="hero-slider">
    <div class="slide active" style="background-image: url('/landing-page/img/slide_business_partner.jpeg');">
        <div class="content-slider">
            <div class="bandage text-animate">
                <h4>online.balikamitour.com</h4>
            </div>
            <h1 class="text-animate">@lang('messages.Our Special Promotion only for exclusive partner')</h1>
            <a href="{{ route('about-us') }}"
                class="cta btn btn-primary py-3 px-4 animated slideInDown">@lang('messages.Explore More')</a>
        </div>
    </div>

    <div class="slide" style="background-image: url('/landing-page/img/slide_business_partner_2.jpeg');">
        <div class="content-slider">
            <div class="bandage text-animate">
                <h4>online.balikamitour.com</h4>
            </div>
            <h1 class="text-animate">@lang('messages.Our Special Promotion only for exclusive partner')</h1>
            <a href="{{ route('about-us') }}"
                class="cta btn btn-primary py-3 px-4 animated slideInDown">@lang('messages.Explore More')</a>
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
