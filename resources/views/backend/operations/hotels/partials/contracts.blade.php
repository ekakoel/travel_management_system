<section id="contracts" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Legal Document</span>
            <h2>Contracts</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#hotelContractAddModal">
                    <i class="fa fa-plus"></i>
                    Add Contract
                </button>
            </div>
        @endcanany
    </div>
    <div class="hotel-detail-panel__body">
        <div class="hotel-detail-contract-list">
            @forelse ($contracts as $contract)
                <article class="hotel-detail-contract-card">
                    <div class="hotel-detail-contract-card__header">
                        <div>
                            <strong>{{ $contract->name }}</strong>
                            <span>{{ dateFormat($contract->period_start) }} - {{ dateFormat($contract->period_end) }}</span>
                        </div>
                        <span class="backend-status-badge backend-status-badge--active">Valid</span>
                    </div>
                    <div class="hotel-detail-actions">
                        <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#hotelContractView{{ $contract->id }}" aria-label="View {{ $contract->name }}">
                            <i class="fa fa-eye"></i>
                        </button>
                        @canany(['posDev','posAuthor'])
                            <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#hotelContractEdit{{ $contract->id }}" aria-label="Edit {{ $contract->name }}">
                                <i class="fa fa-pencil-alt"></i>
                            </button>
                            <form action="{{ route('admin.hotels.contracts.destroy', $contract->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="file_name" value="{{ $contract->file_name }}">
                                <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $contract->name }}" aria-label="Delete {{ $contract->name }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </form>
                        @endcanany
                    </div>
                </article>
            @empty
                <div class="backend-empty-state">
                    <i class="fa fa-file-pdf-o"></i>
                    <strong>No active contracts.</strong>
                    <span>Add a contract document to keep hotel agreement data complete.</span>
                </div>
            @endforelse
        </div>
    </div>
</section>
