<div class="modal fade" id="add-promo-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add New Promo
                </div>
                <div class="card-box-body">
                    <form id="create-promo" action="{{ route('func.promo.add') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name </label>
                                    <input type="text" name="name" id="name" wire:model="name" class="form-control  @error('name') is-invalid @enderror" placeholder="Insert promo name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="promotion_type">Promotion Type <span>*</span></label>
                                    <select id="promotion_type" name="promotion_type" class="form-control custom-select @error('promotion_type') is-invalid @enderror" required>
                                        <option selected value="Special Offer">Special Offer</option>
                                        <option value="Best Choice">Best Choice</option>
                                        <option value="Best Price">Best Price</option>
                                        <option value="Hot Deal">Hot Deal</option>
                                    </select>
                                    @error('promotion_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="booking_code">Booking Code</label>
                                    <input type="text" name="booking_code" id="booking_code" wire:model="booking_code" class="form-control  @error('booking_code') is-invalid @enderror" placeholder="Insert booking code" value="{{ old('booking_code') }}">
                                    @error('booking_code')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quotes">Quotes</label>
                                    <input type="text" name="quotes" id="quotes" wire:model="quotes" class="form-control  @error('quotes') is-invalid @enderror" placeholder="Ex: Get special price for special moment!" value="{{ old('quotes') }}">
                                    @error('quotes')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rooms_id">Room <span>*</span></label>
                                    <select id="rooms_id" name="rooms_id" class="form-control custom-select @error('rooms_id') is-invalid @enderror" required>
                                        <option selected value="">Select room</option>
                                        @foreach ($rooms as $sroom)
                                            <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                        @endforeach
                                    </select>
                                    @error('rooms_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimum_stay">Minimum Stay </label>
                                    <input type="number" min="1" max="8" name="minimum_stay" id="minimum_stay" wire:model="minimum_stay" class="form-control custom-input-number  @error('minimum_stay') is-invalid @enderror" placeholder="Insert minimum stay" value="{{ old('minimum_stay') }}" required>
                                    @error('minimum_stay')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="book_periode_start">Book Period Start </label>
                                    <input type="text" name="book_periode_start" id="book_periode_start" wire:model="book_periode_start" class="form-control date-picker @error('book_periode_start') is-invalid @enderror" placeholder="Select date" value="{{ old('book_periode_start') }}" required>
                                    @error('book_periode_start')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="book_periode_end">Book Period End </label>
                                    <input type="text" name="book_periode_end" id="book_periode_end" wire:model="book_periode_end" class="form-control date-picker @error('book_periode_end') is-invalid @enderror" placeholder="Select date" value="{{ old('book_periode_end') }}" required>
                                    @error('book_periode_end')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periode_start">Stay Period Start </label>
                                    <input type="text" name="periode_start" id="periode_start" wire:model="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Select date" value="{{ old('periode_start') }}" required>
                                    @error('periode_start')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periode_end">Stay Period End </label>
                                    <input type="text" name="periode_end" id="periode_end" wire:model="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Select date" value="{{ old('periode_end') }}" required>
                                    @error('periode_end')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_rate">Contract Rate <span>*</span></label>
                                    <div class="btn-icon">
                                        <span>Rp</span>
                                        <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="markup">Markup <span>*</span></label>
                                    <div class="btn-icon">
                                        <span>$</span>
                                        <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr class="form-hr">
                            </div>
                            <div class="col-md-12">
                                <div class="row my-18">
                                    <div class="col-md-12">
                                        <div class="tab-inner-title">Include</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="include">English</label>
                                            <textarea id="include" name="include"  class="textarea_editor form-control border-radius-0 @error('include') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include') }}</textarea>
                                            @error('include')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="include_traditional">Chinese Traditional</label>
                                            <textarea id="include_traditional" name="include_traditional"  class="textarea_editor form-control border-radius-0 @error('include_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include_traditional') }}</textarea>
                                            @error('include_traditional')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="include_simplified">Chinese Simplified</label>
                                            <textarea id="include_simplified" name="include_simplified"  class="textarea_editor form-control border-radius-0 @error('include_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include_simplified') }}</textarea>
                                            @error('include_simplified')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr class="form-hr">
                            </div>
                            <div class="col-md-12">
                                <div class="row my-18">
                                    <div class="col-md-12">
                                        <div class="tab-inner-title">Benefits</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="benefits">English</label>
                                            <textarea id="benefits" name="benefits"  class="textarea_editor form-control border-radius-0 @error('benefits') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits') }}</textarea>
                                            @error('benefits')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="benefits_traditional">Chinese Traditional</label>
                                            <textarea id="benefits_traditional" name="benefits_traditional"  class="textarea_editor form-control border-radius-0 @error('benefits_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits_traditional') }}</textarea>
                                            @error('benefits_traditional')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="benefits_simplified">Chinese Simplified</label>
                                            <textarea id="benefits_simplified" name="benefits_simplified"  class="textarea_editor form-control border-radius-0 @error('benefits_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits_simplified') }}</textarea>
                                            @error('benefits_simplified')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr class="form-hr">
                            </div>
                            <div class="col-md-12">
                                <div class="row my-18">
                                    <div class="col-md-12">
                                        <div class="tab-inner-title">Additional Information</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="additional_info">English</label>
                                            <textarea id="additional_info" name="additional_info"  class="textarea_editor form-control border-radius-0 @error('additional_info') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info') }}</textarea>
                                            @error('additional_info')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="additional_info_traditional">Chinese Traditional</label>
                                            <textarea id="additional_info_traditional" name="additional_info_traditional"  class="textarea_editor form-control border-radius-0 @error('additional_info_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info_traditional') }}</textarea>
                                            @error('additional_info_traditional')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="additional_info_simplified">Chinese Simplified</label>
                                            <textarea id="additional_info_simplified" name="additional_info_simplified"  class="textarea_editor form-control border-radius-0 @error('additional_info_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info_simplified') }}</textarea>
                                            @error('additional_info_simplified')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                        </div>
                    </form>
                </div>
                <div class="card-box-footer">
                    <button type="submit" form="create-promo" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Promo</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>