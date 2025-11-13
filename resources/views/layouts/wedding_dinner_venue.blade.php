
@php
    $dinners_id = json_decode($weddings->dinner_venue_id);
@endphp
<div class="tab-inner-title">
    Dinner Venue
    @if ($weddings->status !== "Active")
        <span>
            <a href="#" data-toggle="modal" data-target="#add-wedding-dinner-venue">
                @if ($weddings->dinner_venues_id != 'null')
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                @else
                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                @endif
            </a>
        </span>
    @endif
</div>
{{-- WEDDING DECORATIONS --------------------------------------------------------------------------------------------------------------------------------}}
@if (count($dinner_venues) > 0)
    @if ($weddings->dinner_venue_id != "null" and $weddings->dinner_venue_id )
        <div class="card-box-content m-b-8">
            @foreach ($dinners_id as $dinner_id)
                @php
                    $dinner_venue = $dinner_venues->where('id',$dinner_id)->first();
                @endphp
                @if ($dinner_venue)
                    @php
                        $vendor_dinner = $vendors->where('id',$dinner_venue->vendor_id)->first();
                    @endphp
                    <div class="card">
                        <a href="#" data-toggle="modal" data-target="#detail-dinner-venue-{{ $dinner_id }}">
                            <div class="card-image-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                <div class="name-card">
                                    <b>{{ $vendor_dinner->name }}</b>
                                    <p>{{ $dinner_venue->service }}</p>
                                </div>
                            </div>
                            
                            <div class="price-card-usd m-t-8">
                                {{ currencyFormatUsd($dinner_venue->publish_rate) }}
                            </div>
                            <div class="label-capacity">
                                {{ $dinner_venue->capacity." pax" }}
                            </div>
                        </a>
                    </div>
                    {{-- MODAL WEDDING DECORATIONS --------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="detail-dinner-venue-{{ $dinner_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $dinner_venue->service }}</div>
                                    </div>
                                    <div class="card-banner m-b-8">
                                        <img class="rounded" src="{{ asset('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->cover }}" loading="lazy">
                                    </div>
                                    @if ($dinner_venue->service)
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-4">
                                                    <b>Wedding Venue: </b><p>{!! $dinner_venue->service !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Duration: </b><p>{!! $dinner_venue->duration." ".$dinner_venue->time !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Capacity: </b><p>{{ $dinner_venue->capacity." guests" }}</p>
                                                </div>
                                                
                                                <div class="col-sm-12">
                                                    <b>Description: </b><p>{!! $dinner_venue->description !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                    </div>
                                    <div class="modal-label-price">
                                        {{ currencyFormatUsd($dinner_venue->publish_rate) }}
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
            <p>The dinner venue have not been added to the wedding package yet.</p>
        </div>
    @endif
    @if ($weddings->status !== "Active")
        <div class="card-grid-footer">
            {{-- MODAL ADD WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="add-wedding-dinner-venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fi-trees"></i>Dinner Venue</div>
                            </div>
                            @if ($dinner_venues)
                                <form id="addDinnerVenue" action="/fadd-wedding-dinner-venue/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                @foreach ($dinner_venues as $no_dinner_venue=>$dinner_venue)
                                                    @if ($dinner_venue)
                                                        @php
                                                            $add_vendor_dinner_venue = $vendors->where('id',$dinner_venue->vendor_id)->first();
                                                        @endphp
                                                        <div class="col-md-4 m-b-18">
                                                            <div class="card">
                                                                <img class="card-img" src="{{ asset ('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->service }}">
                                                                <input type="checkbox" name="dinner_venues_id[]" value="{{ $dinner_venue->id }}">
                                                                <div class="name-card">
                                                                    <b>{{ $add_vendor_dinner_venue->name }}</b>
                                                                    <p>{{ $dinner_venue->service }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @error('dinner_venues_id[]')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addDinnerVenue" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
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
            <p>Sorry, dinner venue are not available at the moment. Please add dinner venue to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
            <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
        </div>
    </div>
@endif

