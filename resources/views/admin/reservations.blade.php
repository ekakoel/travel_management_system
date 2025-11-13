@section('title', __('messages.Reservation'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="icon-copy fa fa-shopping-cart" aria-hidden="true"></i> Reservation
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Reservation</li>
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
                {{-- ACTIVE RESERVATION ============================================================================================================================= --}}
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box p-b-18 mb-30">
                            <div class="card-box-title">
                                <div class="subtitle">Reservation</div>
                            </div>
                            <table id="tbInv" class="data-table-pagination table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 25%;">Reservation No</th>
                                        <th style="width: 25%;">Invoice</th>
                                        <th style="width: 25%;">Due Date</th>
                                        <th style="width: 10%;">Status</th>
                                        <th class="datatable-nosort" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $no => $rsv)
                                        @if (count($guests) > 0)
                                            @php
                                                $agent_id = (int)$rsv->agn_id;
                                                $agent = $agents->where('id', $agent_id)->first();
                                                $guest = $guests->where('rsv_id', $rsv->id);
                                                $cgn = count($guest);
                                                $inv = $invoices->where('rsv_id', $rsv->id)->first();
                                            @endphp
                                        @endif
                                        <tr>
                                            <td>
                                                <p class="font-16">{{ ++$no }}</p>
                                            </td>
                                            <td>
                                                <p class="font-16">{{ $rsv->rsv_no }}</p>
                                            </td>
                                            <td>
                                                @if (isset($inv))
                                                    <p>{{ $inv->inv_no }}</p>
                                                @else
                                                    <p>-</p>
                                                @endif
                                            </td>
                                            @if (isset($inv))
                                                @if ($inv->due_date < $now)
                                                    <td class="background-due-date-expired">
                                                @else
                                                    <td>
                                                @endif
                                                {{ date('D, d M y', strtotime($inv->due_date)) }}
                                            @else
                                                <td>-</td>
                                            @endif
                                            <td style="text-align: left;">
                                                @if ($rsv->status == "Active")
                                                    <div class="status-active"></div>
                                                @elseif ($rsv->status == "Draft")
                                                    <div class="status-draft"></div>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="table-action">
                                                    <a href="/reservation-{{ $rsv->id }}">
                                                        <button class="btn-view" data-toggle="tooltip" data-placement="top"
                                                            title="Detail Reservation"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                                    </a>
                                                    <form action="/fdelete-rsv/{{ $rsv->id }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit"
                                                            data-toggle="tooltip" data-placement="left" title="Delete {{ $rsv->rsv_no }}"><i
                                                            class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="card-box-footer">
                                <a href="#" data-toggle="modal" data-target="#add-reservation">
                                    <button class="btn btn-primary"><i class="ion-plus-round"></i> Create Reservation</button>
                                </a>
                                {{-- Modal Add Reservation --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="add-reservation" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Reservation</div>
                                                </div>
                                                <div class="modal-body pd-5">
                                                    <form id="create-reservation" action="/fadd-reservation" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="agn_id">Agent <span>*</span></label><br>
                                                                    <select id="agn_id" name="agn_id" style="width: 100%" class="custom-select @error('agn_id') is-invalid @enderror" required>
                                                                        <option selected value="">Select Agent</option>
                                                                        @foreach ($agents as $agent)
                                                                            <option value="{{ $agent->id }}">{{ $agent->name ." @". $agent->office }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('agn_id')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="checkincout">@lang('messages.Check In') - @lang('messages.Check Out')</label>
                                                                    <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror"
                                                                        type="text" value="{{ old('checkincout') }}" placeholder="@lang('messages.Select date')" required>
                                                                    @error('checkincout')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <input type="hidden" name="action" value="Create Reservation">
                                                        <input type="hidden" name="service" value="Reservation">
                                                        <input type="hidden" name="page" value="reservation">
                                                    </form>
                                                </div>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="create-reservation" class="btn btn-primary"><i class="icon-copy fa fa-check"
                                                            aria-hidden="true"></i> Create</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection