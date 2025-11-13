@php
    use Carbon\Carbon;
@endphp
@section('title', __('messages.Villa Detail'))
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
                                <div class="title"><i class="micon dw dw-building-1" aria-hidden="true"></i>Private Villa</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="/villas-admin">Villas</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $villa->name }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (count($errors) > 0)
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
                                                <p><b>{{ $villa->name }}</b></p>
                                            </div>
                                            <div class="col-6">
                                                <p><b>{{ dateTimeFormat($villa->created_at) }}</b></p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-6">
                                                <p><b>Author :</b> {{ $author?->name }}</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><i>{{ Carbon::parse($villa->created_at)->diffForHumans();  }}</i></p>
                                            </div>
                                            <div class="col-md-12">
                                                <p><b>Rooms :</b> {{ count($villa->rooms)." rooms" }}</p>
                                                @php
                                                    $last_price = $villa->prices->where('end_date','>', $now);
                                                    $clp = count($last_price);
                                                    $end_date = $now;
                                                    $hi = $now;
                                                @endphp
                                                @foreach ($villa->prices as $lprices)
                                                
                                                    @php
                                                        $ed = $lprices->end_date;
                                                    @endphp
                                                    @if ($ed > $hi)
                                                        @php
                                                            $end_date = $ed;
                                                            $hi = $ed;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                @if ($end_date > date('Y-m-d',strtotime($now)))
                                                    <p><b>Last Price :</b> {{ dateFormat($end_date) }}</p>
                                                @else
                                                    <p style="color:red;">Expired</p>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="fa fa-file-text" aria-hidden="true"></i> {{ $villa->name }}</div>
                                    <div class="status-card">
                                        @include('partials.status-icon', ['status' => $villa->status])
                                    </div>
                                </div>
                                <div class="page-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="main-card-banner">
                                                <img src="{{ asset ('storage/villas/covers/' . $villa->cover) }}" alt="{{ $villa->name }}" loading="lazy">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card-subtitle">Website</div>
                                                            <p>{{ $villa->web }}</p>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="card-subtitle">Min Stay</div>
                                                            <p>{{ $villa->min_stay." nights" }}</p>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Contact Person</div>
                                                            <p>{{ $villa->contact_person }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Phone</div>
                                                            <p>{{ $villa->phone }}</p>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Address</div>
                                                            <p>{{ $villa->address }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="card-subtitle">Region</div>
                                                            <a target="__blank" href="{{ $villa->map }}">
                                                                <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>{{ " ". $villa->region }}</p>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    @if (isset($villa->check_in_time) and isset($villa->check_out_time))
                                                        <hr class="form-hr">
                                                        <div class="row">
                                                            @if (isset($villa->check_out_time))
                                                                <div class="col-6">
                                                                    <div class="card-subtitle">Check-out</div>
                                                                    <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('h:i a',strtotime($villa->check_out_time)) }}</p>
                                                                </div>
                                                            @endif
                                                            @if (isset($villa->check_in_time))
                                                                <div class="col-6">
                                                                    <div class="card-subtitle">Check-in</div>
                                                                    <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('h:i a',strtotime($villa->check_in_time)) }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if (isset($villa->airport_distance) and isset($villa->airport_duration))
                                                        <hr class="form-hr">
                                                        <div class="row">
                                                            @if (isset($villa->airport_distance))
                                                                <div class="col-6">
                                                                    <div class="card-subtitle">Airport Distance</div>
                                                                    <p><i class="fa fa-map" aria-hidden="true"></i> {{ $villa->airport_distance." Km" }}</p>
                                                                </div>
                                                            @endif
                                                            @if (isset($villa->airport_duration))
                                                                <div class="col-6">
                                                                    <div class="card-subtitle">Airport Duration</div>
                                                                    <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $villa->airport_duration." Hours" }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr class="form-hr">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-subtitle">Description</div>
                                        </div>
                                        <div class="col-md-12">
                                            <p class="m-b-8">
                                                <b>English:</b><br>
                                                {!! $villa->description !!}
                                            </p>
                                            @if ($villa->description_traditional)
                                                <p class="m-b-8">
                                                    <b>Chinese Traditional:</b><br>
                                                    {!! $villa->description_traditional !!}
                                                </p>
                                            @endif
                                            @if ($villa->description_simplified)
                                                <p class="m-b-8">
                                                    <b>Chinese Simplified:</b><br>
                                                    {!! $villa->description_simplified !!}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($villa->facility)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-subtitle">Facility</div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="m-b-8">
                                                    <b>English:</b><br>
                                                    {!! $villa->facility !!}
                                                </p>
                                                @if ($villa->facility_traditional)
                                                    <p class="m-b-8">
                                                        <b>Chinese Traditional:</b><br>
                                                        {!! $villa->facility_traditional !!}
                                                    </p>
                                                @endif
                                                @if ($villa->facility_simplified)
                                                    <p class="m-b-8">
                                                        <b>Chinese Simplified:</b><br>
                                                        {!! $villa->facility_simplified !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if ($villa->additional_info)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-subtitle">Additional Information</div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="m-b-8">
                                                    <b>English:</b><br>
                                                    {!! $villa->additional_info !!}
                                                </p>
                                                @if ($villa->additional_info_traditional)
                                                    <p class="m-b-8">
                                                        <b>Chinese Traditional:</b><br>
                                                        {!! $villa->additional_info_traditional !!}
                                                    </p>
                                                @endif
                                                @if ($villa->additional_info_simplified)
                                                    <p class="m-b-8">
                                                        <b>Chinese Simplified:</b><br>
                                                        {!! $villa->additional_info_simplified !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if ($villa->cancellation_policy)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-subtitle">Cancellation Policy</div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="m-b-8">
                                                    <b>English:</b><br>
                                                    {!! $villa->cancellation_policy !!}
                                                </p>
                                                @if ($villa->cancellation_policy_traditional)
                                                    <p class="m-b-8">
                                                        <b>Chinese Traditional:</b><br>
                                                        {!! $villa->cancellation_policy_traditional !!}
                                                    </p>
                                                @endif
                                                @if ($villa->cancellation_policy_simplified)
                                                    <p class="m-b-8">
                                                        <b>Chinese Simplified:</b><br>
                                                        {!! $villa->cancellation_policy_simplified !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @canany(['posDev','posAuthor'])
                                        <div class="card-box-footer">
                                            <a href="{{ route('admin.villa.edit',$villa->id) }}">
                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Villa</button>
                                            </a>
                                        </div>
                                    @endcanany
                                </div>
                            </div>
                            {{-- CONTRACT --}}
                            <div id="contracts" class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Contract</div>
                                </div>
                                <div class="card-box-content">
                                    @if (count($contracts)>0)
                                        @foreach ($contracts->where('villa_id',$villa->id) as $contract)
                                            <div class="card-contract p-8">
                                                <div class="card-subtitle"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ $contract->name }}</div>
                                                <p><i>{{ date('d M y',strtotime($contract->period_start)).' - '.date('d M y',strtotime($contract->period_end)) }}</i></p>
                                                <hr class="form-hr">
                                                <div class="card-action text-right">
                                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#contract-{{ $contract->id }}">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    @canany(['posDev','posAuthor'])
                                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#edit-contract-{{ $contract->id }}">
                                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                        </a>
                                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#delete-contract-{{ $contract->id }}">
                                                            <i class="icon-copy fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    @endcanany
                                                </div>
                                                {{-- MODAL VIEW CONTRACT --}}
                                                <div class="modal fade" id="contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                            <div class="modal-body pd-5">
                                                                <embed src="{{ asset('/storage/villas/villas-contract/'.$contract->file_name) }}" frameborder="10" width="100%" height="850px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @canany(['posDev','posAuthor'])
                                                    {{-- MODAL DELETE CONTRACT --}}
                                                    <div class="modal fade" id="delete-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Delete Contract</div>
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
                                                                        <form id="removeContract" action="{{ route('func.remove-villa-contract',$contract->id) }}" method="post">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="file_name" value="{{ $contract->file_name }}">
                                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                            <input type="hidden" name="villa_id" value="{{ $villa->id }}">
                                                                            <button class="btn btn-danger" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i> Delete</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- MODAL EDIT CONTRACT --}}
                                                    <div class="modal fade" id="edit-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Contract</div>
                                                                    </div>
                                                                
                                                                    <form id="update-villa-contract-{{ $contract->id }}" action="{{ route('func.update-villa-contract',$contract->id) }}" method="post" enctype="multipart/form-data">
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
                                                                            <input name="villa_id" value="{{ $villa->id }}" type="hidden">
                                                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="update-villa-contract-{{ $contract->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
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
                                        <a href="#" data-toggle="modal" data-target="#add-contract-{{ $villa->id }}" data-toggle="tooltip" data-placement="top" title="Add more contract">
                                            <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Contract</button>
                                        </a>
                                    </div>
                                    {{-- MODAL ADD Contract =====================================================================================================================--}}
                                    <div class="modal fade" id="add-contract-{{ $villa->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Contract</div>
                                                    </div>
                                                
                                                    <form id="addContract" action="/fadd-villa-contract" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        {{ csrf_field() }}
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="file_name">Contract PDF File</label>
                                                                    <input type="file" name="file_name" id="file_name" class="custom-file-input @error('file_name') is-invalid @enderror" placeholder="Choose Contract" value="{{ old('file_name') }}" required>
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
                                                            
                                                            <input name="villa_id" value="{{ $villa->id }}" type="hidden">
                                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="addContract" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                            {{-- ROOM --}}
                            <div id="rooms" class="product-wrap">
                                <div class="product-detail-wrap">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="fa fa-bed" aria-hidden="true"></i> Rooms</div>
                                        </div>
                                        @if (count($villa->rooms) > 0)
                                            <div class="card-box-content">
                                                @foreach ($villa->rooms as $room)
                                                    <div class="card">
                                                        <a href="#" data-toggle="modal" data-target="#detail-room-{{ $room->id }}">
                                                            <div class="card-image-container">
                                                                <img class="img-fluid rounded thumbnail-image {{ $room->status == 0 ? "grayscale":""  }}" src="{{ url('storage/villas/rooms/' . $room->cover) }}" alt="{{ $room->name }}">
                                                                <div class="card-detail-title">
                                                                    <p>
                                                                        {{ $room->name }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        @canany(['posDev','posAuthor'])
                                                            <div class="card-btn-container">
                                                                <a href="{{ route('view.edit-villa-room',$room->id) }}">
                                                                    <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button><br>
                                                                </a>
                                                                <form id="deleteRoom{{ $room->id }}" action="{{ route('func.delete-villa-room',$room->id) }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                    <input id="villas_id" name="villas_id" value="{{ $villa->id }}" type="hidden">
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        @endcanany
                                                    </div>
                                                    {{-- MODAL ROOM DETAIL --}}
                                                    <div class="modal fade" id="detail-room-{{ $room->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="fa fa-bed" aria-hidden="true"></i> {{ $room->name }}</div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="page-card">
                                                                                <div class="card-banner">
                                                                                    <img src="{{ asset ('storage/villas/rooms/' . $room->cover) }}" alt="{{ $room->name }}" loading="lazy">
                                                                                </div>
                                                                                <div class="card-content">
                                                                                    <div class="card-text">
                                                                                        <div class="row ">
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Name</div>
                                                                                                <p>{{ $room->name }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Room Type</div>
                                                                                                <p>{{ $room->room_type }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Bed Type</div>
                                                                                                <p>{{ $room->bed_type }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">View</div>
                                                                                                <p>{{ $room->view }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Capacity (Adult)</div>
                                                                                                <p>{{ $room->guest_adult > 0 ? $room->guest_adult. " Guest" : "-" }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Capacity (Child)</div>
                                                                                                <p>{{ $room->guest_child > 0 ? $room->guest_child. " Guest" : "-" }}</p>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="card-subtitle">Size</div>
                                                                                                <p>{{ $room->size. " m²" }}</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    @if ($room->amenities != "")
                                                                                        <div class="row">
                                                                                            <div class="col-md-12">
                                                                                                <hr class="form-hr">
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="card-subtitle">Amenities</div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <p class="m-b-8">
                                                                                                    <b>English:</b><br>
                                                                                                    {!! $room->amenities !!}
                                                                                                </p>
                                                                                                @if ($room->amenities_traditional)
                                                                                                    <p class="m-b-8">
                                                                                                        <b>Chinese Traditional:</b><br>
                                                                                                        {!! $room->amenities_traditional !!}
                                                                                                    </p>
                                                                                                @endif
                                                                                                @if ($room->amenities_simplified)
                                                                                                    <p class="m-b-8">
                                                                                                        <b>Chinese Simplified:</b><br>
                                                                                                        {!! $room->amenities_simplified !!}
                                                                                                    </p>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    @if ($room->description != "")
                                                                                        <div class="row">
                                                                                            <div class="col-md-12">
                                                                                                <hr class="form-hr">
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="card-subtitle">Description</div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <p class="m-b-8">
                                                                                                    <b>English:</b><br>
                                                                                                    {!! $room->description !!}
                                                                                                </p>
                                                                                                @if ($room->description_traditional)
                                                                                                    <p class="m-b-8">
                                                                                                        <b>Chinese Traditional:</b><br>
                                                                                                        {!! $room->description_traditional !!}
                                                                                                    </p>
                                                                                                @endif
                                                                                                @if ($room->description_simplified)
                                                                                                    <p class="m-b-8">
                                                                                                        <b>Chinese Simplified:</b><br>
                                                                                                        {!! $room->description_simplified !!}
                                                                                                    </p>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        @canany(['posDev','posAuthor'])
                                                                            <a href="{{ route('view.edit-villa-room',$room->id) }}">
                                                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                            </a>
                                                                        @endcanany
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="notif">!!! The villa doesn't have a rooms yet, please add a rooms now!</div>
                                        @endif
                                        @canany(['posDev','posAuthor'])
                                            <div class="card-box-footer">
                                                <a href="{{ route('admin.villa-room.add',$villa->id) }}">
                                                    <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Rooms</button>
                                                </a>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
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
                                                <p><b>{{ $villa->name }}</b></p>
                                            </div>
                                            <div class="col-6">
                                                <p><b>{{ dateTimeFormat($villa->created_at) }}</b></p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-6">
                                                <p><b>Author :</b> {{ $author?->name }}</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><i>{{ Carbon::parse($villa->created_at)->diffForHumans();  }}</i></p>
                                            </div>
                                            <div class="col-md-12">
                                                <p><b>Rooms :</b> {{ count($villa->rooms)." rooms" }}</p>
                                                @php
                                                    $last_price = $villa->prices->where('end_date','>', $now);
                                                    $clp = count($last_price);
                                                    $end_date = $now;
                                                    $hi = $now;
                                                @endphp
                                                @foreach ($villa->prices as $lprices)
                                                
                                                    @php
                                                        $ed = $lprices->end_date;
                                                    @endphp
                                                    @if ($ed > $hi)
                                                        @php
                                                            $end_date = $ed;
                                                            $hi = $ed;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                @if ($end_date > date('Y-m-d',strtotime($now)))
                                                    <p><b>Last Price :</b> {{ dateFormat($end_date) }}</p>
                                                @else
                                                    <p style="color:red;">Expired</p>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.villas.partials.additional-services')
                    @include('admin.villas.partials.villa-prices')
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
    @include('partials.loading-form', ['id' => 'removeContract'])
@endsection
<script>
    function searchPriceByYears() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceByYears");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPrice");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
            } else {
            tr[i].style.display = "none";
            }
        }       
        }
    }
</script>

