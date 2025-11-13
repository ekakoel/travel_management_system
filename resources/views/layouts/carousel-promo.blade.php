<div class="carousel">
    <div class="owl-carousel owl-theme">
        @foreach ($promotions as $promotion)
            <div class="carousel-container">
                <div class="item">
                    <img src="{{ asset('storage/promotion/cover/' . $promotion->cover) }}" alt="{{ $promotion->name }}">
                </div>
            </div>
        @endforeach
    </div>
</div>