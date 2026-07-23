<section id="additional-charge" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Commercial Rule</span>
            <h2>Additional Charges</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <a href="{{ route('admin.hotels.additional-charges.create', $hotel->id) }}" class="backend-toolbar-action">
                    <i class="fa fa-plus"></i>
                    Add Charge
                </a>
            </div>
        @endcanany
    </div>
    <div class="backend-table-wrap hotel-detail-table-wrap">
        <table class="backend-table hotel-detail-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Mandatory</th>
                    <th>Published Rate</th>
                    @canany(['posDev','posAuthor'])
                        <th>Action</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse ($hotelDetail->additionalChargeRows() as $row)
                    @php
                        $additionalCharge = $row['model'];
                    @endphp
                    <tr>
                        <td data-label="Type">{{ $additionalCharge->type }}</td>
                        <td data-label="Name"><strong>{{ $additionalCharge->name }}</strong></td>
                        <td data-label="Mandatory">{{ $row['mandatory_label'] }}</td>
                        <td data-label="Published Rate"><span class="hotel-detail-rate">{!! currencyFormatUsd($row['published_rate']) !!}</span></td>
                        @canany(['posDev','posAuthor'])
                            <td data-label="Action">
                                <div class="hotel-detail-actions">
                                    <a href="{{ route('admin.hotels.additional-charges.edit', $additionalCharge->id) }}" class="backend-icon-action" aria-label="Edit {{ $additionalCharge->name }}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.additional-charges.destroy', $additionalCharge->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $additionalCharge->name }}" aria-label="Delete {{ $additionalCharge->name }}">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
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
