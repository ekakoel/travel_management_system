document.addEventListener('DOMContentLoaded', () => {
    const sliderElement = document.querySelector('.home-slider__swiper');

    if (!sliderElement || typeof Swiper === 'undefined') {
        return;
    }

    const slideCount = sliderElement.querySelectorAll('.swiper-slide').length;

    if (slideCount <= 1) {
        return;
    }

    new Swiper(sliderElement, {
        loop: true,
        speed: 900,

        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },

        effect: 'fade',

        fadeEffect: {
            crossFade: true,
        },

        pagination: {
            el: sliderElement.querySelector('.swiper-pagination'),
            clickable: true,
        },

        navigation: {
            nextEl: sliderElement.querySelector('.swiper-button-next'),
            prevEl: sliderElement.querySelector('.swiper-button-prev'),
        },

        keyboard: {
            enabled: true,
        },

        a11y: {
            enabled: true,
        },
    });
});