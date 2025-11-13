@section('title', __('messages.Edit Order Room'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-pencil"></i>&nbsp; Edit Rooms Order</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders">Order</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Rooms</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">

                                <div class="title">Suites and Villas</div>
                            </div>
                            @php
                                $nor = $order->number_of_room;
                                $nogr = json_decode($order->number_of_guests_room);
                                $guest_name = json_decode($order->guest_detail);
                                $guest_detail = json_decode($order->guest_detail);
                                $special_day = json_decode($order->special_day);
                                $special_date = json_decode($order->special_date);
                                $extra_bed = json_decode($order->extra_bed);
                                $extra_bed_id = json_decode($order->extra_bed_id);
                                $extra_bed_price = json_decode($order->extra_bed_price);
                                $price_pax = json_decode($order->price_pax);
                                $r=1;
                            @endphp

                            <form id="update-order-room" action="{{ route('func.admin-update-order-room',$order->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-12">
                                        <ol>
                                            @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" or $order->guest_detail == ""  )
                                            @else
                                                @for ($i = 0; $i < $nor; $i++)
                                                    <li class="m-b-8">
                                                        <div class="room-container{{ $nogr[$i] > $order->capacity && $extra_bed_id[$i] == 0 ? "-error":"" }} control-group">
                                                            @if ($i > 0)
                                                                <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                                            @endif
                                                            <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="subtitle">{{ $room->rooms }}</div>
                                                                    </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="number_of_guests_room[]">Number of Guests</label>
                                                                        <input type="number" min="1" max="{{ ($order->capacity / 2) + $order->capacity }}" name="number_of_guests_room[]" class="form-control m-0 @error('number_of_guests_room[]') is-invalid @enderror" placeholder="Number of guests" value="{{ $nogr[$i] }}" required>
                                                                        @error('number_of_guests_room[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <div class="form-group">
                                                                        <label for="guest_detail[]"><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="Child guests must include the age on the back of their name. ex: Children's Name(age)" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Guest Name  </label>
                                                                        <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="Separate names with commas" value="{{ $guest_name[$i] }}" required>
                                                                        @error('guest_detail[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="special_day[]"><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="If during your stay there are guests who have special days such as birthdays, aniversaries, and others" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Special Day </label>
                                                                        <input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="ex: Birthday" value="{{ $special_day[$i] }}">
                                                                        @error('special_day[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="special_date[]">Insert Date</label>
                                                                        @if ($special_date == "")
                                                                            <input type="text" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yy/mm/dd" value="">
                                                                        @else
                                                                            <input type="text" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yy/mm/dd" value="{{ $special_date[$i] }}">
                                                                        @endif
                                                                        @error('special_date[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4" style="place-self: padding-bottom: 6px;">
                                                                    @php
                                                                        $extra_bed_room = $extrabed;
                                                                        $extrabedroom = $extrabed->where('id',$extra_bed_id[$i])->first();
                                                                    @endphp
                                                                    <div class="form-group">
                                                                        <label for="extra_bed_id[]">Extra Bed <span> * </span> <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="Choose an extra bed if the room is occupied by more than 2 guests." class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                                        <select name="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required>
                                                                            @if ($extra_bed_id[$i] != 0)
                                                                                <option selected value="{{ $extra_bed_id[$i] }}">{{ $extrabedroom->name." (".$extrabedroom->type.") $ ".$extra_bed_price[$i] }}</option>
                                                                            @else
                                                                                <option value="0">None</option>
                                                                            @endif
                                                                            <option value="0">None</option>
                                                                            @foreach ($extra_bed_room as $eb)
                                                                                @php
                                                                                    $eb_cr = ceil($eb->contract_rate/$usdrates->rate) + $eb->markup;
                                                                                    $eb_price = (ceil($eb_cr * $tax->tax / 100) + $eb_cr) * $order->duration;
                                                                                @endphp
                                                                                <option value="{{ $eb->id }}">{{ $eb->name." (".$eb->type.") $ ".$eb_price }}</option>
                                                                            @endforeach
                                                                            
                                                                        </select>
                                                                        @error('extra_bed[]')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endfor
                                                <div class="after-add-more"></div>
                                            @endif
                                        </ol>
                                    </div>
                               
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            @if ($order->request_quotation == "Yes")
                                                <p class="m-t-8">
                                                    You have booked rooms for more than 8 units before and, we will contact you as soon as possible to confirm the order, after you submit this order, thank you.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                              <div class="col-md-12 text-right">
                                <button id="add" type="button" class="btn btn-primary add-more m-b-8"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More Room</button>
                              </div>
                                    <input type="hidden" name="status" value="Draft">
                                    <input type="hidden" name="price_pax" value="{{ $price_pax }}">
                                    <input type="hidden" name="request_quotation" value="{{ $order->request_quotation }}">
                                    <input type="hidden" id="vc_room" value="{{ $nor }}">
                                </div>
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="update-order-room" class="btn btn-primary m-b-8"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                <a href="/orders-admin-{{ $order->id }}#optional_service">
                                    <button type="button" class="btn btn-danger m-b-8"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </a>
                            </div>
                            <div class="copy hide">
                                <li class="m-b-8">
                                    <div class="room-container control-group">
                                        <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="subtitle">{{ $room->rooms }} </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="number_of_guests_room[]">Number of Guests</label>
                                                    <input type="number" min="1" max="{{ ($order->capacity / 2) + $order->capacity }}" name="number_of_guests_room[]" class="form-control m-0 @error('number_of_guests_room[]') is-invalid @enderror" placeholder="Number of guests" required>
                                                    @error('number_of_guests_room[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="form-group">
                                                    <label for="guest_detail[]"><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="Child guests must include the age on the back of their name. ex: Children's Name(age)" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Guest Name  </label>
                                                    <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="Separate names with commas" required>
                                                    @error('guest_detail[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="special_day[]"><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="If during your stay there are guests who have special days such as birthdays, aniversaries, and others" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Special Day </label>
                                                    <input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="ex: Birthday" >
                                                    @error('special_day[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="special_date[]">Insert Date</label>
                                                    @if ($special_date != "")
                                                        <input type="text" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yy/mm/dd">
                                                    @else
                                                        <input type="text" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yy/mm/dd">
                                                    @endif
                                                    @error('special_date[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4" style="place-self: padding-bottom: 6px;">
                                                <div class="form-group">
                                                    <label for="extra_bed_id[]">Extra Bed <span> * </span> <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="Choose an extra bed if the room is occupied by more than 2 guests." class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                    <select name="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required>
                                                        <option selected value="">Select extra bed</option>
                                                        <option value="0">None</option>
                                                        @foreach ($extra_bed_room as $ebr)
                                                            @php
                                                                $ebr_cr = ceil($ebr->contract_rate/$usdrates->rate) + $ebr->markup;
                                                                $ebr_price = (ceil($ebr_cr * $tax->tax / 100) + $ebr_cr) * $order->duration;
                                                            @endphp
                                                            <option value="{{ $ebr->id }}">{{ $ebr->name." (".$ebr->type.") $ ".$ebr_price }}</option>
                                                        @endforeach
                                                        
                                                    </select>
                                                    @error('extra_bed[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    var jmlh_room = 20;
                                    var cr = document.getElementById("vc_room").value;
                                    var ro = 1;
                                    $("#add").click(function(){ 
                                        if(ro < jmlh_room){ 
                                            ro++;
                                            cr++;
                                            var html = $(".copy").html();
                                            $(".after-add-more").before(html);
                                            document.getElementById("room_no[]").innerHTML=cr;
                                        }
                                    });
                                    $("body").on("click",".remove",function(){ 
                                        $(this).parents(".control-group").remove();
                                        cr--;
                                        document.getElementById("room_no[]").innerHTML=cr;
                                    });
                                });
                            </script>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    

    
