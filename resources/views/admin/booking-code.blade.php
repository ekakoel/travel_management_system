@section('title', __('messages.Booking Code'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="fa fa-calendar-check-o" aria-hidden="true"></i> Booking Code {{ Auth::user()->position }}
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Booking Code</li>
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
                        @if (\Session::has('invalid'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('invalid') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-4 mobile">
                            @if (isset($activebookingcodes) or isset($draft_bookingcodes) or isset($usedup_bookingcodes) or isset($pending_bookingcodes) or isset($invalid_bookingcodes) or isset($expired_bookingcodes))
                                <div class="counter-container">
                                    @if (count($activebookingcodes)>0)
                                        <div class="widget">
                                            <a href="#activebookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activebookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draft_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#draftbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($usedup_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#usedupbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-usedup">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $usedup_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Used up</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($pending_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#pendingbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($invalid_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#invalidbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($expired_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#expiredbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-expired">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $expired_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Expired</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($rejected_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#rejectedbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $rejected_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- ATTENTIONS --}}
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="activebookingcodes" class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>Booking Code</div>
                                </div>
                                @if (count($bookingcodes)>0)
                                    <div class="input-container">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchBookingCodeByName" type="text" onkeyup="searchBookingCodeByName()" class="form-control" name="search-bookingcode-byname" placeholder="Search by name">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchPartnerByCode" type="text" onkeyup="searchPartnerByCode()" class="form-control" name="search-bookingcode-location" placeholder="Search by location">
                                        </div>
                                    </div>
                                    <table id="tbPartners" class="data-table table stripe hover" >
                                        <thead>
                                            <tr>
                                                <th data-priority="2" class="datatable-nosort" style="width: 5%;">No</th>
                                                <th data-priority="1" style="width: 15%;">Name</th>
                                                <th data-priority="2" style="width: 15%;">Code</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 10%;">Expired Date</th>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">Discounts</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 10%;">amount</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 10%;">Used</th>
                                                <th data-priority="2" style="width: 10%;">Status</th>
                                                @canany(['posDev','posAuthor'])
                                                    <th data-priority="2" class="datatable-nosort" style="width: 15%;">Action</th>
                                                @endcanany
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bookingcodes as $no=>$bookingcode)
                                                @php
                                                    $will_be_expired = $now->diffInDays($bookingcode->expired_date);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $bookingcode->name }}</div>
                                                    </td>
                                                    @if ($bookingcode->status !== "Expired")
                                                        <td data-toggle="tooltip" data-placement="right" title="{{ "Expired in ".$will_be_expired." days" }}">
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="right" title="{{ "Expired on ".date('d M Y', strtotime($bookingcode->expired_date)) }}">
                                                    @endif
                                                        <p>{{ $bookingcode->code }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ date('d M y', strtotime($bookingcode->expired_date)) }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ '$ '. $bookingcode->discounts }}</p>
                                                    </td>
                                                    
                                                    <td>
                                                        <p>{{ $bookingcode->amount }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ $bookingcode->used }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($bookingcode->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($bookingcode->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @elseif ($bookingcode->status == "Usedup")
                                                            <div class="status-usedup"></div>
                                                        @elseif ($bookingcode->status == "Expired")
                                                            <div class="status-expired"></div>
                                                        @elseif ($bookingcode->status == "Pending")
                                                            <div class="status-pending"></div>
                                                        @elseif ($bookingcode->status == "Invalid")
                                                            <div class="status-invalid"></div>
                                                        @elseif ($bookingcode->status == "Rejected")
                                                            <div class="status-rejected"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    @canany(['posDev','posAuthor'])
                                                        <td class="text-right">
                                                            <div class="table-action">
                                                                <a href="#" data-toggle="modal" data-target="#edit-bookingcode-{{ $bookingcode->id }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/fremove-bookingcode/{{ $bookingcode->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                        {{-- MODAL EDIT BOOKING CODE --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="edit-bookingcode-{{ $bookingcode->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Booking Code</div>
                                                                        </div>
                                                                        <form id="update-bcode-{{ $bookingcode->id }}" action="fupdate-bookingcode-{{ $bookingcode->id }}" method="post" enctype="multipart/form-data">
                                                                            @method('put')
                                                                            {{ csrf_field() }}
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group">
                                                                                            <label for="name" class="form-label col-form-label">Name</label>
                                                                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $bookingcode->name }}" required>
                                                                                            @error('name')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group">
                                                                                            <label for="code" class="form-label col-form-label">Booking Code</label>
                                                                                            <input style="text-transform:uppercase"  type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Insert code" value="{{ $bookingcode->code }}" required>
                                                                                            @error('code')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group">
                                                                                            <label for="status" class="form-label col-form-label">Status</label>
                                                                                            <select name="status" id="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                                                                <option selected value="{{ $bookingcode->status }}">{{ $bookingcode->status }}</option>
                                                                                                <option value="Active">Active</option>
                                                                                                <option value="Draft">Draft</option>
                                                                                                <option value="Expired">Expired</option>
                                                                                            </select>
                                                                                            @error('status')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group row">
                                                                                            <label for="discounts" class="col-12 col-sm-12 col-md-12 col-form-label">Discounts</label>
                                                                                            <div class="col-md-12">
                                                                                                <div class="btn-icon">
                                                                                                    <span>$</span>
                                                                                                    <input type="number" min="1" id="discounts" name="discounts" class="input-icon form-control @error('discounts') is-invalid @enderror" placeholder="Insert discount" value="{{ $bookingcode->discounts }}">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group">
                                                                                            <label for="amount" class="form-label col-form-label">Amount</label>
                                                                                            <input type="number" min="0" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Insert amount" value="{{ $bookingcode->amount }}" required>
                                                                                            @error('amount')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4">
                                                                                        <div class="form-group">
                                                                                            <label for="expired_date" class="form-label col-form-label">Expired Date</label>
                                                                                            <input readonly type="text" id="expired_date" name="expired_date" class="form-control date-picker @error('expired_date') is-invalid @enderror" placeholder="Expired Date" value="{{ date('d M Y', strtotime($bookingcode->expired_date)) }}" required>
                                                                                            @error('expired_date')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <hr class="form-hr">
                                                                                <div class="form-notif">This form is used to update a booking code, Please ensure that all inputs are filled with accurate data before publishing.</div>
                                                                                <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="update-bcode-{{ $bookingcode->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcanany
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-xl-12">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> No Booking Code in this page, add one!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-bookingcode"><button class="btn btn-primary"><i class="ion-plus-round"></i> Create Booking Code</button></a>
                                    </div>
                                    {{-- MODAL ADD BOOKING CODE --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="add-bookingcode" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Booking Code</div>
                                                    </div>
                                                    <form id="create-bcode" action="{{ route('fadd-booking-code') }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        {{ csrf_field() }}
                                                            <div class="row">
                                                                <div class="col-12 col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="name">Name</label>
                                                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                                                        @error('name')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="code">Booking Code</label>
                                                                        <input style="text-transform:uppercase"  type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Insert code" value="{{ old('code') }}" required>
                                                                        @error('code')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="discounts">Discounts <span> *</span></label>
                                                                        <div class="btn-icon">
                                                                            <span>$</span>
                                                                            <input type="number" min="1" id="discounts" name="discounts" class="input-icon form-control @error('discounts') is-invalid @enderror" placeholder="Insert discount" value="{{ old("discounts") }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="amount">Amount</label>
                                                                        <input type="number" min="0" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Insert amount" value="{{ old('amount') }}" required>
                                                                        @error('amount')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="expired_date">Expired Date</label>
                                                                        <input readonly type="text" id="expired_date" name="expired_date" class="form-control date-picker @error('expired_date') is-invalid @enderror" placeholder="Expired Date" value="{{ old('expired_date') }}" required>
                                                                        @error('expired_date')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            
                                                        
                                                                
                                                                <div class="col-12 m-t-8">
                                                                    <hr class="form-hr">
                                                            
                                                                    <div class="form-notif">This form is used to generate a booking code, Please ensure that all inputs are filled with accurate data before publishing.</div>
                                                                
                                                                </div>
                                                                <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                            </div>
                                                            
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="create-bcode" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            @if (isset($activebookingcodes) or isset($draft_bookingcodes) or isset($usedup_bookingcodes) or isset($pending_bookingcodes) or isset($invalid_bookingcodes) or isset($Expired_bookingcodes))
                                <div class="counter-container">
                                    @if (count($activebookingcodes)>0)
                                        <div class="widget">
                                            <a href="#activebookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activebookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draft_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#draftbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($usedup_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#usedupbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-usedup">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $usedup_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Used up</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($pending_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#pendingbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($invalid_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#invalidbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($expired_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#expiredbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-expired">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $expired_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Expired</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($rejected_bookingcodes)>0)
                                        <div class="widget">
                                            <a href="#rejectedbookingcodes">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $rejected_bookingcodes->count() }} Code</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- ATTENTIONS --}}
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
function searchBookingCodeByName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchBookingCodeByName");
    filter = input.value.toUpperCase();
    table = document.getElementById("tbPartners");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
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
<script>
function searchPartnerByCode() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchPartnerByCode");
    filter = input.value.toUpperCase();
    table = document.getElementById("tbPartners");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
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