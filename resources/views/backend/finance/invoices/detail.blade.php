@php
    $agent = Auth::User()->where('id',$reservation->agn_id)->first();
    $from = date('Y-m-d',strtotime($reservation->checkin));
    $dur = $dur_res + 1;
    $date_stay = [];
    for ($a=0; $a < $dur ; $a++) { 
        $date_stay[$a] = $from;
        $from = date('Y-m-d',strtotime('+1 days',strtotime($from)));
    }
@endphp
@section('title', __('messages.Invoice Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-file-text-o" aria-hidden="true"></i> @lang('messages.Invoice')
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">@lang('messages.Admin Panel')</a></li>
                                <li class="breadcrumb-item"><a href="/reservation">@lang('messages.Reservation')</a></li>
                                <li class="breadcrumb-item"><a href="/reservation-{{ $reservation->id }}">{{ $reservation->rsv_no }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $invoice->inv_no }}</li>
                            </ol>
                        </nav>
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
                    <div class="product-wrap">
                        <div class="product-detail-wrap">
                            <div class="row">
                                {{-- ATTENTIONS --}}
                                <div class="col-md-4 mobile">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                </div>
                                <div class="col-md-8 m-b-18">
                                    <div class="card-box p-b-18">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="page-subtitle">@lang('messages.Invoice')</div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-5">
                                                                <div class="page-list">@lang('messages.Invoice No')</div>
                                                                <div class="page-list">@lang('messages.Invoice Date')</div>
                                                            </div>
                                                            <div class="col-7">
                                                                <div class="page-list">{{ ": " .$invoice->inv_no }}</div>
                                                                <div class="page-list">{{ ": " .dateFormat($invoice->created_at) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 m-b-18">
                                                        <div class="row">
                                                            <div class="col-5">
                                                                <div class="page-list">@lang('messages.Guest Name')</div>
                                                                <div class="page-list">@lang('messages.Phone')</div>
                                                                <div class="page-list">@lang('messages.Agent')</div>
                                                                <div class="page-list">@lang('messages.Company / Office')</div>
                                                                <div class="page-list">@lang('messages.Contact Number')</div>
                                                            </div>
                                                            <div class="col-7">
                                                                @if ($reservation->pickup_name == "")
                                                                    <div class="page-list">: -</div>
                                                                @else
                                                                    <div class="page-list">{{ ": " .$reservation->pickup_name }}</div>
                                                                @endif
                                                                @if ($reservation->phone == "")
                                                                    <div class="page-list">: -</div>
                                                                @else
                                                                    <div class="page-list">{{ ": " .$reservation->phone }}</div>
                                                                @endif
                                                                <div class="page-list">{{ ": " .$agent->name }}</div>
                                                                <div class="page-list">{{ ": " .$agent->office }}</div>
                                                                <div class="page-list">{{ ": " .$agent->phone }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <table class="data-table table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%" scope="col">No</th>
                                                                    <th style="width: 45%" scope="col">Description</th>
                                                                    <th style="width: 10%" scope="col">Rate</th>
                                                                    <th style="width: 10%" scope="col">Unit/Pax</th>
                                                                    <th style="width: 10%" scope="col">Night/Times</th>
                                                                    <th style="width: 10%" scope="col">Amount</th>
                                                                    @if ($reservation->status != "Active")
                                                                        <th style="width: 10%" scope="col">Action</th>
                                                                    @endif
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($accommodations as $no=>$acc)
                                                                    <tr>
                                                                        <td>
                                                                            <p>{{ ++$no }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{{ $acc->servicename." ".$acc->location." ".date('d M Y',strtotime($acc->checkin))." - ".date('d M Y',strtotime($acc->checkout))." ".$acc->subservice }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd(($acc->final_price / $acc->duration)/$acc->number_of_room) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $acc->number_of_room }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $acc->duration }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($acc->final_price) }}</p>
                                                                        </td>
                                                                        @if ($reservation->status != "Active")
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                                @foreach ($tours as $tour)
                                                                    <tr>
                                                                        <td>
                                                                            <p>{{ ++$no }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{{ $tour->service." ".$tour->subservice." ".date('d M Y',strtotime($tour->checkin))." - ".date('d M Y',strtotime($tour->checkout)) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($tour->price_pax) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $tour->number_of_guests }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>1</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($tour->final_price) }}</p>
                                                                        </td>
                                                                        @if ($reservation->status != "Active")
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                                @foreach ($activities as $activity)
                                                                    <tr>
                                                                        <td>
                                                                            <p>{{ ++$no }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{{ $activity->service." ".$activity->location." ".date('d M Y',strtotime($activity->checkin))." ".$activity->subservice }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($activity->price_pax) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $activity->number_of_guests }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>1</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($activity->final_price) }}</p>
                                                                        </td>
                                                                        @if ($reservation->status != "Active")
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                                @foreach ($transports as $transport)
                                                                    <tr>
                                                                        <td>
                                                                            <p>{{ ++$no }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{{ $transport->service." ".$transport->service_type." ".date('d M Y',strtotime($transport->checkin))." ".$transport->servicename." ".$transport->capacity." Seat" }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($transport->price_pax) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>1</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>1</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($transport->final_price) }}</p>
                                                                        </td>
                                                                        @if ($reservation->status != "Active")
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                                @foreach ($additional_invoice as $additionalinv)
                                                                    <tr>
                                                                        <td>
                                                                            <p>{{ ++$no }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{{ $additionalinv->description." ".dateFormat($additionalinv->date) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($additionalinv->rate) }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $additionalinv->unit }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ $additionalinv->times }}</p>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <p>{{ currencyFormatUsd($additionalinv->amount) }}</p>
                                                                        </td>
                                                                        @if ($reservation->status != "Active")
                                                                            <td>
                                                                                <div class="reservation-guest">
                                                                                    <span>
                                                                                        <a href="#" data-toggle="modal" data-target="#edit-additional-inv-{{ $additionalinv->id }}"> 
                                                                                            <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                                        </a>
                                                                                        <form action="/delete-additional-inv/{{ $additionalinv->id }}" method="post">
                                                                                            @csrf
                                                                                            @method('delete')
                                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $additionalinv->description }}"><i class="icon-copy fa fa-trash"></i></button>
                                                                                        </form>
                                                                                    </span>
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                    <div class="modal fade" id="edit-additional-inv-{{ $additionalinv->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content text-left">
                                                                                <div class="product-detail-wrap">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Update Service</div>
                                                                                        </div>
                                                                                        <div class="col-12 text-right">
                                                                                            <hr class="form-hr">
                                                                                        </div>
                                                                                    </div>
                                                                                    <form action="/fupdate-additional-inv/{{ $additionalinv->id }}" method="post" enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        @method('put')
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="date">Date <span>*</span></label>
                                                                                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                                                        <option selected value="{{ date('Y-m-d',strtotime($additionalinv->date)) }}">{{ date('d M Y',strtotime($additionalinv->date)) }}</option>
                                                                                                        @foreach ($date_stay as $datestay)
                                                                                                            <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y',strtotime($datestay)) }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-8">
                                                                                                <div class="form-group row">
                                                                                                    <label for="description" class="col-sm-12 col-md-12 col-form-label">Description <span>*</span></label>
                                                                                                    <div class="col-sm-12">
                                                                                                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ $additionalinv->description }}" required>
                                                                                                    </div>
                                                                                                    @error('description')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="form-group row">
                                                                                                    <label for="rate" class="col-sm-12 col-md-12 col-form-label">Rate </label>
                                                                                                    <div class="col-sm-12">
                                                                                                    <input type="number" min=1 name="rate" class="form-control @error('rate') is-invalid @enderror" placeholder="Insert USD rate" value="{{ $additionalinv->rate }}">
                                                                                                    </div>
                                                                                                    @error('rate')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="form-group row">
                                                                                                    <label for="unit" class="col-sm-12 col-md-12 col-form-label">Unit/Pax </label>
                                                                                                    <div class="col-sm-12">
                                                                                                    <input type="number" min=1 name="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="Insert unit or pax" value="{{ $additionalinv->unit }}">
                                                                                                    </div>
                                                                                                    @error('unit')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-4">
                                                                                                <div class="form-group row">
                                                                                                    <label for="times" class="col-sm-12 col-md-12 col-form-label">Night/Times </label>
                                                                                                    <div class="col-sm-12">
                                                                                                    <input type="number" min=1 name="times" class="form-control @error('times') is-invalid @enderror" placeholder="insert night or times" value="{{ $additionalinv->times }}">
                                                                                                    </div>
                                                                                                    @error('times')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                            <div class="col-sm-12 col-md-12 text-right">
                                                                                                <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                       {{-- {{ "Total Price: $". number_format($total_price_order) }} --}}
                                                    </div>
                                                    @if ($reservation->status != "Active")
                                                        <div class="col-md-12 text-right"> 
                                                            <a href="#" data-toggle="modal" data-target="#add-additional-inv"> 
                                                                <button class="btn btn-primary" data-toggle="tooltip" data-placement="left" title="Add Additional Invoice"><i class="icon-copy fa fa-plus"></i> Add</button>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                    @endif
                                                    {{-- MODAL ADD ADDITIONAL INVOICE ========================================================================================================--}}
                                                    <div class="modal fade" id="add-additional-inv" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="product-detail-wrap">
                                                                    
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guest</div>
                                                                            </div>
                                                                            <div class="col-12 text-right">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                        </div>
                                                                        <form action="/fadd-additional-inv" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group">
                                                                                        <label for="date">Date <span>*</span></label>
                                                                                        <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                                            <option selected value="">Select Date</option>
                                                                                            @foreach ($date_stay as $datestay)
                                                                                                <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y',strtotime($datestay)) }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-8">
                                                                                    <div class="form-group row">
                                                                                        <label for="description" class="col-sm-12 col-md-12 col-form-label">Description <span>*</span></label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ old('description') }}" required>
                                                                                        </div>
                                                                                        @error('description')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group row">
                                                                                        <label for="rate" class="col-sm-12 col-md-12 col-form-label">Rate </label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="number" min=1 name="rate" class="form-control @error('rate') is-invalid @enderror" placeholder="Insert rate" value="{{ old('rate') }}">
                                                                                        </div>
                                                                                        @error('rate')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group row">
                                                                                        <label for="unit" class="col-sm-12 col-md-12 col-form-label">Unit/Pax </label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="number" min=1 name="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="Insert unit or pax" value="{{ old('unit') }}">
                                                                                        </div>
                                                                                        @error('unit')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group row">
                                                                                        <label for="times" class="col-sm-12 col-md-12 col-form-label">Night/Times </label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="number" min=1 name="times" class="form-control @error('times') is-invalid @enderror" placeholder="insert night or times" value="{{ old('times') }}">
                                                                                        </div>
                                                                                        @error('times')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class="col-sm-12 col-md-12 text-right">
                                                                                    <input type="hidden" name="inv_id" value="{{ $invoice->id }}">
                                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 m-t-18">
                                                        <div class="row">
                                                            <div class="col-6 col-md-6">
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right det-price">Total Invoice</div>
                                                                    <div class="col-md-12 text-right det-list">Total IDR</div>
                                                                    <div class="col-md-12 text-right det-list">Total USD</div>
                                                                    <div class="col-md-12 text-right det-list">Tax IDR</div>
                                                                    <div class="col-md-12 text-right det-list">Tax USD</div>
                                                                    <div class="col-md-12 text-right det-list">Total IDR Plus Tax</div>
                                                                    <div class="col-md-12 text-right det-list">Total USD Plus Tax</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 col-md-4">
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right det-list"><br></div>
                                                                    <div class="col-md-12 text-right det-list">IDR</div>
                                                                    <div class="col-md-12 text-right det-list">USD</div>
                                                                    <div class="col-md-12 text-right det-list">IDR</div>
                                                                    <div class="col-md-12 text-right det-list">USD</div>
                                                                    <div class="col-md-12 text-right det-list">IDR</div>
                                                                    <div class="col-md-12 text-right det-list">USD</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 col-md-2">
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right det-list"><br></div>
                                                                    <div class="col-md-12 text-right det-list">0.00</div>
                                                                    <div class="col-md-12 text-right det-list">{{ number_format($total_price_order+$sum_additional_invoice) }}</div>
                                                                    <div class="col-md-12 text-right det-list">0.00</div>
                                                                    <div class="col-md-12 text-right det-list">0.00</div>
                                                                    <div class="col-md-12 text-right det-list">0.00</div>
                                                                    <div class="col-md-12 text-right det-list">{{ number_format($total_price_order+$sum_additional_invoice) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 m-t-18">
                                                @php
                                                    $bankaccount = $bank_acc->where('id',$invoice->bank_id)->first();
                                                @endphp
                                                <div class="bordered">
                                                    @if (isset($bankaccount))
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <div class="col-4">
                                                                        <p>Bank</p>
                                                                        <p>Name</p>
                                                                        <p>Account IDR</p>
                                                                        <p>Account USD</p>
                                                                    </div>
                                                                    <div class="col-8">
                                                                        <p>: {{ $bankaccount->bank }}</p>
                                                                        <p>: {{ $bankaccount->name }}</p>
                                                                        <p>: {{ $bankaccount->account_idr }}</p>
                                                                        <p>: {{ $bankaccount->account_usd }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <div class="col-4">
                                                                        <p>Address</p>
                                                                        <p>Swift Code</p>
                                                                        <p>Telephone</p>
                                                                    </div>
                                                                    <div class="col-8">
                                                                        <p>: {{ $bankaccount->address }}</p>
                                                                        <p>: {{ $bankaccount->swift_code }}</p>
                                                                        <p>: {{ $bankaccount->telephone }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="btn-edit-modal m-r-18">
                                                                @if ($reservation->status != "Active")
                                                                    <a href="#" data-toggle="modal" data-target="#edit-bank-{{ $invoice->id }}"> 
                                                                        <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                    </a>
                                                                @endif
                                                                <div class="modal fade" id="edit-bank-{{ $invoice->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="product-detail-wrap">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Select Bank Account</div>
                                                                                    </div>
                                                                                    <div class="col-12 text-right">
                                                                                        <hr class="form-hr">
                                                                                    </div>
                                                                                </div>
                                                                                <form action="/fupdate-invoice-bank/{{ $invoice->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-12">
                                                                                            <div class="form-group">
                                                                                                <label for="bank_id" class="col-sm-12 col-md-12 col-form-label">Select Bank Account <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="bank_id" class="custom-select @error('bank_id') is-invalid @enderror" placeholder="Select bank account" required>
                                                                                                        <option selected value="">Select bank account</option>
                                                                                                        @foreach ($bank_acc as $bankacc)
                                                                                                            <option value="{{ $bankacc->id }}">{{ $bankacc->name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-12 col-md-12 text-right">
                                                                                            <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Select</button>
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <p>Bank account not found, please add a bank account first!</p>
                                                        </div>
                                                        <div class="col-md-4 text-right">
                                                            @if ($reservation->status != "Active")
                                                                <a href="#" data-toggle="modal" data-target="#edit-bank-{{ $invoice->id }}"> 
                                                                    <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                </a>
                                                            @endif
                                                            <div class="modal fade" id="edit-bank-{{ $invoice->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="product-detail-wrap">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Select Bank Account</div>
                                                                                </div>
                                                                                <div class="col-12 text-right">
                                                                                    <hr class="form-hr">
                                                                                </div>
                                                                            </div>
                                                                            <form action="/fupdate-invoice-bank/{{ $invoice->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('put')
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <div class="form-group">
                                                                                            <label for="bank_id" class="col-sm-12 col-md-12 col-form-label">Select Bank Account <span>*</span></label>
                                                                                            <div class="col-sm-12">
                                                                                                <select name="bank_id" class="custom-select @error('bank_id') is-invalid @enderror" placeholder="Select bank account" required>
                                                                                                    <option selected value="">Select bank account</option>
                                                                                                    @foreach ($bank_acc as $bankacc)
                                                                                                        <option value="{{ $bankacc->id }}">{{ $bankacc->name }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12 col-md-12 text-right">
                                                                                        <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Select</button>
                                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12 m-t-8 text-right">
                                                <a href="/reservation-{{ $reservation->id }}"><button type="button" class="btn btn-dark"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ATTENTIONS --}}
                                <div class="col-md-4 desktop">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
