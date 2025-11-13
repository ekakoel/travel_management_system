<div class="card villa-item fade-up" data-region="{{ strtolower($villa->region) }}">
    <div class="image-container">
        <div class="top-lable">
            <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
            <a target="__blank" href="{{ $villa->map }}">
                {{ $villa->region }}
            </a>
        </div>
        <a href="{{ route('view.villas.show',$villa->code) }}">
            <img src="{{ $villa->cover?asset('storage/villas/covers/' . $villa->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
            <div class="card-detail-title">{{ $villa->name }}</div>
        </a>
    </div>
</div>
