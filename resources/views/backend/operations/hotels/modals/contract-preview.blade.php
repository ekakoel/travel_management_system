@foreach ($contracts as $contract)
    <div class="modal fade hotel-detail-modal" id="hotelContractView{{ $contract->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <section class="backend-modal">
                    <div class="backend-modal__header">
                        <div>
                            <span>Contract Document</span>
                            <h3>{{ $contract->name }}</h3>
                        </div>
                        <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="backend-modal__body">
                        <embed src="{{ asset('storage/hotels/hotels-contract/' . $contract->file_name) }}" width="100%" height="720">
                    </div>
                </section>
            </div>
        </div>
    </div>

    @canany(['posDev','posAuthor'])
        <div class="modal fade hotel-detail-modal" id="hotelContractEdit{{ $contract->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <section class="backend-modal">
                        <div class="backend-modal__header">
                            <div>
                                <span>Contract</span>
                                <h3>Edit Contract</h3>
                            </div>
                            <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <form id="hotelContractUpdate{{ $contract->id }}" action="{{ route('admin.hotels.contracts.update', $contract->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="backend-modal__body">
                                <div class="hotel-detail-form-grid">
                                    <div class="is-wide">
                                        <label for="contractFile{{ $contract->id }}">Contract PDF File</label>
                                        <input type="file" name="file_name" id="contractFile{{ $contract->id }}">
                                    </div>
                                    <div>
                                        <label for="contractName{{ $contract->id }}">Contract Name</label>
                                        <input name="contract_name" id="contractName{{ $contract->id }}" type="text" value="{{ $contract->name }}" required>
                                    </div>
                                    <div>
                                        <label for="contractStart{{ $contract->id }}">Period Start</label>
                                        <input readonly name="period_start" id="contractStart{{ $contract->id }}" type="text" class="date-picker" value="{{ date('d M Y', strtotime($contract->period_start)) }}" required>
                                    </div>
                                    <div>
                                        <label for="contractEnd{{ $contract->id }}">Period End</label>
                                        <input readonly name="period_end" id="contractEnd{{ $contract->id }}" type="text" class="date-picker" value="{{ date('d M Y', strtotime($contract->period_end)) }}" required>
                                    </div>
                                </div>
                                <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                            </div>
                            <div class="backend-modal__footer">
                                <button type="submit" class="backend-page-primary-action">Update</button>
                                <button type="button" class="backend-toolbar-action" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    @endcanany
@endforeach

@canany(['posDev','posAuthor'])
    <div class="modal fade hotel-detail-modal" id="hotelContractAddModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <section class="backend-modal">
                    <div class="backend-modal__header">
                        <div>
                            <span>Contract</span>
                            <h3>Add Contract</h3>
                        </div>
                        <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <form id="hotelContractCreate" action="{{ route('admin.hotels.contracts.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="backend-modal__body">
                            <div class="hotel-detail-form-grid">
                                <div class="is-wide">
                                    <label for="contractCreateFile">Contract PDF File</label>
                                    <input type="file" name="file_name" id="contractCreateFile" required>
                                </div>
                                <div>
                                    <label for="contractCreateName">Contract Name</label>
                                    <input name="contract_name" id="contractCreateName" type="text" value="{{ old('contract_name') }}" required>
                                </div>
                                <div>
                                    <label for="contractCreateStart">Period Start</label>
                                    <input readonly name="period_start" id="contractCreateStart" type="text" class="date-picker" value="{{ old('period_start') }}" required>
                                </div>
                                <div>
                                    <label for="contractCreateEnd">Period End</label>
                                    <input name="period_end" id="contractCreateEnd" type="text" class="date-picker" value="{{ old('period_end') }}" required>
                                </div>
                            </div>
                            <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                            <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                        </div>
                        <div class="backend-modal__footer">
                            <button type="submit" class="backend-page-primary-action">Add Contract</button>
                            <button type="button" class="backend-toolbar-action" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>

@endcanany
