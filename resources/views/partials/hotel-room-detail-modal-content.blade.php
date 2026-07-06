@php
    $roomDetail = $card['room_detail'] ?? [];
    $facts = $roomDetail['facts'] ?? [];
    $amenities = trim((string) ($roomDetail['amenities'] ?? ''));
    $include = trim((string) ($roomDetail['include'] ?? ''));
    $additionalInfo = trim((string) ($roomDetail['additional_info'] ?? ''));
@endphp

<div class="availability-room-modal">
    <div class="availability-room-modal__image">
        <img
            src="{{ $roomImageFull }}"
            class="availability-progressive-image"
            alt="{{ $card['room_name'] }}"
            loading="lazy"
            decoding="async"
            onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';"
        >
    </div>

    @if (count($facts) > 0)
        <div class="availability-room-modal__facts">
            @foreach ($facts as $fact)
                <div class="availability-room-modal__fact">
                    <span class="availability-room-modal__fact-icon" aria-hidden="true">
                        <i class="fa {{ $fact['icon'] }}"></i>
                    </span>
                    <span>
                        <small>{{ $fact['label'] }}</small>
                        <strong>{{ $fact['value'] }}</strong>
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    @if ($amenities !== '')
        <section class="availability-room-modal__section">
            <h4>@lang('messages.Amenities')</h4>
            <div>{!! $amenities !!}</div>
        </section>
    @endif

    @if ($include !== '')
        <section class="availability-room-modal__section">
            <h4>@lang('messages.Room inclusions and notes')</h4>
            <div>{!! $include !!}</div>
        </section>
    @endif

    @if ($additionalInfo !== '')
        <section class="availability-room-modal__section">
            <h4>@lang('messages.Additional Info')</h4>
            <div>{!! $additionalInfo !!}</div>
        </section>
    @endif
</div>
