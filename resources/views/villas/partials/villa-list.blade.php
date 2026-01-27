@forelse ($villas as $villa)
    @include('villas.partials.villa-card', ['villa' => $villa])
@empty
    <div class="no-data">@lang('messages.Villa not found')!</div>
@endforelse
@forelse ($villas as $villa)
    @include('villas.partials.villa-card', ['villa' => $villa])
@empty
    <div class="no-data">@lang('messages.Sorry, no relevant villa information was found!')!</div>
@endforelse
