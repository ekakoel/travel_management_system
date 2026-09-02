@if($sliders->isNotEmpty())
    <section class="home-slider">
        <div class="swiper home-slider__swiper">
            <div class="swiper-wrapper">

                @foreach($sliders as $slider)
                    <div class="swiper-slide">
                        <article class="home-slider__slide">

                            <picture class="home-slider__media">
                                @if($slider->mobile_image)
                                    <source
                                        media="(max-width: 767px)"
                                        srcset="{{ asset('storage/' . $slider->mobile_image) }}"
                                    >
                                @endif

                                <img
                                    src="{{ asset('storage/' . $slider->image) }}"
                                    alt="{{ $slider->title }}"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                >
                            </picture>

                            @if($slider->title || $slider->description || ($slider->button_url && $slider->button_text))
                                <div class="home-slider__content">

                                    @if($slider->title)
                                        <h1 class="home-slider__title">
                                            {{ $slider->title }}
                                        </h1>
                                    @endif

                                    @if($slider->description)
                                        <p class="home-slider__description">
                                            {!! $slider->description !!}
                                        </p>
                                    @endif

                                    @if($slider->button_url && $slider->button_text)
                                        <a
                                            href="{{ $slider->button_url }}"
                                            class="btn btn-primary"
                                        >
                                            {{ $slider->button_text }}
                                        </a>
                                    @endif

                                </div>
                            @endif

                        </article>
                    </div>
                @endforeach

            </div>

            <div class="swiper-pagination"></div>

            <button
                type="button"
                class="swiper-button-prev"
                aria-label="Previous slide">
            </button>

            <button
                type="button"
                class="swiper-button-next"
                aria-label="Next slide">
            </button>
        </div>
    </section>
@endif