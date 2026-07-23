@php
    $localizedDescription = app()->getLocale() === 'zh'
        ? $hotel->description_simplified
        : (app()->getLocale() === 'zh-CN' ? $hotel->description_traditional : $hotel->description);
    $localizedFacility = app()->getLocale() === 'zh'
        ? $hotel->facility_simplified
        : (app()->getLocale() === 'zh-CN' ? $hotel->facility_traditional : $hotel->facility);
    $localizedBenefits = app()->getLocale() === 'zh'
        ? $hotel->benefits_simplified
        : (app()->getLocale() === 'zh-CN' ? $hotel->benefits_traditional : $hotel->benefits);
    $localizedAdditionalInfo = app()->getLocale() === 'zh'
        ? $hotel->additional_info_simplified
        : (app()->getLocale() === 'zh-CN' ? $hotel->additional_info_traditional : $hotel->additional_info);
    $localizedCancellation = app()->getLocale() === 'zh'
        ? $hotel->cancellation_policy_simplified
        : (app()->getLocale() === 'zh-CN' ? $hotel->cancellation_policy_traditional : $hotel->cancellation_policy);
@endphp

<section class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Hotel Profile</span>
            <h2>Detail Information</h2>
        </div>
        <div class="hotel-detail-section-actions">
        </div>
    </div>
    <div class="hotel-detail-panel__body">
        <div class="hotel-detail-profile-summary">
            <div class="hotel-detail-media-column">
                <figure class="hotel-detail-cover">
                    <img
                        src="{{ asset('storage/hotels/hotels-cover/' . $hotel->cover) }}"
                        alt="{{ $hotel->name }}"
                        loading="lazy"
                        decoding="async"
                        width="640"
                        height="360"
                    >
                </figure>

            </div>

            <dl class="hotel-detail-grid">
                <div><dt>Website</dt><dd><a href="{{ $hotel->web }}" target="_blank" rel="noopener">{{ $hotel->web ?: '-' }}</a></dd></div>
                <div><dt>Region</dt><dd><a href="{{ $hotel->map }}" target="_blank" rel="noopener"><i class="fa fa-map-marker"></i> {{ $hotel->region ?: '-' }}</a></dd></div>
                <div><dt>Contact Person</dt><dd>{{ $hotel->contact_person ?: '-' }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $hotel->phone ?: '-' }}</dd></div>
                <div class="is-wide"><dt>Address</dt><dd>{{ $hotel->address ?: '-' }}</dd></div>
            </dl>

            <dl class="hotel-detail-grid hotel-detail-stay-grid">
                <div><dt>Min Stay</dt><dd>{{ $hotel->min_stay }} nights</dd></div>
                <div><dt>Max Stay</dt><dd>{{ $hotel->max_stay }} nights</dd></div>
                <div><dt>Check-in</dt><dd>{{ $hotel->check_in_time ? date('H.i', strtotime($hotel->check_in_time)) : '-' }}</dd></div>
                <div><dt>Check-out</dt><dd>{{ $hotel->check_out_time ? date('H.i', strtotime($hotel->check_out_time)) : '-' }}</dd></div>
                <div><dt>Airport Distance</dt><dd>{{ $hotel->airport_distance ? $hotel->airport_distance . ' Km' : '-' }}</dd></div>
                <div><dt>Airport Duration</dt><dd>{{ $hotel->airport_duration ? $hotel->airport_duration . ' Hours' : '-' }}</dd></div>
            </dl>
        </div>

        @foreach ([
            'Description' => $localizedDescription,
            'Facility' => $localizedFacility,
            'Benefits' => $localizedBenefits,
            'Additional Charge' => $hotel->optional_rate,
            'Additional Information' => $localizedAdditionalInfo,
            'Cancellation Policy' => $localizedCancellation,
        ] as $label => $content)
            @if (filled($content))
                <div class="hotel-detail-content-block">
                    <span>{{ $label }}</span>
                    <p>{!! $content !!}</p>
                </div>
            @endif
        @endforeach
    </div>
</section>
