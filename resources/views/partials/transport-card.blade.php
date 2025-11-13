
<div class="card">
    <div class="image-container">
        <div class="top-lable">
            <p><i class="icon-copy ion-ios-person"></i>{{ $transport->capacity . ' ' . __('messages.Seat') }}</p>
        </div>
        <a href="{{ route('view.transport-detail',$transport->code) }}">
            <img src="{{ $transport->cover?getThumbnail('/transports/transports-cover/' . $transport->cover,320,200):getThumbnail('images/default.webp',380,200) }}" class="thumbnail-image" loading="lazy">
            <div class="card-detail-title">{{ $transport->name }}</div>
        </a>
    </div>
</div>
