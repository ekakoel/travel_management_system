@section('title', __('messages.Edit Reservation'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-7 col-sm-7">
                            <div class="title">
                                <h4>Edit- Reservation {{ $reservation->rsv_no }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="reservation">Reservation</a></li>
                                    <li class="breadcrumb-item"><a href="page-{{ $reservation->id }}">Reservation Detail</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ 'Edit - '. $reservation->rsv_no }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-5 col-sm-5 text-right">
                            Status: 
                            @if ($reservation->status == "On Progress")
                                <h4 style="color: green">{{ $reservation->status }}</h4>
                            @else
                                <h4 style="color: grey">{{ $reservation->status }}</h4>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="product-wrap card-box mb-30">
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-heading">Edit Reservation</div>
                                    </div>
                                    <div class="col-md-12">
                                        <form action="/fupdate-reservation/{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <div class="row">
                                                <div class="col-12">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-4">
                                                    <div class="page-list"> Reservation No </div>
                                                    <div class="page-list"> Reservation Date </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="page-list-value">
                                                        {{  $reservation->rsv_no }}
                                                    </div>
                                                    <div class="page-list-value">
                                                        {{ date('D, d-M-Y (H.i)', strtotime($reservation->created_at)) }}
                                                    </div>
                                                </div> 
                                                
                                                
                                                <div class="col-sm-12">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="agn_id" class="col-sm-12 col-md-12 col-form-label">Agent Name <span>*</span></label>
                                                        <div class="col-sm-12">
                                                            <select id="agn_id" name="agn_id" class="custom-select col-12 @error('agn_id') is-invalid @enderror" required>
                                                                <option selected value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                                @foreach ($agents as $agents)
                                                                    <option value="{{ $agents->id }}">{{ $agents->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('agn_id')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="status" class="col-sm-12 col-md-12 col-form-label">Status <span>*</span></label>
                                                        <div class="col-sm-12">
                                                            <select id="status" name="status"
                                                                class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                <option selected="{{ $reservation->status }}">{{ $reservation->status }}</option>
                                                                <option value="On Progress">On Progress</option>
                                                                <option value="Archived">Archived</option>
                                                            </select>
                                                            @error('status')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="page-subtitle">Guest Detail</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="no_of_gst" class="col-sm-12 col-md-12 col-form-label">Number Of Guest <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="number" id="no_of_gst" name="no_of_gst" class="form-control @error('no_of_gst') is-invalid @enderror" placeholder="Ex: 2" value="{{ $reservation->no_of_gst }}" required>
                                                                @error('no_of_gst')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="gst_name" class="col-sm-12 col-md-12 col-form-label">Guest Name<span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" id="gst_name" name="gst_name" class="form-control @error('gst_name') is-invalid @enderror" placeholder="Insert guest name" value="{{ $reservation->gst_name }}" required>
                                                                @error('gst_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="gst_phone" class="col-sm-12 col-md-12 col-form-label">Guest Phone <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="number" id="gst_phone" name="gst_phone" class="form-control @error('gst_phone') is-invalid @enderror" placeholder="Insert guest phone" value="{{ $reservation->gst_phone }}" required>
                                                                @error('gst_phone')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="page-subtitle">Flight Detail</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="arrival_flight" class="col-sm-12 col-md-12 col-form-label">Arrival Flight <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" id="arrival_flight" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="Insert arrival flight" value="{{ $reservation->arrival_flight }}" required>
                                                                @error('arrival_flight')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="arrival_time" class="col-sm-12 col-md-12 col-form-label">Arrival Time <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" name="arrival_time" class="form-control time-picker-default @error('arrival_time') is-invalid @enderror" placeholder="Hotel Name" value="{{ $reservation->arrival_time }}" required>
                                                            @error('arrival_time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="departure_flight" class="col-sm-12 col-md-12 col-form-label">Departure Flight <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" id="departure_flight" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="Insert arrival flight" value="{{ $reservation->departure_flight }}" required>
                                                                @error('departure_flight')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="departure_time" class="col-sm-12 col-md-12 col-form-label">Departure Time <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" name="departure_time" class="form-control time-picker-default @error('departure_time') is-invalid @enderror" placeholder="Hotel Name" value="{{ $reservation->departure_time }}" required>
                                                            @error('departure_time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="pickup_date" class="col-sm-12 col-md-12 col-form-label">Pickup Date <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input name="pickup_date" id="pickup_date" wire:model="pickup_date" class="form-control date-picker @error('pickup_date') is-invalid @enderror" type="text" value="{{ date('d M Y', strtotime($reservation->pickup_date)) }}"required>
                                                            @error('pickup_date')
                                                                <div class="alert alert-danger">
                                                                    <strong>{{ $message }}</strong>
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="pickup_time" class="col-sm-12 col-md-12 col-form-label">Pickup Time <span>*</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <input type="text" name="pickup_time" class="form-control time-picker-default @error('pickup_time') is-invalid @enderror" placeholder="Pickup Time" value="{{ $reservation->pickup_time }}" required>
                                                            @error('pickup_time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="page-subtitle">Guide Detail</div>
                                                </div>
                                                {{-- <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label for="guide_id" class="col-sm-12 col-md-12 col-form-label">Guide Name <span>*</span></label>
                                                        <div class="col-sm-12">
                                                            <select id="guide_id" name="guide_id"
                                                                class="custom-select col-12 @error('guide_id') is-invalid @enderror" required>
                                                                <option selected value="{{ $reservation->guide->id }}">{{ $reservation->guide->name }}</option>
                                                                @foreach ($guide as $guide)
                                                                    <option valude="{{ $guide->id }}">{{ $guide->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('guide_id')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="col-12">
                                                    <div class="page-subtitle">Note</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="msg" class="col-sm-12 col-md-12 col-form-label">Note</label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <textarea id="msg" name="msg" class="textarea_editor form-control border-radius-0" placeholder="Enter your notes here">{{ $reservation->msg }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- ACTION LOG ----------------------------------------------------}}
                                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                <input type="hidden" name="action" value="Update">
                                                <input type="hidden" name="service" value="Reservation">
                                                <input type="hidden" name="page" value="Reservation Edit">
                                                <input type="hidden" name="note" value="{{ $reservation }}">
                                                {{-- END ACTION LOG ----------------------------------------------------}}
                                                <div class="col-sm-12 col-md-12 text-right">
                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                    <a href="/reservation-{{ $reservation->id }}"><button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i>Cancel</button></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                               
                            
                            
                             
                        </div>
                    </div>
					<div class="col-md-4">
                        <div class="product-wrap card-box mb-30">
                            <b>Attention!</b> <br>
                            <i>1. On this page you can make changes to an existing reservation</i><br>
                            <i>2. Make sure all data is correct and valid!</i><br>
                            <i>3. After all the data is filled correctly then you can Update the reservation</i><br>
                            <i>4. Any changes that occur will be recorded to find out the history of the reservation</i>
                        </div>
                        @if ($reservation->msg != "")
                            <div class="product-wrap card-box mb-30">
                                <b>Admin Notes</b> <br>
                                <div class="trackrecord">
                                    <p>{{ $reservation->msg }}</p>
                                </div>
                            </div>
                        @else
                        @endif
                    </div>
					
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
    @endcan
@endsection
