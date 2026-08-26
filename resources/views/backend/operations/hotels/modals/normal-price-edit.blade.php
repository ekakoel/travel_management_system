<div class="modal fade hotel-detail-modal hotel-normal-price-edit-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <section class="backend-modal">
                <div class="backend-modal__header">
                    <div>
                        <span>Normal Price</span>
                        <h2 id="{{ $modalId }}Title">Edit Normal Price</h2>
                        <p>{{ $price->rooms?->rooms ?: 'Room price' }}</p>
                    </div>
                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <form class="backend-form hotel-normal-price-edit-form" action="{{ route('admin.hotels.normal-prices.update', $price->id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="backend-modal__body">
                        <div class="backend-form-grid backend-form-grid--2">
                            <div class="backend-form-field is-wide">
                                <label for="{{ $modalId }}Room" class="backend-form-label is-required">Room</label>
                                <select class="backend-form-control" id="{{ $modalId }}Room" name="rooms_id" required>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" @selected((int) $price->rooms_id === (int) $room->id)>{{ $room->rooms }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}StartDate" class="backend-form-label is-required">Start Date</label>
                                <input class="backend-form-control" id="{{ $modalId }}StartDate" name="start_date" type="text" value="{{ dateFormat($price->start_date) }}" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}EndDate" class="backend-form-label is-required">End Date</label>
                                <input class="backend-form-control" id="{{ $modalId }}EndDate" name="end_date" type="text" value="{{ dateFormat($price->end_date) }}" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}ContractRate" class="backend-form-label is-required">Contract Rate</label>
                                <input class="backend-form-control" id="{{ $modalId }}ContractRate" name="contract_rate" type="text" inputmode="numeric" value="{{ $price->contract_rate }}" placeholder="Insert contract rate" required data-backend-money-unit="IDR">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Markup" class="backend-form-label is-required">Markup</label>
                                <input class="backend-form-control" id="{{ $modalId }}Markup" name="markup" type="text" inputmode="numeric" value="{{ $price->markup }}" placeholder="Insert markup" required data-backend-money-unit="USD">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}KickBack" class="backend-form-label">Kick Back</label>
                                <input class="backend-form-control" id="{{ $modalId }}KickBack" name="kick_back" type="text" inputmode="numeric" value="{{ $price->kick_back ?? 0 }}" placeholder="Insert kick back" data-backend-money-unit="USD">
                            </div>
                        </div>

                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                    </div>

                    <div class="backend-modal__footer">
                        <button type="submit" class="backend-button backend-button-primary">
                            <i class="fa fa-floppy-o"></i>
                            Update Price
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
