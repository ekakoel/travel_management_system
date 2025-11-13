@forelse ($hotels as $hotel)
    <div class="fade-up">
        @include('partials.hotel-card', ['hotel' => $hotel])
    </div>
@empty
    <div class="no-data">@lang('messages.Hotel not found')!</div>
@endforelse
