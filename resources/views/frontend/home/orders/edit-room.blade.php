@section('title', __('messages.Hotel Room'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; @lang('messages.Edit Order')</div>
                            @include('partials.breadcrumbs', [
                                'breadcrumbs' => [
                                    ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                    ['url' => route('view.orders'), 'label' => __('messages.Order')],
                                    ['url' => route('view.edit-order-hotel',$order->id), 'label' => $order->orderno],
                                    ['label' => __('messages.Edit Room')],
                                ]
                            ])
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="fa fa-hotel"></i> @lang('messages.Suites and Villas')</div>
                            </div>
                            <form id="editOrderRoom" action="{{ route('func.update.order-room',$order->id) }}" method="post" enctype="multipart/form-data">
                                @method('put')
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-12">
                                        <ol>
                                            @if ($order->number_of_room || $order->number_of_guests_room || $order->guest_detail || $order->guest_detail  )
                                                @foreach ($nogr as $index=>$number_of_guest_room)
                                                    <div class="control-group">

                                                        @if ($index > 0)
                                                            <button class="btn btn-remove btnremove remove" type="button" data-id="{{ $order->id }}">
                                                                <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                        <div class="{{ $number_of_guest_room > $room->capacity_adult && !$extra_bed_id[$index] ? "room-container-error":"room-container";}} m-b-18">
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="subtitle">
                                                                        @lang('messages.Room')
                                                                        @if ($number_of_guest_room > $room->capacity_adult && !$extra_bed_id[$index])
                                                                            <p class="blink_me float-right p-r-27"><i>@lang('messages.This room need extra bed!')</i></p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                        
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="number_of_guests_room[]">@lang('messages.Number of Guest')</label>
                                                                        <input type="number" min="1" max="{{ $room->capacity_adult + $room->capacity_child }}" name="number_of_guests_room[]" class="form-control m-0 @error('number_of_guests_room[]') is-invalid @enderror" placeholder="{{ __('messages.Capacity') ." ".$room->capacity." ". __('messages.guests') }}" value="{{ $number_of_guest_room }}" required>
                                                                        @error('number_of_guests_room[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                        
                                                                <div class="col-sm-9">
                                                                    <div class="form-group">
                                                                        <label for="guest_detail[]">@lang('messages.Guest Name')  
                                                                            <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Child guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i>
                                                                        </label>
                                                                        <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ $guest_detail[$index] }}" required>
                                                                        @error('guest_detail[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="special_day[]">@lang('messages.Special Day') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> </label>
                                                                        <input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex: Birthday')" value="{{ $special_day[$index] }}">
                                                                        @error('special_day[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="special_date[]">@lang('messages.Insert Date for Special Day')</label>
                                                                        <select name="special_date[]" class="custom-select">
                                                                            <option {{ $special_date[$index]?"":"selected"; }} value="">@lang('messages.None')</option>
                                                                            @foreach ($date_stay as $datestay)
                                                                                <option {{ $special_date[$index] == date('Y-m-d',strtotime($datestay))?"selected":""; }} value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4" style="place-self: padding-bottom: 6px;">
                                                                    <div class="form-group">
                                                                        <label for="extra_bed_id[]">@lang('messages.Extra Bed') <span> * </span> 
                                                                            <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" 
                                                                            title="@lang('messages.Select an extra bed if the room is occupied by more than room capacity')" 
                                                                            class="icon-copy fa fa-info-circle" aria-hidden="true"></i>
                                                                        </label><br>
                                                                        <select name="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror">
                                                                            <option value="0">@lang('messages.None')</option>
                                                                            @foreach ($extrabeds as $eb)
                                                                                <option {{ $extra_bed_id[$index] == $eb->id?"selected":""; }} value={{ $eb->id }}>{{ $eb->name }} ({{ $eb->type }}) {{ currencyFormatUsd($eb->price) }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('extra_bed[]')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="order_hotel_promo_details_id[]" value="{{ $order->id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="after-add-more"></div> 
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            @if ($order->request_quotation == "Yes")
                                                <p class="m-t-8">
                                                    @lang('messages.You have booked rooms for more than 8 units before and, we will contact you as soon as possible to confirm the order, after you submit this order, thank you.')
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="status" value="Draft">
                                    <input type="hidden" name="price_pax" value="{{ $price_pax }}">
                                </div>
                            </form>
                           <div class="card-box-footer">
                                <button id="add" type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add More Room')</button>
                                <button type="submit" form="editOrderRoom" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Update')</button>
                                <a href="{{ route('view.edit-order-hotel',$order->id) }}#sitesAndVillas">
                                    <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                </a>
                           </div>
                            <div class="copy hide">
                                <div class="control-group">
                                    <button class="btn btn-remove btnremove remove" type="button" data-id="{{ $order->id }}">
                                        <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                                    </button>
                                    <div class="room-container m-b-18">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="subtitle">
                                                    @lang('messages.Room')
                                                </div>
                                            </div>
    
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="number_of_guests_room[]">@lang('messages.Number of Guest')</label>
                                                    <input type="number" min="1" max="{{ $room->capacity_adult + ($room->capacity_child) }}" name="number_of_guests_room[]" class="form-control m-0 @error('number_of_guests_room[]') is-invalid @enderror" placeholder="{{ __('messages.Capacity') ." ".$room->capacity." ". __('messages.guests') }}" value="{{ old('number_of_guests_room[]') }}" required>
                                                    @error('number_of_guests_room[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
    
                                            <div class="col-sm-9">
                                                <div class="form-group">
                                                    <label for="guest_detail[]">@lang('messages.Guest Name')  
                                                        <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Child guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i>
                                                    </label>
                                                    <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail') }}" required>
                                                    @error('guest_detail[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="special_day[]">@lang('messages.Special Day') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i> </label>
                                                    <input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex: Birthday')" value="{{ old('special_day') }}">
                                                    @error('special_day[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="special_date[]">@lang('messages.Insert Date for Special Day')</label>
                                                    <select name="special_date[]" class="custom-select">
                                                        <option selected value="">@lang('messages.None')</option>
                                                        @foreach ($date_stay as $datestay)
                                                            <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4" style="place-self: padding-bottom: 6px;">
                                                <div class="form-group">
                                                    <label for="extra_bed_id[]">@lang('messages.Extra Bed') <span> * </span> 
                                                        <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" 
                                                        title="@lang('messages.Select an extra bed if the room is occupied by more than room capacity')" 
                                                        class="icon-copy fa fa-info-circle" aria-hidden="true"></i>
                                                    </label><br>
                                                    <select name="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror">
                                                        <option selected value="">@lang('messages.None')</option>
                                                        @foreach ($extrabeds as $ebr)
                                                            <option value={{ $ebr->id }}>{{ $ebr->name }} ({{ $ebr->type }}) {{ currencyFormatUsd($ebr->price) }}</option>
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
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    var jmlh_room = 8;
                                    var ro = 1;
                                    $(".add-more").click(function(){ 
                                        if(ro < jmlh_room){ 
                                            ro++;
                                            
                                            var html = $(".copy").html();
                                            $(".after-add-more").before(html);
                                        }
                                    });
                            
                                    $("body").on("click",".remove",function(){ 
                                        $(this).parents(".control-group").remove();
                                    });
                                });
                            </script>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.loading-form', ['id' => 'editOrderRoom'])
@endsection
    

    