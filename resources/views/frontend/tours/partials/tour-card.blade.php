@php
    $imagePath = public_path('/storage/tours/tours-cover/'. $tour->cover);
@endphp
<div class="card">
    <div class="image-container">
        <div class="top-lable">
            <p>
                {!! $tour->type?->$typeField !!}
            </p>
        </div>
        <a href="{{ route('view.tour-detail',$tour->slug) }}">
            @if (!empty($tour->cover) && file_exists($imagePath))
                <img src="{{ getThumbnail('/tours/tours-cover/' . $tour->cover, 380, 200) }}" alt="">
            @else
                <img src="{{ getThumbnail('/images/default.webp', 380, 200) }}" alt="">
            @endif
            <div class="card-detail-title">
                {{ $tour->$tourNameField }}
            </div>
        </a>
    </div>
</div>
