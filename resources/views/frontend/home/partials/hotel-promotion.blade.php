 <!-- Hotel Promotion -->
 <div class="container-fluid py-5 my-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h1 class="display-6">@lang('messages.Exclusive Hotel Promotions')</h1>
            <p class="text-primary fs-5 mb-3">@lang('messages.Unlock Special Hotel Offers and Maximize Your Profit with Bali Kami Tour.')</p>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper fadeInUp" data-wow-delay="0.1s">
                @forelse($promos as $promo)
                    <div class="swiper-slide">
                        <a href="{{ route('view.hotel-detail',$promo->hotels?->code) }}">
                            <div class="slide-content">
                                <img src="{{ getThumbnail('hotels/hotels-cover/' . $promo->hotels->cover,600,900) }}" alt="{{ $promo->hotels->name." Promotion" }}">
                                <div class="slide-info">
                                    <h3 class="slide-name">{{ $promo->hotels->name }}</h3>
                                    <p class="slide-promo">{{ $promo->promotion_type }}</p>
                                    <span class="slide-dates">{{ dateFormat($promo->periode_start) }} - {{ dateFormat($promo->periode_end) }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p>@lang('messages.No promotions available.')</p>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Navigator -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>