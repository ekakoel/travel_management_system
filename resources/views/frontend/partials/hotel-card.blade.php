<div class="hotel-card">
    <div class="card-services">
        <a href="{{ route('view.hotel-detail', $hotel->code) }}">
            <div class="image-container">
                <img src="{{ getThumbnail('hotels/hotels-cover/' . $hotel->cover,380,200) }}"
                        onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';"
                        class="thumbnail-image" loading="lazy">
                <div class="service-card-title">{{ $hotel->name }}</div>
            </div>
        </a>
    </div>
</div>