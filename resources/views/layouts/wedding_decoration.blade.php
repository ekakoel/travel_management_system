
@php
    $decors_id = json_decode($weddings->decorations_id);
@endphp
    <div class="tab-inner-title">
        Decorations
        @if ($weddings->status !== "Active")
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-decoration">
                    @if ($weddings->decorations_id != 'null')
                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                    @else
                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                    @endif
                </a>
            </span>
        @endif
    </div>
    {{-- WEDDING DECORATIONS --------------------------------------------------------------------------------------------------------------------------------}}
    @if (count($decorations) > 0)
        @if ($weddings->decorations_id != "null" and $weddings->decorations_id )
            <div class="card-box-content m-b-8">
                @foreach ($decors_id as $decor_id)
                    @php
                        $decoration = $decorations->where('id',$decor_id)->first();
                    @endphp
                    @if ($decoration)
                        @php
                            $vendor_decoration = $vendors->where('id',$decoration->vendor_id)->first();
                        @endphp
                        <div class="card">
                            <a href="#" data-toggle="modal" data-target="#detail-decoration-{{ $decor_id }}">
                                <div class="card-image-container">
                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $decoration->cover) }}" alt="{{ $decoration->name }}">
                                    <div class="name-card">
                                        <b>{{ $vendor_decoration->name }}</b>
                                        <p>{{ $decoration->service }}</p>
                                    </div>
                                </div>
                            
                                <div class="price-card-usd m-t-8">
                                    {{ currencyFormatUsd($decoration->publish_rate) }}
                                </div>
                            </a>
                        </div>
                        {{-- MODAL WEDDING DECORATIONS --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="detail-decoration-{{ $decor_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $decoration->service }}</div>
                                        </div>
                                        <div class="card-banner m-b-8">
                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $decoration->cover) }}" alt="{{ $decoration->cover }}" loading="lazy">
                                        </div>
                                        @if ($decoration->service)
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-sm-4">
                                                        <b>Wedding Venue: </b><p>{!! $decoration->service !!}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <b>Duration: </b><p>{!! $decoration->duration." ".$decoration->time !!}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <b>Capacity: </b><p>{{ $decoration->capacity." guests" }}</p>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <b>Description: </b><p>{!! $decoration->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-label-price">
                                                {{ currencyFormatUsd($decoration->publish_rate) }}
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
                <p>The decorations have not been added to the wedding package yet.</p>
            </div>
        @endif
        @if ($weddings->status !== "Active")
            <div class="card-grid-footer">
                {{-- MODAL ADD WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
                <div class="modal fade" id="add-wedding-decoration" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fi-trees"></i>Decorations</div>
                                </div>
                                @if ($decorations)
                                    <form id="addDecoration" action="/fadd-wedding-decoration/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                        @method('put')
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    @foreach ($decorations as $no_decoration=>$decoration)
                                                        @if ($decoration)
                                                            @php
                                                                $add_vendor_decoration = $vendors->where('id',$decoration->vendor_id)->first();
                                                            @endphp
                                                            <div class="col-md-4 m-b-18">
                                                                <div class="card">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration->cover) }}" alt="{{ $decoration->service }}">
                                                                    <input type="checkbox" name="decorations_id[]" value="{{ $decoration->id }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $add_vendor_decoration->name }}</b>
                                                                        <p>{{ $decoration->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @error('decorations_id[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addDecoration" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
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
        <div class="col-12">
            <p>Sorry, decorations are not available at the moment. Please add decorations to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
            <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
        </div>
    @endif
