@php
    $additionalChargeRows = $hotelDetail->additionalChargeRows();
    $canManageAdditionalCharges = \Illuminate\Support\Facades\Gate::any(['posDev', 'posAuthor', 'posAdm']);
@endphp

<section id="additional-charge" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Commercial Rule</span>
            <h2>Additional Charges</h2>
        </div>
        @if ($canManageAdditionalCharges)
            <div class="hotel-detail-section-actions">
                <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#hotelAdditionalChargeAdd{{ $hotel->id }}">
                    <i class="fa fa-plus"></i>
                    Add Charge
                </button>
            </div>
        @endif
    </div>
    <div class="backend-table-wrap hotel-detail-table-wrap">
        <table class="backend-table hotel-detail-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Mandatory</th>
                    <th>Published Rate</th>
                    @if ($canManageAdditionalCharges)
                        <th class="backend-table-action-column">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($additionalChargeRows as $row)
                    @php
                        $additionalCharge = $row['model'];
                    @endphp
                    <tr>
                        <td data-label="Type">{{ $additionalCharge->type }}</td>
                        <td data-label="Name"><strong>{{ $additionalCharge->name }}</strong></td>
                        <td data-label="Mandatory">{{ $row['mandatory_label'] }}</td>
                        <td data-label="Published Rate">
                            <span class="hotel-detail-rate">{{ currencyFormatUsd($row['published_rate']) }}</span>
                            <button type="button" class="hotel-price-calculation-action" data-toggle="modal" data-target="#hotelAdditionalChargeCalculation{{ $additionalCharge->id }}">
                                View calculation
                            </button>
                        </td>
                        @if ($canManageAdditionalCharges)
                            <td data-label="Action">
                                <div class="backend-table-actions hotel-detail-actions">
                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#hotelAdditionalChargeEdit{{ $additionalCharge->id }}" aria-label="Edit {{ $additionalCharge->name }}">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <form action="{{ route('admin.hotels.additional-charges.destroy', $additionalCharge->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $additionalCharge->name }}" aria-label="Delete {{ $additionalCharge->name }}">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManageAdditionalCharges ? 5 : 4 }}">
                            <div class="backend-table-empty">
                                <i class="fa fa-asterisk"></i>
                                <strong>No additional charges.</strong>
                                <span>Additional hotel charges are not configured yet.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if ($canManageAdditionalCharges)
    @include('backend.operations.hotels.modals.additional-charge-form', [
        'modalId' => 'hotelAdditionalChargeAdd'.$hotel->id,
        'mode' => 'create',
        'hotel' => $hotel,
        'additionalCharge' => null,
    ])
@endif

@foreach ($additionalChargeRows as $row)
    @include('backend.operations.hotels.modals.price-calculation', [
        'modalId' => 'hotelAdditionalChargeCalculation'.$row['model']->id,
        'eyebrow' => 'Additional Charge Calculation',
        'title' => $row['model']->name,
        'subtitle' => $row['model']->type,
        'pricing' => $row['pricing'],
    ])

    @if ($canManageAdditionalCharges)
        @include('backend.operations.hotels.modals.additional-charge-form', [
            'modalId' => 'hotelAdditionalChargeEdit'.$row['model']->id,
            'mode' => 'edit',
            'hotel' => $hotel,
            'additionalCharge' => $row['model'],
        ])
    @endif
@endforeach
