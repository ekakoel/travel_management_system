@section('title', __('messages.Airport Shuttle'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-car"></i>&nbsp; Add or Edit Airport Shuttle</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders">Order</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Airport Shuttle</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Add or Edit Airport Shuttle
                                </div>
                            </div>
                            <table class="data-table table nowrap" >
                                <thead>
                                    <tr>
                                        <th>@lang('messages.No')</th>
                                        <th>@lang('messages.Date')</th>
                                        <th>@lang('messages.Service')</th>
                                        <th>@lang('messages.Transport')</th>
                                        <th>@lang('messages.Src') <=> @lang('messages.Dst') </th>
                                        <th>@lang('messages.Duration')</th>
                                        <th>@lang('messages.Distance')</th>
                                        <th>@lang('messages.Price')</th>
                                        <th>@lang('messages.Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airport_shuttles as $num_airport=>$airport_shuttle)
                                        <tr>
                                            <td>{{ ++$num_airport }}</td>
                                            <td>{{ dateTimeFormat($airport_shuttle->date) }}</td>
                                            <td><p>Airport Shuttle</p></td>
                                            <td>{{ $airport_shuttle->transport?->brand." ".$airport_shuttle->transport?->name }}</td>
                                            <td>{{ $airport_shuttle->src." <=> ".$airport_shuttle->dst }}</td>
                                            <td>{{ $airport_shuttle->duration }}</td>
                                            <td>{{ $airport_shuttle->distance }}</td>
                                            <td>{{ currencyFormatUsd($airport_shuttle->price) }}</td>
                                            <td class="text-right">
                                                <div class="table-action">
                                                   
                                                    <a href="#" data-toggle="modal" data-target="#edit-airport-shuttle-{{ $airport_shuttle->id }}">
                                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                    </a>
                                                    <form class="display-content" action="/fremove-airport-shuttle/{{ $airport_shuttle->id }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                   
                                                </div>
                                            </td>
                                            
                                        </tr>
                                        {{-- MODAL EDIT AIRPORT SHUTTLE --}}
                                        <div class="modal fade" id="edit-airport-shuttle-{{ $airport_shuttle->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content" >
                                                    
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Airport Shuttle</div>
                                                            </div>
                                                            
                                                                <form id="fedit-airport-shuttle-{{ $airport_shuttle->id }}" action="/fedit-airport-shuttles-{{ $airport_shuttle->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            
                                                                                <div class="control-group">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="flight_number">Flight Number</label>
                                                                                                <input type="text" name="flight_number" class="form-control @error('flight_number') is-invalid @enderror" value="{{ $airport_shuttle->flight_number }}" required>
                                                                                                @error('flight_number')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="date">Date</label>
                                                                                                <input readonly type="text" name="date" class="form-control datetimepicker @error('date') is-invalid @enderror" value="{{ date('d F Y (H:i A)',strtotime($airport_shuttle->date)) }}" required>
                                                                                                @error('date')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="src">Source <span>*</span></label>
                                                                                                <select type="text" name="src" class="custom-select @error('src') is-invalid @enderror" required>
                                                                                                    <option selected value="{{ $airport_shuttle->src }}">{{ $airport_shuttle->src }}</option>
                                                                                                    <option value="Airport">Airport</option>
                                                                                                    <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                                                                                </select>
                                                                                                @error('src')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="dst">Destination <span>*</span></label>
                                                                                                <select type="text" name="dst" class="custom-select @error('dst') is-invalid @enderror" required>
                                                                                                    <option value="{{ $airport_shuttle->dst }}">{{ $airport_shuttle->dst }}</option>
                                                                                                    <option value="Airport">Airport</option>
                                                                                                    <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                                                                                </select>
                                                                                                @error('dst')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="transport">Transport <span> *</span></label>
                                                                                                <select id="transport" name="transport" class="custom-select m-0 @error('transport') is-invalid @enderror" required>
                                                                                                    {{-- <option selected value="{{ $airport_shuttle->transport }}">{{ $airport_shuttle->transport }}</option> --}}
                                                                                                    @foreach ($transports as $transport)
                                                                                                        <option {{ $airport_shuttle->transport?->id == $transport->id ? "selected":""; }} value="{{ $transport->id }}">{{ $transport->name." - ".$transport->capacity }} <i> Seats</i></option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('transport')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group">
                                                                                                <label for="duration">Duration</label>
                                                                                                <input type="text" readonly name="duration"  class="form-control @error('duration') is-invalid @enderror"value="{{ $hotel->airport_duration }}">
                                                                                                @error('duration')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group">
                                                                                                <label for="distance">Distance</label>
                                                                                                <input type="text" readonly name="distance"  class="form-control @error('distance') is-invalid @enderror"value="{{ $hotel->airport_distance }}">
                                                                                                @error('distance')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group">
                                                                                                <label for="price">Price</label>
                                                                                                <input type="number" name="price" min="0" class="form-control @error('price') is-invalid @enderror" placeholder="$" value="{{ $airport_shuttle->price }}">
                                                                                                @error('price')
                                                                                                    <div class="alert alert-danger">
                                                                                                        {{ $message }}
                                                                                                    </div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                    
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                </form>
                                                            
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="fedit-airport-shuttle-{{ $airport_shuttle->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- <form id="edit-airport-shuttle" action="/fedit-airport-shuttles-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put') --}}
                               
                            
                            <div class="card-box-footer">
                                <a href="#" data-toggle="modal" data-target="#add-airport-shuttle">
                                    <button type="button" class="btn btn-primary "><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Airport Shuttle</button>
                                </a>
                                <a href="/orders-admin-{{ $order->id }}#optional_service">
                                    <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                </a>
                            </div>
                            
                            {{-- MODAL ADD AIRPORT SHUTTLE --}}
                            <div class="modal fade" id="add-airport-shuttle" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content" >
                                        
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Airport Shuttle</div>
                                                </div>
                                                
                                                    <form id="fadd-airport-shuttle" action="/fadd-airport-shuttle" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                
                                                                    <div class="control-group">
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="flight_number">Flight Number</label>
                                                                                    <input type="text" name="flight_number" class="form-control @error('flight_number') is-invalid @enderror" value="{{ old('flight_number') }}" required>
                                                                                    @error('flight_number')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="date">Date</label>
                                                                                    <input readonly type="text" name="date" class="form-control datetimepicker @error('date') is-invalid @enderror" placeholder="Select date" value="{{ old('date') }}" required>
                                                                                    @error('date')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="src">Source <span>*</span></label>
                                                                                    <select type="text" name="src" class="custom-select @error('src') is-invalid @enderror" required>
                                                                                        <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                                                                        <option value="Airport">Airport</option>
                                                                                    </select>
                                                                                    @error('src')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="dst">Destination <span>*</span></label>
                                                                                    <select type="text" name="dst" class="custom-select @error('dst') is-invalid @enderror" required>
                                                                                        <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                                                                        <option value="Airport">Airport</option>
                                                                                    </select>
                                                                                    @error('dst')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="transport">Transport <span> *</span></label>
                                                                                    <select id="transport" name="transport" class="custom-select m-0 @error('transport') is-invalid @enderror" required>
                                                                                        <option selected value="">Select Transport</option>
                                                                                        @foreach ($transports as $transport)
                                                                                            <option value="{{ $transport->id }}">{{ $transport->name." - ".$transport->capacity }} <i> Seats</i></option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('transport')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-3">
                                                                                <div class="form-group">
                                                                                    <label for="duration">Duration</label>
                                                                                    <input type="text" readonly name="duration"  class="form-control @error('duration') is-invalid @enderror"value="{{ $hotel->airport_duration }}">
                                                                                    @error('duration')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-3">
                                                                                <div class="form-group">
                                                                                    <label for="distance">Distance</label>
                                                                                    <input type="text" readonly name="distance"  class="form-control @error('distance') is-invalid @enderror"value="{{ $hotel->airport_distance }}">
                                                                                    @error('distance')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="price">Price</label>
                                                                                    <input type="number" name="price" min="0" class="form-control @error('price') is-invalid @enderror" placeholder="$" value="{{ old('price') }}">
                                                                                    @error('price')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                        
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                    </form>
                                                
                                                <div class="card-box-footer">
                                                    <button type="submit" form="fadd-airport-shuttle" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
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
    </div>
@endsection