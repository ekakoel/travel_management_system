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
                                    alt="{{ $slider->localized_title }}"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                >
                            </picture>

                            @if(
                                $slider->localized_title ||
                                $slider->localized_description ||
                                ($slider->button_url && $slider->localized_button_text)
                            )
                                <div class="home-slider__content">

                                    @if($slider->localized_title)
                                        <h1 class="home-slider__title">
                                            {{ $slider->localized_title }}
                                        </h1>
                                    @endif

                                    @if($slider->localized_description)
                                        <p class="home-slider__description">
                                            {!! $slider->localized_description !!}
                                        </p>
                                    @endif

                                    @if($slider->button_url && $slider->localized_button_text)
                                        <a
                                            href="{{ $slider->button_url }}"
                                            class="btn btn-primary"
                                        >
                                            {{ $slider->localized_button_text }}
                                        </a>
                                    @endif

                                </div>
                            @endif

                        </article>
                    </div>
                @endforeach

            </div>

            {{-- <div class="swiper-pagination"></div>

            <button
                type="button"
                class="swiper-button-prev"
                aria-label="{{ __('messages.Previous') }}">
            </button>

            <button
                type="button"
                class="swiper-button-next"
                aria-label="{{ __('messages.Next') }}">
            </button> --}}
        </div>
    </section>
@endif