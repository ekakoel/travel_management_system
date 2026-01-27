<div class="card">
    @if ($hotel->promos->isNotEmpty())
        <div class="persen-promo" data-toggle="tooltip" data-placement="top" title="Promotion" aria-hidden="true">
            <img src="{{ asset('storage/icon/persen.png') }}" alt="Promo discount" loading="lazy">
        </div>
        @php
            $latestPromo = $hotel->promos->sortByDesc('created_at')->first();
            $promoImages = [
                'Hot Deal' => 'hot_deal_promo.png',
                'Best Choice' => 'best_choice_promo.png',
                'Best Price' => 'best_price_promo.png',
                'Special Offer' => 'special_offer_promo.png',
            ];
        @endphp
        @if ($latestPromo && isset($promoImages[$latestPromo->promotion_type]))
            <div class="promo-hot-deal">
                <img src="{{ asset('storage/icon/' . $promoImages[$latestPromo->promotion_type]) }}" alt="{{ $latestPromo->promotion_type }} Promotion">
            </div>
        @endif
    @endif
    <div class="image-container">
        <div class="top-lable">
            <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
            <a target="__blank" href="{{ $hotel->map }}">
                {{ $hotel->region }}
            </a>
        </div>
        <a href="{{ route('view.hotel-detail',$hotel->code) }}">
            <img src="{{ $hotel->cover?getThumbnail('/hotels/hotels-cover/' . $hotel->cover,380,200):getThumbnail('/images/default.webp',380,200) }}" class="thumbnail-image" loading="lazy">
            <div class="card-detail-title">{{ $hotel->name }}</div>
        </a>
    </div>
</div>
