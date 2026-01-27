
@php
    $documentation_id = json_decode($weddings->documentations_id);
@endphp

    <div class="tab-inner-title">
        Documentation
        @if ($weddings->status !== "Active")
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-documentation">
                    @if ($weddings->dinner_venues_id != 'null')
                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                    @else
                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                    @endif
                </a>
            </span>
        @endif
    </div>
    {{-- WEDDING DOCUMENTATION --------------------------------------------------------------------------------------------------------------------------------}}
    @if (count($documentations) > 0)
        @if ($documentation_id != "null" and $documentation_id )
            <div class="card-box-content m-b-8">
                @foreach ($documentation_id as $documentationid)
                    @php
                        $documentation = $documentations->where('id',$documentationid)->first();
                    @endphp
                    @if ($documentation)
                        @php
                            $vendor_documentation = $vendors->where('id',$documentation->vendor_id)->first();
                        @endphp
                        <div class="card">
                            <a href="#" data-toggle="modal" data-target="#detail-documentation-{{ $documentationid }}">
                                <div class="card-image-container">
                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $documentation->cover) }}" alt="{{ $documentation->name }}">
                                    <div class="name-card">
                                        <b>{{ $vendor_documentation->name }}</b>
                                        <p>{{ $documentation->service }}</p>
                                    </div>
                                </div>
                                
                                <div class="price-card-usd m-t-8">
                                    {{ currencyFormatUsd($documentation->publish_rate) }}
                                </div>
                            </a>
                        </div>
                        {{-- MODAL WEDDING ENTERTAINMENT --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="detail-documentation-{{ $documentationid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $documentation->service }}</div>
                                        </div>
                                        <div class="card-banner m-b-8">
                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $documentation->cover) }}" alt="{{ $documentation->cover }}" loading="lazy">
                                        </div>
                                        @if ($documentation->service)
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-sm-4">
                                                        <b>Wedding Venue: </b><p>{!! $documentation->service !!}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <b>Duration: </b><p>{!! $documentation->duration." ".$documentation->time !!}</p>
                                                    </div>
                                                    @if ($documentation->capacity)
                                                        
                                                        <div class="col-sm-4">
                                                            <b>Capacity: </b><p>{{ $documentation->capacity." guests" }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="col-sm-12">
                                                        <b>Description: </b><p>{!! $documentation->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                        <div class="modal-label-price">
                                            {{ currencyFormatUsd($documentation->publish_rate) }}
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
                <p>The documentation have not been added to the wedding package yet.</p>
            </div>
        @endif
        @if ($weddings->status !== "Active")
            <div class="card-grid-footer">
                {{-- MODAL ADD WEDDING DOCUMENTATION --------------------------------------------------------------------------------------------------------------- --}}
                <div class="modal fade" id="add-wedding-documentation" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fi-camera"></i>Documentation</div>
                                </div>
                                @if ($documentations)
                                    <form id="addDocumentation" action="/fadd-wedding-documentation/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                        @method('put')
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    @foreach ($documentations as $no_dinner_venue=>$dinner_venue)
                                                        @if ($dinner_venue)
                                                            @php
                                                                $add_vendor_dinner_venue = $vendors->where('id',$dinner_venue->vendor_id)->first();
                                                            @endphp
                                                            <div class="col-md-4 m-b-18">
                                                                <div class="card">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->service }}">
                                                                    <input type="checkbox" name="documentations_id[]" value="{{ $dinner_venue->id }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $add_vendor_dinner_venue->name }}</b>
                                                                        <p>{{ $dinner_venue->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @error('documentations_id[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addDocumentation" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
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
                <p>Sorry, documentation are not available at the moment. Please add documentation to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
                <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
            </div>
        </div>
    @endif

