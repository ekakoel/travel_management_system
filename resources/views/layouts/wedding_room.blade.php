
@php
    $rooms_id = json_decode($weddings->suite_and_villas_id);
@endphp
<div class="tab-inner-title">
    Suites and Villas
    @if ($weddings->status !== "Active")
        <span>
            <a href="#" data-toggle="modal" data-target="#add-wedding-rooms">
                @if ($rooms_id != "null" and $rooms_id )
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                @else
                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                @endif
            </a>
        </span>
    @endif
</div>
{{-- WEDDING SUITE AND VILLAS --------------------------------------------------------------------------------------------------------------------------------}}
@if (count($suite_and_villas) > 0)
    @if ($rooms_id != "null" and $rooms_id )
        <div class="card-box-content m-b-8">
            @foreach ($rooms_id as $roomsid)
                @php
                    $rooms = $suite_and_villas->where('id',$roomsid)->first();
                    if ($rooms) {
                        $roomandprice = $room_price->where('rooms_id',$rooms->id)->first();
                        $cr_room = ceil($roomandprice->contract_rate/$usdrates->rate); 
                        $cr_markup = $cr_room + $roomandprice->markup;
                        $cr_tax =  ceil($cr_markup * ($taxes->tax / 100));
                        $roomprice = ($cr_tax + $cr_markup)*$wedding_duration;
                    }else{
                        $roomprice = 0;
                    }
                @endphp
                @if ($rooms)
                    @php
                        $hotel_room = $hotels->where('id',$rooms->hotels_id)->first();
                    @endphp
                    <div class="card">
                        <a href="#" data-toggle="modal" data-target="#detail-rooms-{{ $roomsid }}">
                            <div class="card-image-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/hotels/hotels-room/' . $rooms->cover) }}" alt="{{ $rooms->name }}">
                                <div class="name-card">
                                    <b>{{ $hotel_room->name }}</b>
                                    <p>{{ $rooms->rooms }}</p>
                                </div>
                            </div>
                            @if ($roomandprice)
                                <div class="price-card-usd m-t-8">
                                    {{ currencyFormatUsd($roomprice) }}
                                </div>
                            @endif
                            
                            <div class="label-capacity">
                                    {{ $weddings->duration }}
                            </div>
                        </a>
                    </div>
                    {{-- MODAL WEDDING SUITE AND VILLAS --------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="detail-rooms-{{ $roomsid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-hotel"></i>{{ $hotel_room->name." - ". $rooms->rooms }}</div>
                                    </div>
                                    <div class="card-banner m-b-8">
                                        <img class="rounded" src="{{ asset('storage/hotels/hotels-room/' . $rooms->cover) }}" alt="{{ $rooms->cover }}" loading="lazy">
                                    </div>
                                    @if ($rooms->name)
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-4">
                                                    <b>Wedding Venue: </b><p>{!! $rooms->name !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Duration: </b><p>{!! $rooms->duration." ".$rooms->time !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Capacity: </b><p>{{ $rooms->capacity." guests" }}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Price: </b><p class="usd-rate">{{ currencyFormatUsd(ceil($rooms->price/$usdrates->rate)) }}</p>
                                                </div>
                                                
                                                <div class="col-sm-12">
                                                    <b>Description: </b><p>{!! $rooms->description !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                    </div>
                                    <div class="modal-label-price">
                                        @if ($roomandprice)
                                        {{ currencyFormatUsd($roomprice) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="card-box-content-empty">
            <p>The rooms have not been added to the wedding package yet.</p>
        </div>
    @endif
    @if ($weddings->status !== "Active")
        <div class="card-grid-footer">
            {{-- MODAL ADD WEDDING SUITE AND VILLAS --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="add-wedding-rooms" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fi-camera"></i>Suites and Villas</div>
                            </div>
                            @if ($suite_and_villas)
                                <form id="addRooms" action="/fadd-wedding-rooms/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                @foreach ($suite_and_villas as $room)
                                                    @if ($room)
                                                        @php
                                                            $hotelsroom = $hotels->where('id',$room->hotels_id)->first();
                                                        @endphp
                                                        <div class="col-md-4 m-b-18">
                                                            <div class="card">
                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->service }}">
                                                                <input type="checkbox" name="rooms_id[]" value="{{ $room->id }}">
                                                                <div class="name-card">
                                                                    <b>{{ $hotelsroom->name }}</b>
                                                                    <p>{{ $room->rooms }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @error('rooms_id[]')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addRooms" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="row">
        <div class="col-12">
            <p>Sorry, rooms are not available at the moment. Please add rooms to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
            <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
        </div>
    </div>
@endif

