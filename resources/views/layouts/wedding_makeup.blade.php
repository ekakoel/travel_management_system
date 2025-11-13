
@php
    $makeup_id = json_decode($weddings->makeup_id);
@endphp
<div class="tab-inner-title">
    Make-up
    @if ($weddings->status !== "Active")
        <span>
            <a href="#" data-toggle="modal" data-target="#add-wedding-makeup">
                @if ($makeup_id != 'null')
                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                @else
                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                @endif
            </a>
        </span>
    @endif
</div>
{{-- WEDDING MAKEUP --------------------------------------------------------------------------------------------------------------------------------}}
@if (count($muas) > 0)
    @if ($makeup_id != "null" and $makeup_id )
        <div class="card-box-content m-b-8">
            @foreach ($makeup_id as $makeups)
                @php
                    $makeup = $muas->where('id',$makeups)->first();
                @endphp
                @if ($makeup)
                    @php
                        $vendor_makeup = $vendors->where('id',$makeup->vendor_id)->first();
                    @endphp
                    <div class="card">
                        <a href="#" data-toggle="modal" data-target="#detail-makeup-{{ $makeups }}">
                            <div class="card-image-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $makeup->cover) }}" alt="{{ $makeup->name }}">
                                <div class="name-card">
                                    <b>{{ $vendor_makeup->name }}</b>
                                    <p>{{ $makeup->service }}</p>
                                </div>
                            </div>
                            <div class="price-card-usd m-t-8">
                                {{ currencyFormatUsd($makeup->publish_rate) }}
                            </div>
                        </a>
                    </div>
                    {{-- MODAL WEDDING MAKEUP --------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="detail-makeup-{{ $makeups }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $makeup->service }}</div>
                                    </div>
                                    <div class="card-banner m-b-8">
                                        <img class="rounded" src="{{ asset('storage/vendors/package/' . $makeup->cover) }}" alt="{{ $makeup->cover }}" loading="lazy">
                                    </div>
                                    @if ($makeup->service)
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-4">
                                                    <b>Wedding Venue: </b><p>{!! $makeup->service !!}</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <b>Duration: </b><p>{!! $makeup->duration." ".$makeup->time !!}</p>
                                                </div>
                                                @if ($makeup->capacity)
                                                    
                                                    <div class="col-sm-4">
                                                        <b>Capacity: </b><p>{{ $makeup->capacity." guests" }}</p>
                                                    </div>
                                                @endif
                                                
                                                
                                                <div class="col-sm-12">
                                                    <b>Description: </b><p>{!! $makeup->description !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                    </div>
                                    <div class="modal-label-price">
                                        {{ currencyFormatUsd($makeup->publish_rate) }}
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
            {{-- MODAL ADD WEDDING MAKEUP --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="add-wedding-makeup" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-paintbrush"></i>Makeup</div>
                            </div>
                            @if ($muas)
                                <form id="addMakeup" action="/fadd-wedding-makeup/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                @foreach ($muas as $makeups)
                                                    @if ($makeups)
                                                        @php
                                                            $add_vendor_makeup = $vendors->where('id',$makeups->vendor_id)->first();
                                                        @endphp
                                                        <div class="col-md-4 m-b-18">
                                                            <div class="card">
                                                                <img class="card-img" src="{{ asset ('storage/vendors/package/' . $makeups->cover) }}" alt="{{ $makeups->service }}">
                                                                <input type="checkbox" name="makeup_id[]" value="{{ $makeups->id }}">
                                                                <div class="name-card">
                                                                    <b>{{ $add_vendor_makeup->name }}</b>
                                                                    <p>{{ $makeups->service }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @error('makeup_id[]')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addMakeup" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
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

