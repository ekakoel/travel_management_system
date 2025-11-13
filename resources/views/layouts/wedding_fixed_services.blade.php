
<div class="card-box">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fa fa-check"></i> Fixed Services</div>
    </div>
    {{-- WEDDING FIXED SERVICES --------------------------------------------------------------------------------------------------------------------------------}}
    @if (count($fixed_services) > 0)
        @if ($weddings->fixed_services_id != "null" and $weddings->fixed_services_id )
            <div class="card-box-content">
                @foreach ($fixed_services_id as $fixed_service_id)
                    @php
                        $fixed_service = $fixed_services->where('id',$fixed_service_id)->first();
                    @endphp
                    @if ($fixed_service)
                        @php
                            $vendor_fixed_service = $vendors->where('id',$fixed_service->vendor_id)->first();
                        @endphp
                        <div class="card">
                            <a href="#" data-toggle="modal" data-target="#detail-fixed_service-{{ $fixed_service_id }}">
                                <div class="card-image-container">
                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $fixed_service->cover) }}" alt="{{ $fixed_service->name }}">
                                    <div class="name-card">
                                        <b>{{ $vendor_fixed_service->name }}</b>
                                        <p>{{ $fixed_service->service }}</p>
                                    </div>
                                </div>
                            
                                <div class="price-card-usd m-t-8">
                                    {{ currencyFormatUsd($fixed_service->publish_rate) }}
                                </div>
                            </a>
                        </div>
                        {{-- MODAL WEDDING FIXED SERVICES --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="detail-fixed_service-{{ $fixed_service_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-check"></i>{{ $fixed_service->service }}</div>
                                        </div>
                                        <div class="card-banner m-b-8">
                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $fixed_service->cover) }}" alt="{{ $fixed_service->cover }}" loading="lazy">
                                        </div>
                                        @if ($fixed_service->service)
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-sm-4">
                                                        <b>Service: </b><p>{!! $fixed_service->service !!}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <b>Duration: </b><p>{!! $fixed_service->duration." ".$fixed_service->time !!}</p>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <b>Description: </b><p>{!! $fixed_service->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-label-price">
                                                {{ currencyFormatUsd($fixed_service->publish_rate) }}
                                            </div>
                                        @endif
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
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
                <p>The fixed_services have not been added to the wedding package yet.</p>
            </div>
        @endif
        @if ($weddings->status !== "Active")
            @if ($fixed_services)
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#add-wedding-fixed_service">
                        @if ($weddings->fixed_services_id != 'null')
                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        @else
                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                        @endif
                    </a>
                    {{-- MODAL ADD WEDDING FIXED SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="add-wedding-fixed_service" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-check"></i>Fixed Service</div>
                                    </div>
                                    
                                        <form id="addFixedService" action="/fadd-wedding-fixed-service/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                            @method('put')
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="row">
                                                        @foreach ($fixed_services as $no_fixed_service=>$fixed_service)
                                                            @if ($fixed_service)
                                                                @php
                                                                    $add_vendor_fixed_service = $vendors->where('id',$fixed_service->vendor_id)->first();
                                                                @endphp
                                                                <div class="col-md-4 m-b-18">
                                                                    <div class="card">
                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $fixed_service->cover) }}" alt="{{ $fixed_service->service }}">
                                                                        <input type="checkbox" name="fixed_services_id[]" value="{{ $fixed_service->id }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $add_vendor_fixed_service->name }}</b>
                                                                            <p>{{ $fixed_service->service }}</p>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @error('fixed_services_id[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="card-box-footer">
                                                <button type="submit" form="addFixedService" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                        </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @else
        <div class="col-12">
            <p>Sorry, fixed service are not available at the moment. Please add fixed service to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
            <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
        </div>
    @endif
</div>
