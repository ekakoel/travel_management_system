<div class="modal fade" id="edit-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Price
                </div>
                <div class="card-box-body">
                    <form id="update-price-{{ $price->id }}" action="/fedit-price-{{ $price->id }}" method="post" enctype="multipart/form-data">
                        @method('put')
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rooms_id">Room </label>
                                            <select id="rooms_id" name="rooms_id" class="custom-select @error('rooms_id') is-invalid @enderror" required>
                                                @foreach ($rooms as $sroom)
                                                    <option {{ $price->rooms->id === $sroom->id? "selected":""; }} value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                                @endforeach
                                            </select>
                                            @error('rooms_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Starting </label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy dw dw-calendar-6"></i></span>
                                                <input name="start_date" id="start_date" wire:model="start_date" class="input-icon form-control date-picker @error('start_date') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ date('d M y', strtotime($price->start_date)) }}" required>
                                                @error('start_date')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">Ending </label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy dw dw-calendar-6"></i></span>
                                                <input name="end_date" id="end_date" wire:model="end_date" class="input-icon form-control date-picker @error('end_date') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ date('d M y', strtotime($price->end_date)) }}" required>
                                                @error('end_date')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contract_rate">Contract Rate </label>
                                            <div class="btn-icon">
                                                <span>Rp</span>
                                                <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $price->contract_rate }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="markup">Markup </label>
                                            <div class="btn-icon">
                                                <span>$</span>
                                                <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $price->markup }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kick_back">Kick Back</label>
                                            <div class="btn-icon">
                                                <span>$</span>
                                                <input type="number" id="kick_back" name="kick_back" class="input-icon form-control @error('kick_back') is-invalid @enderror" placeholder="Insert kick back" value="{{ $price->kick_back }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                            <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                        </div>
                    </form>
                </div>
                <div class="card-box-footer">
                    <button type="submit" form="update-price-{{ $price->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>