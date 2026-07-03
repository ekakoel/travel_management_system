document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".frontend-loop-swiper").forEach(function (swiperElement) {
        if (swiperElement.dataset.swiperInitialized === "true") {
            return;
        }

        var baseSlideCount = Number(swiperElement.dataset.swiperBaseCount || 0);
        var slideCount = Number(swiperElement.dataset.swiperSlideCount || swiperElement.querySelectorAll(".swiper-slide").length);
        var initialSlide = Number(swiperElement.dataset.swiperInitialSlide || 0);
        var effect = swiperElement.dataset.swiperEffect || "coverflow";
        var speed = Number(swiperElement.dataset.swiperSpeed || 800);

        var swiperInstance = new Swiper(swiperElement, {
            effect: effect,
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            loop: false,
            speed: speed,
            watchOverflow: false,
            initialSlide: initialSlide,
            watchSlidesProgress: true,
            roundLengths: true,
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 200,
                modifier: 1,
                slideShadows: false,
            },
            pagination: {
                el: swiperElement.querySelector(".swiper-pagination"),
                clickable: true,
            },
            navigation: {
                nextEl: swiperElement.querySelector(".swiper-button-next"),
                prevEl: swiperElement.querySelector(".swiper-button-prev"),
            },
        });

        function normalizeLoopPosition() {
            if (baseSlideCount <= 0 || slideCount <= baseSlideCount * 2) {
                return;
            }

            var activeIndex = swiperInstance.activeIndex;
            var leadingBoundary = baseSlideCount;
            var trailingBoundary = slideCount - baseSlideCount - 1;

            if (activeIndex < leadingBoundary) {
                swiperInstance.slideTo(activeIndex + (baseSlideCount * 2), 0, false);
            } else if (activeIndex > trailingBoundary) {
                swiperInstance.slideTo(activeIndex - (baseSlideCount * 2), 0, false);
            }
        }

        swiperInstance.on("slideChange", normalizeLoopPosition);
        swiperInstance.on("transitionEnd", normalizeLoopPosition);

        normalizeLoopPosition();
        swiperElement.dataset.swiperInitialized = "true";
    });
});
