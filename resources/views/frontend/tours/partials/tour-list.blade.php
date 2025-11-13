@forelse ($tours as $tour)
    <div class="fade-up">
        @include('frontend.tours.partials.tour-card', ['tours' => $tours, 'typeField' => $typeField, 'tourNameField' => $tourNameField])
    </div>
@empty
    <div class="no-data">@lang('messages.Hotel not found')!</div>
@endforelse
