    // document.addEventListener("submit", function(e) {
    //     const form = e.target.closest("form");
    //     if (!form) return;
    //     let submitBtn = form.querySelector("[type=submit]");
    //     if (!submitBtn && form.id) {
    //         submitBtn = document.querySelector(`[type=submit][form="${form.id}"]`);
    //     }

    //     if (submitBtn) {
    //         submitBtn.disabled = true;
    //         const originalText = submitBtn.innerHTML;
    //         submitBtn.innerHTML = `
    //             <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    //         `;
    //         form.addEventListener("ajaxError", function() {
    //             submitBtn.disabled = false;
    //             submitBtn.innerHTML = originalText;
    //         });
    //     }
    // }, true);
    
    document.addEventListener("submit", function(e) {
        const form = e.target.closest("form");
        if (!form) return;

        let submitBtn = form.querySelector("[type=submit]");
        if (!submitBtn && form.id) {
            submitBtn = document.querySelector(`[type=submit][form="${form.id}"]`);
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;

            // simpan original text di dataset
            submitBtn.dataset.originalText = originalText;

            // ubah ke spinner
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            `;

            // kembalikan lagi setelah 2 detik
            setTimeout(() => {
                if (submitBtn.dataset.originalText) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText;
                    delete submitBtn.dataset.originalText;
                }
            }, 3000);
        }
    }, true);


    $(document).ready(function() {
        var today = moment().toDate();
        var oneDayAfter = moment().add(1, 'days').toDate();
        var sevenDaysAfter = moment().add(7, 'days').toDate();
        var nineDaysAfter = moment().add(9, 'days').toDate();
        var defaultDate = moment().add(7, 'days').toDate();
        $('input[name="checkincout"]').daterangepicker({
            minDate: sevenDaysAfter,
            opens: 'left',
            autoApply: true,
            language: 'en',
            format: 'MM/DD/YYYY',
        });
    });

// Tampilkan tombol saat scroll ke bawah
window.onscroll = function() {
    let btn = document.getElementById("backToTopBtn");
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        btn.style.display = "block";
    } else {
        btn.style.display = "none";
    }
};

// Scroll ke atas ketika tombol diklik
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("backToTopBtn").addEventListener("click", function () {
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
});

(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();

    // NAVBAR
    const navbar = document.getElementById("mainNavbar");
    window.addEventListener("scroll", function() {
        if (window.scrollY > 0) {
            navbar.classList.add("scrolled");
            $('.sticky-top').addClass('shadow-sm').css('top', '0px');
        } else {
            navbar.classList.remove("scrolled");
            $('.sticky-top').removeClass('shadow-sm').css('top', '-100px');
        }
    });

    // Sticky Navbar
    // $(window).scroll(function () {
    //     if ($(this).scrollTop() > 300) {
    //         $('.sticky-top').addClass('shadow-sm').css('top', '0px');
    //     } else {
    //         $('.sticky-top').removeClass('shadow-sm').css('top', '-100px');
    //     }
    // });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Roadmap carousel
    $(".roadmap-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 25,
        loop: true,
        dots: false,
        nav: true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ],
        responsive: {
            0:{
                items:1
            },
            576:{
                items:2
            },
            768:{
                items:3
            },
            992:{
                items:4
            },
            1200:{
                items:5
            }
        }
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 25,
        loop: true,
        center: true,
        dots: false,
        nav: true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ],
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });
})(jQuery);

// SWIPER
var swiper = new Swiper(".mySwiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    loop: true,
    speed: 800,
    // autoplay: {
    //     delay: 5000,
    //     disableOnInteraction: false,
    //     pauseOnMouseEnter: true,
    // },
    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 200,
        modifier: 1,
        slideShadows: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});

