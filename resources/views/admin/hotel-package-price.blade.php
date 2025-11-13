@php
    use Carbon\Carbon;
@endphp
<div id="package" class="product-wrap m-b-18">
    <div class="product-detail-wrap">
        <div class="row">
            <div class="col-md-8">
                <div class="card-box">
                    <div class="card-box-title">
                        <i class="icon-copy fa fa-cubes" aria-hidden="true"></i> Package Price
                    </div>
                    <div class="card-box-body">
                        @if (count($hotel->packages) > 0)
                            <div class="search-container">
                                <div class="input-group-icon">
                                    <i class="icon-copy fa fa-search" aria-hidden="true"></i>
                                    <input id="searchPackageByName" type="text" onkeyup="searchPackageByName()" class="input-icon form-control" name="search-package-byname" placeholder="Filter by name">
                                </div>
                            </div> 
                            <table id="tbPackage" class="data-table table nowrap" >
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Name</th>
                                        <th style="width: 30%;">Period</th>
                                        <th style="width: 30%;">Published Rate</th>
                                        <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                       @forelse ($hotel->packages as $package)
                                            <tr>
                                                <td>
                                                    <b>{{ $package->name }}</b><br>
                                                        {{ $room->rooms }}<br>
                                                    @if ($package->booking_code != "")
                                                        <b>{{ $package->booking_code }}</b><br>
                                                    @endif
                                                    @include('partials.status-icon', ['status' => $package->status])
                                                </td>
                                                <td>
                                                    <b>Minimum Stay</b>
                                                    <p>{{ $package->duration." Night" }}</p>
                                                    <b>Stay Period</b>
                                                    <p>{{ dateFormat($package->stay_period_start) }} - {{ dateFormat($package->stay_period_end) }}<br></p>
                                                    @if ($package->stay_period_end < $now)
                                                        <div class="expired-ico">
                                                            <img src="{{ asset ('storage/icon/expired.png') }}" alt="{{ $package->name }}" loading="lazy">
                                                        </div>
                                                    @endif
                                                </td>
                                                
                                                <td>
                                                    <div class="rate-usd">{!! currencyFormatUsd($package->calculatePrice($usdrates,$tax)) !!}</div>
                                                    <div class="rate-idr">{{ currencyFormatIdr($package->calculatePrice($usdrates,$tax) * $usdrates->rate) }}</div>
                                                </td>
                                                <td class="text-right">
                                                    <div class="table-action">
                                                        <a href="#" data-toggle="modal" data-target="#detail-package-{{ $package->id }}">
                                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                        </a>
                                                        @canany(['posDev','posAuthor'])
                                                            <a href="#" data-toggle="modal" data-target="#edit-package-{{ $package->id }}">
                                                                <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                            </a>
                                                            <form action="{{ route('func.package.delete',$package->id) }}" method="post">
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete">
                                                                    <i class="icon-copy fa fa-trash"></i></button>
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                        @endcanany
                                                    </div>
                                                </td>
                                                @include('admin.partials.modal-detail-hotel-package-price', compact("package"))
                                                {{-- @canany(['posDev','posAuthor'])
                                                @endcanany --}}
                                                @canany(['posDev','posAuthor'])
                                                    @include('admin.partials.modal-update-hotel-package-price', compact("package","rooms"))
                                                @endcanany
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">No active prices available for this room.</td>
                                            </tr>
                                        @endforelse
                                </tbody>
                            </table>
                        @else
                            <div class="notif">!!! The hotel doesn't have a package yet, please add a package!</div>
                        @endif
                    </div>
                    <div class="card-box-footer">
                        @canany(['posDev','posAuthor'])
                            <a href="#" data-toggle="modal" data-target="#add-package-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Detail">
                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add package</button>
                            </a>
                        @endcanany
                    </div>
                    {{-- MODAL ADD PACKAGE --}}
                    @canany(['posDev','posAuthor'])
                        <div class="modal fade" id="add-package-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add package</div>
                                        </div>
                                        <div class="card-box-body">
                                            <form id="create-package" action="{{ route('func.package.add') }}" method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
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
                                                            <label for="duration">Minimum Stay </label>
                                                            <input type="number" min="1" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ old('duration') }}" required>
                                                            @error('duration')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name">Name </label>
                                                            <input type="text" name="name" id="name" wire:model="name" class="form-control  @error('name') is-invalid @enderror" placeholder="Insert package name" value="{{ old('name') }}" required>
                                                            @error('name')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="rooms_id">Room <span> *</span></label>
                                                            <select id="rooms_id" name="rooms_id" class="custom-select @error('rooms_id') is-invalid @enderror" required>
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
                                                            <label for="stay_period_start">Stay Period Start </label>
                                                            <input type="text" name="stay_period_start" class="form-control date-picker @error('stay_period_start') is-invalid @enderror" placeholder="Select date" value="{{ old('stay_period_start') }}" required>
                                                            @error('stay_period_start')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="stay_period_end">Stay Period End </label>
                                                            <input type="text" name="stay_period_end" class="form-control date-picker @error('stay_period_end') is-invalid @enderror" placeholder="Select date" value="{{ old('stay_period_end') }}" required>
                                                            @error('stay_period_end')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="contract_rate">Contract Rate <span> *</span></label>
                                                            <div class="btn-icon">
                                                                <span>Rp</span>
                                                                <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="markup">Markup <span> *</span></label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Include</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="updateInclude">English</label>
                                                            <textarea id="updateInclude" name="include"  class="textarea_editor form-control @error('include') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include') }}</textarea>
                                                            @error('include')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="includeTraditionalPackage">Chinese Traditional</label>
                                                            <textarea id="includeTraditionalPackage" name="include_traditional"  class="textarea_editor form-control @error('include_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include_traditional') }}</textarea>
                                                            @error('include_traditional')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="includeSimplifiedPackage">Chinese Simplified</label>
                                                            <textarea id="includeSimplifiedPackage" name="include_simplified"  class="textarea_editor form-control @error('include_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('include_simplified') }}</textarea>
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
                                                            <textarea id="benefitsEnglish" name="benefits"  class="textarea_editor form-control @error('benefits') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits') }}</textarea>
                                                            @error('benefits')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="benefitsTraditionalPackage">Chinese Traditional</label>
                                                            <textarea id="benefitsTraditionalPackage" name="benefits_traditional"  class="textarea_editor form-control @error('benefits_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits_traditional') }}</textarea>
                                                            @error('benefits_traditional')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="benefitsSimplified">Chinese Simplified</label>
                                                            <textarea id="benefitsSimplified" name="benefits_simplified"  class="textarea_editor form-control @error('benefits_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('benefits_simplified') }}</textarea>
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
                                                            <textarea id="additionalInfoEnglish" name="additional_info"  class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info') }}</textarea>
                                                            @error('additional_info')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="additionalInfoTraditionalPackage">Chinese Traditional</label>
                                                            <textarea id="additionalInfoTraditionalPackage" name="additional_info_traditional"  class="textarea_editor form-control @error('additional_info_traditional') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info_traditional') }}</textarea>
                                                            @error('additional_info_traditional')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="additionalInfoSimplified">Chinese Simplified</label>
                                                            <textarea id="additionalInfoSimplified" name="additional_info_simplified"  class="textarea_editor form-control @error('additional_info_simplified') is-invalid @enderror" placeholder="Insert some text ...">{{ old('additional_info_simplified') }}</textarea>
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
                                            <button type="submit" form="create-package" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Package</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>
        </div>
    </div>
</div>