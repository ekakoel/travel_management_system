
@php
    $wed_venues_id = json_decode($weddings->wedding_venue_id);
@endphp
<div class="tab-inner-title">
    Wedding Venue
    @if ($weddings->status !== "Active")
        <span>
            <a href="#" data-toggle="modal" data-target="#add-wedding-venue">
                @if ($weddings->wedding_venue_id != 'null' and $weddings->wedding_venue_id)
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                @else
                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                @endif
            </a>
        </span>
    @endif
</div>
{{-- WEDDING VENUE --------------------------------------------------------------------------------------------------------------------------------}}
@if (count($wedding_venues) > 0)
    @if ($wed_venues_id != "null" and $wed_venues_id )
        <div class="card-box-content m-b-8">
            @foreach ($wed_venues_id as $wed_venue_id)
                @php
                    $wedding_venue = $wedding_venues->where('id',$wed_venue_id)->first();
                @endphp
                @if ($wedding_venue)
                    @php
                        $vendor_venue = $vendors->where('id',$wedding_venue->vendor_id)->first();
                    @endphp
                    <div class="card">
                        <a href="#" data-toggle="modal" data-target="#detail-wedding_venue-{{ $wed_venue_id }}">
                            <div class="card-image-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->name }}">
                                <div class="name-card">
                                    <b>{{ $vendor_venue->name }}</b>
                                    <p>{{ $wedding_venue->service }}</p>
                                </div>
                            </div>
                            <div class="price-card-usd m-t-8">
                                {{ currencyFormatUsd($wedding_venue->publish_rate) }}
                            </div>
                            <div class="label-capacity">
                                <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding_venue->capacity }}
                            </div>
                        </a>
                    </div>
                    {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="detail-wedding_venue-{{ $wed_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $wedding_venue->service }}</div>
                                    </div>
                                    <div class="card-banner m-b-8">
                                        <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->cover }}" loading="lazy">
                                    </div>
                                    @if ($wedding_venue->service)
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-4">
                                                    <b>Wedding Venue: </b><p>{!! $wedding_venue->service !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Duration: </b><p>{!! $wedding_venue->duration." ".$wedding_venue->time !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Capacity: </b><p>{{ $wedding_venue->capacity." guests" }}</p>
                                                </div>
                                                
                                                @if ($wedding_venue->description)
                                                    <div class="col-sm-12">
                                                        <b>Description: </b><p>{!! $wedding_venue->description !!}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                    </div>
                                    <div class="modal-label-price">
                                        {{ currencyFormatUsd($wedding_venue->publish_rate) }}
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
            <p>The Wedding Venue have not been added to the wedding package yet.</p>
        </div>
    @endif
    @if ($weddings->status !== "Active")
        {{-- MODAL ADD WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
        <div class="modal fade" id="add-wedding-venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-left">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>Add Wedding Venue</div>
                        </div>
                        <form id="addweddingvenue" action="/fadd-wedding-venue/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="row">
                                        @foreach ($wedding_venues as $no_wedding_venue=>$wedding_venue)
                                            @if ($wedding_venue)
                                                @php
                                                    $venue_hotel = $hotels->where('id',$wedding_venue->hotel_id)->first();
                                                    
                                                @endphp
                                                @if ($venue_hotel)
                                                    <div class="col-md-4 m-b-18">
                                                        <div class="card">
                                                            <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->service }}">
                                                            <input type="checkbox" id="wedding_venue_id[]" name="wedding_venue_id[]" value="{{ $wedding_venue->id }}">
                                                            <div class="name-card">
                                                                <b>{{ $venue_hotel->name }}</b>
                                                                <p>{{ $wedding_venue->service }}</p>
                                                            </div>
                                                            <div class="label-capacity">{{ $wedding_venue->capacity." guests" }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                    @error('wedding_venue_id[]')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-box-footer">
                                <button type="submit" form="addweddingvenue" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="row">
        <div class="col-12">
            <p>Sorry, wedding venue are not available at the moment. Please add wedding venue to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
            <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
        </div>
    </div>
@endif
