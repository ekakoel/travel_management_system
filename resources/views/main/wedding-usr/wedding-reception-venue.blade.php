{{-- RECEPTION VENUE --}}
<div id="receptionVenues" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy dw dw-pinwheel"></i> @lang('messages.Reception Venue')</div>
    </div>
    @if ($hotel->reception_venues)
        <div class="card-box-content">
            @foreach ($hotel->reception_venues as $rec_no=>$reception_venue)
                <div class="card">
                    <a href="#" data-toggle="modal" data-target="#detail-dinner-package-{{ $reception_venue->id }}">
                        <div class="card-image-container">
                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                            <div class="card-price-container">
                                <div class="card-price-bl">
                                    <i class="icon-copy fa fa-user" aria-hidden="true"></i> {{ $reception_venue->capacity }}
                                </div>
                                <div class="card-price-br">
                                    {{ currencyFormatUsd($reception_venue->price) }}
                                </div>
                            </div>
                            <div class="name-card">
                                <p>
                                    <b>{{ $reception_venue->name }}</b><br>
                                </p>
                            </div>
                            
                        </div>
                    </a>
                    <div class="card-button-container">
                        <a href="#" data-toggle="modal" data-target="#order-reception-venue-{{ $reception_venue->id }}">
                            <button class="btn card-button"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang("messages.Order")</button>
                        </a>
                        @if (count($wedding_planner_reception_venue_none)>0)
                            <a href="#" data-toggle="modal" data-target="#add-reception-venue-to-wedding-planner-{{ $reception_venue->id }}">
                                <button class="btn card-button"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang("messages.Add To Planner")</button>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="modal fade" id="detail-dinner-package-{{ $reception_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-eye"></i> @lang('messages.Reception Venue')</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="modal-img-container">
                                            <img src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}" loading="lazy">
                                            <div class="modal-service-name">
                                                {{ $reception_venue->name }}
                                                <p>{{ "@". $hotel->name }}</p>
                                                <p>
                                                    {{ $reception_venue->capacity }} @lang('messages.Invitations')
                                                </p>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            @if ($reception_venue->description)
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">@lang('messages.Description')</div>
                                                                <div class="card-ptext-margin m-b-8">
                                                                    {!! $reception_venue->description !!}
                                                                </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($reception_venue->terms_and_conditions)
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">@lang('messages.Terms and Conditions')</div>
                                                            <div class="card-ptext-margin m-b-8">
                                                                {!! $reception_venue->terms_and_conditions !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="tab-inner-title-light">@lang('messages.Rate')</div>
                                        <div class="card-ptext-margin">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="card-subtitle">@lang('messages.Rate')</div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <div class="usd-rate">
                                                        {{ currencyFormatUsd($reception_venue->price) }} 
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="order-reception-venue-{{ $reception_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy dw dw-pinwheel"></i> @lang('messages.Reception Venue') - {{ $reception_venue->name }}</div>
                                </div>
                                <form id="orderReceptionVenue-{{ $reception_venue->id }}" action="/fadd-order-reception-venue/{{ $reception_venue->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        {{-- Admin create order ================================================================= --}}
                                        @canany(['weddingRsv','weddingDvl','weddingSls','weddingAuthor'])
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="user_id">Select Agent <span>*</span></label>
                                                    <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                        <option selected value="">Select Agent</option>
                                                        @foreach ($agents as $agent)
                                                            <option class="option-list" value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('user_id')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endcan

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="groom_name">@lang("messages.Groom's Name") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-torso"></i></span>
                                                    <input type="text" name="groom_name" class="form-control input-icon @error('groom_name') is-invalid @enderror" placeholder="@lang("messages.Groom's Name")" value="{{ old('groom_name') }}" required>
                                                </div>
                                                @error('groom_name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="groom_id">@lang("messages.ID") / @lang('messages.Passport') <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i></span>
                                                    <input type="text" name="groom_id" class="form-control input-icon @error('groom_id') is-invalid @enderror" placeholder="@lang("messages.ID") / @lang("messages.Passport") @lang("messages.number")"  value="{{ old('groom_id') }}" required>
                                                </div>
                                                @error('groom_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bride_name">@lang("messages.Bride's Name") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-torso-female"></i></span>
                                                    <input type="text" name="bride_name" class="form-control input-icon @error('bride_name') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" value="{{ old('bride_name') }}" required>
                                                </div>
                                                @error('bride_name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="brides_id">@lang("messages.ID") / @lang('messages.Passport') <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i></span>
                                                    <input type="text" name="brides_id" class="form-control input-icon @error('brides_id') is-invalid @enderror" placeholder="@lang("messages.ID") / @lang("messages.Passport") @lang("messages.number")"  value="{{ old('brides_id') }}" required>
                                                </div>
                                                @error('brides_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reception_date_start">@lang("messages.Reception Date") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <input readonly name="reception_date_start" type="text" class="wedding-date form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('reception_date_start') }}" required>
                                                </div>
                                                @error('reception_date_start')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_invitations">@lang('messages.Number of Invitations') <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-torsos-all"></i></span>
                                                    <input name="number_of_invitations" type="number" min="1" max="{{ $reception_venue->capacity }}"  class="form-control input-icon @error('number_of_invitations') is-invalid @enderror" placeholder="Maximum invitations {{ $reception_venue->capacity }}" type="text" value="{{ old('number_of_invitations') }}" required>
                                                </div>
                                                @error('number_of_invitations')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr class="form-hr">
                                            <div class="modal-show-price-container">
                                                <div class="modal-show-price">
                                                    @lang('messages.Price'): <span>{{ number_format($reception_venue->price) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="price" value="{{ $reception_venue->price }}">
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="orderReceptionVenue-{{ $reception_venue->id }}" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket"></i> @lang("messages.Order")</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="notification">Reception Venue not available</div>
    @endif
</div>