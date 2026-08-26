@php
    $extraBedRows = $hotelDetail->extraBedRows();
    $canManageExtraBeds = \Illuminate\Support\Facades\Gate::any(['posDev', 'posAuthor']);
@endphp

<section id="extra-bed" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Pricing Add-on</span>
            <h2>Extra Bed</h2>
        </div>
        @if ($canManageExtraBeds)
            <div class="hotel-detail-section-actions">
                <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#hotelExtraBedAdd{{ $hotel->id }}">
                    <i class="fa fa-plus"></i>
                    Add Extra Bed
                </button>
            </div>
        @endif
    </div>

    <div class="backend-table-wrap hotel-detail-table-wrap">
        <table class="backend-table hotel-detail-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age Rule</th>
                    <th>Description</th>
                    <th>Published Rate</th>
                    @if ($canManageExtraBeds)
                        <th class="backend-table-action-column">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($extraBedRows as $row)
                    @php
                        $extraBed = $row['model'];
                    @endphp
                    <tr>
                        <td data-label="Name">
                            <strong>{{ $row['title'] }}</strong>
                            <span>{{ $extraBed->type }}</span>
                        </td>
                        <td data-label="Age Rule">{{ $row['age_range'] }}</td>
                        <td data-label="Description">{!! \Illuminate\Support\Str::limit(strip_tags($row['description']), 120) !!}</td>
                        <td data-label="Published Rate">
                            <span class="hotel-detail-rate">{{ currencyFormatUsd($row['published_rate']) }}</span>
                            <button type="button" class="hotel-price-calculation-action" data-toggle="modal" data-target="#hotelExtraBedCalculation{{ $extraBed->id }}">
                                View calculation
                            </button>
                        </td>
                        @if ($canManageExtraBeds)
                            <td data-label="Action">
                                <div class="backend-table-actions hotel-detail-actions">
                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#hotelExtraBedEdit{{ $extraBed->id }}" aria-label="Edit {{ $row['title'] }}">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <form action="{{ route('func.extrabed.delete', $extraBed->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $row['title'] }}" aria-label="Delete {{ $row['title'] }}">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManageExtraBeds ? 5 : 4 }}">
                            <div class="backend-table-empty">
                                <i class="fa fa-bed"></i>
                                <strong>No extra beds.</strong>
                                <span>Extra bed add-ons are not configured for this Hotel yet.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if ($canManageExtraBeds)
    @include('backend.operations.hotels.modals.extra-bed-form', [
        'modalId' => 'hotelExtraBedAdd'.$hotel->id,
        'mode' => 'create',
        'hotel' => $hotel,
        'extraBed' => null,
    ])
@endif

@foreach ($extraBedRows as $row)
    @include('backend.operations.hotels.modals.price-calculation', [
        'modalId' => 'hotelExtraBedCalculation'.$row['model']->id,
        'eyebrow' => 'Extra Bed Calculation',
        'title' => $row['title'],
        'subtitle' => $row['age_range'],
        'pricing' => $row['pricing'],
    ])

    @if ($canManageExtraBeds)
        @include('backend.operations.hotels.modals.extra-bed-form', [
            'modalId' => 'hotelExtraBedEdit'.$row['model']->id,
            'mode' => 'edit',
            'hotel' => $hotel,
            'extraBed' => $row['model'],
        ])
    @endif
@endforeach
