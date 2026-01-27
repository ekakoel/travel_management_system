
@php
    $other_id = json_decode($weddings->other_service_id);
@endphp
    <div class="tab-inner-title">
        Other Service
        @if ($weddings->status !== "Active")
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-other">
                    @if ($weddings->other_service_id != 'null')
                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                    @else
                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                    @endif
                </a>
            </span>
        @endif
    </div>
    {{-- WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------------------------}}
    @if (count($other_services) > 0)
        @if ($other_id != "null" and $other_id )
                <div class="card-box-content m-b-8">
                    @foreach ($other_id as $otherid)
                        @php
                            $other = $other_services->where('id',$otherid)->first();
                        @endphp
                        @if ($other)
                            @php
                                $vendor_other = $vendors->where('id',$other->vendor_id)->first();
                            @endphp
                            <div class="card">
                                <a href="#" data-toggle="modal" data-target="#detail-other-{{ $otherid }}">
                                    <div class="card-image-container">
                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $other->cover) }}" alt="{{ $other->name }}">
                                        <div class="name-card">
                                            <b>{{ $vendor_other->name }}</b>
                                            <p>{{ $other->service }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="price-card-usd m-t-8">
                                        {{ currencyFormatUsd($other->publish_rate) }}
                                    </div>
                                </a>
                            </div>
                            {{-- MODAL WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                            <div class="modal fade" id="detail-other-{{ $otherid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-left">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $other->service }}</div>
                                            </div>
                                            <div class="card-banner m-b-8">
                                                <img class="rounded" src="{{ asset('storage/vendors/package/' . $other->cover) }}" alt="{{ $other->cover }}" loading="lazy">
                                            </div>
                                            @if ($other->service)
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-sm-4">
                                                            <b>Wedding Venue: </b><p>{!! $other->service !!}</p>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <b>Duration: </b><p>{!! $other->duration." ".$other->time !!}</p>
                                                        </div>
                                                        @if ($other->capacity)
                                                            
                                                            <div class="col-sm-4">
                                                                <b>Capacity: </b><p>{{ $other->capacity." guests" }}</p>
                                                            </div>
                                                        @endif
                                                        <div class="col-sm-12">
                                                            <b>Description: </b><p>{!! $other->description !!}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="card-box-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                            <div class="modal-label-price">
                                                {{ currencyFormatUsd($other->publish_rate) }}
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
                <p>The other service have not been added to the wedding package yet.</p>
            </div>
        @endif
        @if ($weddings->status !== "Active")
            <div class="card-grid-footer">
                {{-- MODAL ADD WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                <div class="modal fade" id="add-wedding-other" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy ion-ios-flower"></i> Other Service</div>
                                </div>
                                @if ($other_services)
                                    <form id="addOther" action="/fadd-wedding-other/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                        @method('put')
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    @foreach ($other_services as $other_service)
                                                        @if ($other_service)
                                                            @php
                                                                $add_vendor_other_service = $vendors->where('id',$other_service->vendor_id)->first();
                                                            @endphp
                                                            <div class="col-md-4 m-b-18">
                                                                <div class="card">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $other_service->cover) }}" alt="{{ $other_service->service }}">
                                                                    <input type="checkbox" name="other_service_id[]" value="{{ $other_service->id }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $add_vendor_other_service->name }}</b>
                                                                        <p>{{ $other_service->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @error('other_service_id[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addOther" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
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
                <p>Sorry, other are not available at the moment. Please add other to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
                <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
            </div>
        </div>
    @endif
