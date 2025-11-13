@php
    use Carbon\Carbon;
@endphp
@section('title', __('messages.Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"><i class="icon-copy dw dw-hotel"></i> {{ $hotel->name }}</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (count($errors)>0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        {{-- ATTENTIONS MOBILE --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy ion-ios-pulse-strong"></i> Log</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <p><b>{{ $hotel->name }}</b></p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><b>{{ dateTimeFormat($hotel->created_at) }}</b></p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-6">
                                                <p><b>Author :</b> {{ $author->name }}</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><i>{{ Carbon::parse($hotel->created_at)->diffForHumans();  }}</i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            {{-- HOTEL DETAIL --}}
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="dw dw-hotel" aria-hidden="true"></i> {{ $hotel->name }}</div>
                                </div>
                                <div class="page-card">
                                    <div class="card-banner">
                                        <img src="{{ asset ('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->name }}" loading="lazy">
                                    </div>
                                    <div class="card-content">
                                        <div class="status-card">
                                            @if ($hotel->status == "Rejected")
                                                <div class="status-rejected"></div>
                                            @elseif ($hotel->status == "Invalid")
                                                <div class="status-invalid"></div>
                                            @elseif ($hotel->status == "Active")
                                                <div class="status-active"></div>
                                            @elseif ($hotel->status == "Waiting")
                                                <div class="status-waiting"></div>
                                            @elseif ($hotel->status == "Draft")
                                                <div class="status-draft"></div>
                                            @elseif ($hotel->status == "Archived")
                                                <div class="status-archived"></div>
                                            @else
                                            @endif
                                        </div>
                                        <div class="card-text">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="card-subtitle">Address</div>
                                                    <p>{{ $hotel->address }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card-subtitle">Region</div>
                                                    <a target="__blank" href="{{ $hotel->map }}">
                                                        <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>{{ " ". $hotel->region }}</p>
                                                    </a>
                                                </div>
                                            </div>
                                            @if (isset($hotel->check_in_time) and isset($hotel->check_out_time))
                                                <hr class="form-hr">
                                                <div class="row">
                                                    @if (isset($hotel->check_out_time))
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Check-out</div>
                                                            <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_out_time)) }}</p>
                                                        </div>
                                                    @endif
                                                    @if (isset($hotel->check_in_time))
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Check-in</div>
                                                            <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_in_time)) }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (isset($hotel->airport_distance) and isset($hotel->airport_duration))
                                                <hr class="form-hr">
                                                <div class="row">
                                                    @if (isset($hotel->airport_distance))
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Airport Distance</div>
                                                            <p><i class="fa fa-map" aria-hidden="true"></i> {{ $hotel->airport_distance." Km" }}</p>
                                                        </div>
                                                    @endif
                                                    @if (isset($hotel->airport_duration))
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Airport Duration</div>
                                                            <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $hotel->airport_duration." Hours" }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- WEDDING CONTRACT --}}
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Wedding Brochure</div>
                                </div>
                                <div class="card-box-content">
                                    @if ($wedding_contracts)
                                        @foreach ($wedding_contracts->where('hotels_id',$hotel->id) as $contract)
                                            <div class="card-contract p-8">
                                                <div class="card-subtitle"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ $contract->name }}</div>
                                                <p><i>{{ date('d M y',strtotime($contract->period_start)).' - '.date('d M y',strtotime($contract->period_end)) }}</i></p>
                                                <hr class="form-hr">
                                                <div class="card-action text-right">
                                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#contract-wedding-{{ $contract->id }}">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    @canany(['posDev','posAuthor'])
                                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#edit-wedding-contract-{{ $contract->id }}">
                                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                        </a>
                                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#delete-wedding-contract-{{ $contract->id }}">
                                                            <i class="icon-copy fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    @endcanany
                                                </div>
                                                {{-- MODAL VIEW CONTRACT --}}
                                                <div class="modal fade" id="contract-wedding-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                            <div class="modal-body pd-5">
                                                                <embed src="storage/hotels/wedding-contract/{{ $contract->file_name }}" frameborder="10" width="100%" height="850px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @canany(['posDev','posAuthor'])
                                                    {{-- MODAL DELETE CONTRACT --}}
                                                    <div class="modal fade" id="delete-wedding-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Delete Wedding Contract</div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <p><b>File Name:</b></p>
                                                                            <p>{{ $contract->file_name }}</p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p><b>Contract Name:</b></p>
                                                                            <p>{{ $contract->name }}</p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p><b>Period Start:</b></p>
                                                                            <p>{{ date('d M Y', strtotime($contract->period_start)) }}</p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p><b>Period End:</b></p>
                                                                            <p>{{ date('d M Y', strtotime($contract->period_end)) }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <form action="/fdelete-wedding-contract/{{ $contract->id }}" method="post">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="file_name" value="{{ $contract->file_name }}">
                                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                            <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                                            <button class="btn btn-danger" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i> Delete</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- MODAL EDIT CONTRACT --}}
                                                    <div class="modal fade" id="edit-wedding-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Contract</div>
                                                                    </div>
                                                                
                                                                    <form id="update-wedding-contract-{{ $contract->id }}" action="/fupdate-wedding-contract/{{ $contract->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="file_name"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> {{ $contract->file_name }}</label>
                                                                                    <input type="file" name="file_name" id="file_name" class="custom-file-input @error('file_name') is-invalid @enderror" placeholder="Choose Contract">
                                                                                    @error('file_name')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="contract_name">Contract Name</label>
                                                                                    <input name="contract_name" id="contract_name"  type="text" class="form-control @error('contract_name') is-invalid @enderror" placeholder="Insert contract name" value="{{ $contract->name }}">
                                                                                    @error('contract_name')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="period_start">Period Start</label>
                                                                                    <input readonly name="period_start" id="period_start"  type="text" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Insert contract name" value="{{ date('d M Y', strtotime($contract->period_start)) }}">
                                                                                    @error('period_start')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="period_end">Period End</label>
                                                                                    <input readonly name="period_end" id="period_end"  type="text" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ date('d M Y', strtotime($contract->period_end)) }}">
                                                                                    @error('period_end')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="update-wedding-contract-{{ $contract->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcanany
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="notif">Contract not available</div>
                                    @endif
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-wedding-contract-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Add more contract">
                                            <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                        </a>
                                    </div>
                                    {{-- MODAL ADD Contract =====================================================================================================================--}}
                                    <div class="modal fade" id="add-wedding-contract-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Contract</div>
                                                    </div>
                                                
                                                    <form id="add-wedding-contract" action="/fadd-wedding-contract" method="post" enctype="multipart/form-data">
                                                        {{ csrf_field() }}
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="file_name">Contract PDF File</label>
                                                                    <input type="file" name="file_name" id="file_name" accept="application/pdf" class="custom-file-input @error('file_name') is-invalid @enderror" placeholder="Choose Contract" value="{{ old('file_name') }}" required>
                                                                    @error('file_name')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="contract_name">Contract Name</label>
                                                                    <input name="contract_name" id="contract_name"  type="text" class="form-control @error('contract_name') is-invalid @enderror" placeholder="Insert contract name" value="{{ old('contract_name') }}" required>
                                                                    @error('contract_name')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="period_start" >Period Start</label>
                                                                    <input readonly name="period_start" id="period_start"  type="text" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_start') }}" required>
                                                                    @error('period_start')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="period_end">Period End</label>
                                                                    <input name="period_end" id="period_end"  type="text" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ old('period_end') }}" required>
                                                                    @error('period_end')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            
                                                            <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="add-wedding-contract" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                            {{-- ENTERANCE FEE --}}
                            <div id="EntranceFee" class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Entrance Fee</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! $hotel->entrance_fee !!}
                                    </div>
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#update-entrance-fee-{{ $hotel->id }}">
                                            @if ($hotel->entrance_fee)
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="modal fade" id="update-entrance-fee-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Entrance Fee</div>
                                                    </div>
                                                    <form id="edit-entrance-fee-{{ $hotel->id }}" action="/fupdate-entrance-fee/{{ $hotel->id }}" method="post" enctype="multipart/form-data" >
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label for="entrance_fee" class="form-label">Entrance Fee</label>
                                                            <textarea id="entrance_fee" name="entrance_fee" class="textarea_editor form-control @error('entrance_fee') is-invalid @enderror" placeholder="Insert entrance_fee" value="{{ $hotel->entrance_fee }}">{!! $hotel->entrance_fee !!}</textarea>
                                                            @error('entrance_fee')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="edit-entrance-fee-{{ $hotel->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                            {{-- CANCELLATION POLICY --}}
                            <div id="cancellationPolicy" class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Cancellation Policy</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! $hotel->wedding_cancellation_policy !!}
                                    </div>
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#update-cancellation-policy-{{ $hotel->id }}">
                                            @if ($hotel->wedding_cancellation_policy)
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="modal fade" id="update-cancellation-policy-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>Edit Cancellation Policy</div>
                                                    </div>
                                                    <form id="edit-cancellation-policy-{{ $hotel->id }}" action="/fupdate-cancellation-policy/{{ $hotel->id }}" method="post" enctype="multipart/form-data" >
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label for="wedding_cancellation_policy" class="form-label">Cancellation Policy</label>
                                                            <textarea name="wedding_cancellation_policy" class="textarea_editor form-control @error('wedding_cancellation_policy') is-invalid @enderror" placeholder="Insert wedding_cancellation_policy" value="{{ $hotel->wedding_cancellation_policy }}">{!! $hotel->wedding_cancellation_policy !!}</textarea>
                                                            @error('wedding_cancellation_policy')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="edit-cancellation-policy-{{ $hotel->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                            {{-- OTHER INFORMATION --}}
                            <div id="weddingInformation" class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Other Information</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! $hotel->wedding_info !!}
                                    </div>
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#update-other-information-{{ $hotel->id }}">
                                            @if ($hotel->wedding_info)
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="modal fade" id="update-other-information-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</div>
                                                    </div>
                                                    <form id="edit-other-information-{{ $hotel->id }}" action="/fupdate-wedding-info/{{ $hotel->id }}" method="post" enctype="multipart/form-data" >
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label for="wedding_info" class="form-label">Other Information</label>
                                                            <textarea id="wedding_info" name="wedding_info" class="textarea_editor form-control @error('wedding_info') is-invalid @enderror" placeholder="Insert wedding_info" value="{{ $hotel->wedding_info }}">{!! $hotel->wedding_info !!}</textarea>
                                                            @error('wedding_info')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="edit-other-information-{{ $hotel->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                            @include('admin.wedding.ceremony-venue-admin')
                            {{-- @include('admin.wedding.ceremony-venue-decoration-admin') --}}
                            @include('admin.wedding.reception-venue-admin')
                            {{-- @include('admin.wedding.dinner-venue-admin')
                            @include('admin.wedding.lunch-venue-admin') --}}
                            {{-- @include('admin.wedding.menus-admin')
                            @include('admin.wedding.beverages-admin') 
                            @include('admin.wedding.corcage-admin') --}}
                            @include('admin.wedding.wedding-package-admin')
                        </div>
                        {{-- ATTENTIONS DESKTOP --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy ion-ios-pulse-strong"></i> Log</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <p><b>{{ $hotel->name }}</b></p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><b>{{ dateTimeFormat($hotel->created_at) }}</b></p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-6">
                                                <p><b>Author :</b> {{ $author->name }}</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><i>{{ Carbon::parse($hotel->created_at)->diffForHumans();  }}</i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        <script>
            document.getElementById('reception_venues_id').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var maxCapacity = selectedOption.getAttribute('data-max');
                var minCapacity = selectedOption.getAttribute('data-min');
                document.getElementById('number_of_guests').setAttribute('max', maxCapacity);
                document.getElementById('number_of_guests').setAttribute('min', minCapacity);
                document.getElementById('max_capacity_label').textContent = maxCapacity ? ' (Min: ' + minCapacity + ', Max: '+ maxCapacity +')' : '';
            });
        </script>
    @endcan
@endsection
