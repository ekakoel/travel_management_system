{{-- MODAL EDIT PACKAGE =======================================================================================--}}
<div class="modal fade" id="edit-package-{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Package Price
                </div>
                <div class="card-box-body">
                    <form id="update-package-{{ $package->id }}" action="{{ route('func.package.update',$package->id) }}" method="post" enctype="multipart/form-data">
                        @method('put')
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="booking_code">Booking Code</label>
                                    <input name="booking_code" id="booking_code" type="text" wire:model="booking_code" class="form-control @error('booking_code') is-invalid @enderror" placeholder="Insert booking code" value="{{ $package->booking_code }}">
                                    @error('booking_code')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span>*</span></label>
                                    <select id="status" name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                        <option selected value="{{ $package->status }}">{{ $package->status }}</option>
                                        <option value="Active">Active</option>
                                        <option value="Draft">Draft</option>
                                    </select>
                                    @error('status')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name </label>
                                    <input name="name" id="name" type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Select Date and Time" value="{{ $package->name }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rooms_id">Rooms <span>*</span></label>
                                    <select id="rooms_id" name="rooms_id" class="custom-select col-12 @error('rooms_id') is-invalid @enderror" required>
                                        <option selected value="{{ $room->id }}">{{ $room->rooms }}</option>
                                        @foreach ($rooms as $prsroom)
                                            <option value="{{ $prsroom->id }}">{{ $prsroom->rooms }}</option>
                                        @endforeach
                                    </select>
                                    @error('rooms_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stay_period_start">Stay Period Start <span>*</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy dw dw-calendar-6"></i></span>
                                        <input  type="text" name="stay_period_start" id="stay_period_start" class="input-icon form-control date-picker @error('stay_period_start') is-invalid @enderror" value="{{ dateFormat($package->stay_period_start) }}" required>
                                    </div>
                                    @error('stay_period_start')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stay_period_end">Stay Period End <span>*</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy dw dw-calendar-6"></i></span>
                                        <input type="text" name="stay_period_end" id="stay_period_end" class="input-icon form-control date-picker @error('stay_period_end') is-invalid @enderror" value="{{ dateFormat($package->stay_period_end) }}" required>
                                    </div>
                                    @error('stay_period_end')
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
                                        <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $package->contract_rate }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="markup">Markup <span>*</span></label>
                                    <div class="btn-icon">
                                        <span>$</span>
                                        <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $package->markup }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration">Minimum Stay </label>
                                    <input  type="text" name="duration" id="duration" class="form-control date-picker @error('duration') is-invalid @enderror" value="{{ $package->duration }}" required>
                                    @error('duration')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="tab-inner-title">Include</div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="updateInclude">English</label>
                                    <textarea id="updateInclude" name="include"  class="textarea_editor form-control @error('include') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->include !!}</textarea>
                                    @error('include')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="includeTraditionalPackage">Chinese Traditional</label>
                                    <textarea id="includeTraditionalPackage" name="include_traditional"  class="textarea_editor form-control @error('include_traditional') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->include_traditional !!}</textarea>
                                    @error('include_traditional')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="includeSimplifiedPackage">Chinese Simplified</label>
                                    <textarea id="includeSimplifiedPackage" name="include_simplified"  class="textarea_editor form-control @error('include_simplified') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->include_simplified !!}</textarea>
                                    @error('include_simplified')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="tab-inner-title">Benefits</div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="benefitsEnglish">English</label>
                                    <textarea id="benefitsEnglish" name="benefits"  class="textarea_editor form-control @error('benefits') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->benefits !!}</textarea>
                                    @error('benefits')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="benefitsTraditionalPackage">Chinese Traditional</label>
                                    <textarea id="benefitsTraditionalPackage" name="benefits_traditional"  class="textarea_editor form-control @error('benefits_traditional') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->benefits_traditional !!}</textarea>
                                    @error('benefits_traditional')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="benefitsSimplified">Chinese Simplified</label>
                                    <textarea id="benefitsSimplified" name="benefits_simplified"  class="textarea_editor form-control @error('benefits_simplified') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->benefits_simplified !!}</textarea>
                                    @error('benefits_simplified')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="tab-inner-title">Additional Information</div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additionalInfoEnglish">English</label>
                                    <textarea id="additionalInfoEnglish" name="additional_info"  class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->additional_info !!}</textarea>
                                    @error('additional_info')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additionalInfoTraditionalPackage">Chinese Traditional</label>
                                    <textarea id="additionalInfoTraditionalPackage" name="additional_info_traditional"  class="textarea_editor form-control @error('additional_info_traditional') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->additional_info_traditional !!}</textarea>
                                    @error('additional_info_traditional')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additionalInfoSimplified">Chinese Simplified</label>
                                    <textarea id="additionalInfoSimplified" name="additional_info_simplified"  class="textarea_editor form-control @error('additional_info_simplified') is-invalid @enderror" placeholder="Insert some text ...">{!! $package->additional_info_simplified !!}</textarea>
                                    @error('additional_info_simplified')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                            <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                        </div>
                    </form>
                </div>
                <div class="card-box-footer">
                    <button type="submit" form="update-package-{{ $package->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>