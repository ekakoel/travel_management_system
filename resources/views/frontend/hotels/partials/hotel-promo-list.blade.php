@forelse ($hotels as $hotel)
    <div class="fade-up">
        @include('frontend.hotels.partials.hotel-promo-card', [
            'hotel' => $hotel,
            'promoImages' => $promoImages
            ])
    </div>
@empty
    <div class="no-data">No hotels found with active promotions!</div>
@endforelse
{{-- @foreach($hotels as $hotel)
    <div class="hotel-card mb-4">
        <div class="card shadow-sm p-3">
            <div class="row g-0 align-items-center">
                <div class="col-md-4">
                    <img src="{{ asset('storage/' . $hotel->cover) }}" alt="{{ $hotel->name }}" class="img-fluid rounded">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h4 class="card-title">{{ $hotel->name }}</h4>
                        <p class="text-muted mb-1"><i class="fa fa-map-marker"></i> {{ $hotel->region }}</p>
                        <p class="mb-2">{{ Str::limit($hotel->description, 120) }}</p>

                        @if($hotel->promos->count() > 0)
                            <div class="hotel-promos mt-2">
                                <h6 class="fw-bold text-success">Available Promotions:</h6>
                                @foreach($hotel->promos as $promo)
                                    <div class="promo-item mb-2 border-start ps-2">
                                        <strong>{{ $promo->name }}</strong><br>
                                        <small class="text-secondary">
                                            {{ __('Book Period:') }}
                                            {{ $promo->book_periode_start }} - {{ $promo->book_periode_end }}<br>
                                            {{ __('Stay Period:') }}
                                            {{ $promo->periode_start }} - {{ $promo->periode_end }}
                                        </small>
                                        @if(!empty($promo->quotes))
                                            <p class="mb-0 text-info fst-italic">"{{ $promo->quotes }}"</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small">No active promotions at the moment.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($hotels->isEmpty())
    <p class="text-center text-muted py-4">No hotels found with active promotions.</p>
@endif --}}