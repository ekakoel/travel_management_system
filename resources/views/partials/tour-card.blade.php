
<div class="card">
    <div class="image-container">
        <div class="top-lable">
            <p>
                <i class="icon-copy dw dw-wall-clock1"></i> 
                {{ $tour->duration_days . 'D' }}
                @if ($tour->duration_nights > 0)
                    /{{ $tour->duration_nights . 'N' }}
                @endif
            </p>
        </div>
        <a href="{{ route('view.tour-detail',$tour->slug) }}">
            <img src="{{ $tour->cover?getThumbnail('/tours/tours-cover/' . $tour->cover,320,200):getThumbnail('images/default.webp',380,200) }}" class="thumbnail-image" loading="lazy">
            <div class="card-detail-title">{{ $tour->name }}</div>
        </a>
    </div>
</div>
