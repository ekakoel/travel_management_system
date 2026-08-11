<section class="availability-section availability-section--related frontend-surface-card">
    <div class="availability-section__header">
        <div>
            <div class="availability-section__eyebrow">@lang('messages.Hotels Around') {{ $hotel->region }}</div>
            <h2 class="availability-section__title">@lang('messages.Nearby Hotels')</h2>
        </div>
        <div class="availability-section__range">
            {{ count($nearhotels) }} @lang('messages.options')
        </div>
    </div>

    <div class="availability-related-grid">
        @foreach ($nearhotels as $nearHotel)
            <article class="availability-related-card">
                <a class="availability-related-card__link" href="{{ route('view.hotel-detail', $nearHotel->code) }}">
                    <div class="availability-related-card__media">
                        <img
                            src="{{ $nearHotel->cover ? getThumbnail('/hotels/hotels-cover/' . $nearHotel->cover, 380, 240) : asset('storage/images/default.webp') }}"
                            alt="{{ $nearHotel->name }}"
                            loading="lazy"
                            decoding="async"
                        >

                        <div class="availability-related-card__badges">
                            <span class="availability-related-card__badge">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                {{ $nearHotel->region }}
                            </span>

                            @if ($nearHotel->promos->isNotEmpty())
                                <span class="availability-related-card__badge availability-related-card__badge--promo">
                                    <i class="fa fa-bolt" aria-hidden="true"></i>
                                    @lang('messages.Promotion')
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="availability-related-card__body">
                        <h3 class="availability-related-card__title">{{ $nearHotel->name }}</h3>
                        <p class="availability-related-card__meta">
                            @lang('messages.Similar stays in this area')
                        </p>
                        <span class="availability-related-card__action">
                            @lang('messages.View Details')
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
</section>
