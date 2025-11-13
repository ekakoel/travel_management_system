{{-- WEDDING PACKAGE --}}
<div id="weddingPackage" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('messages.Wedding Package')</div>
    </div>
    @if (count($weddingPackages)>0)
        <div id="weddingPackages" class="card-box-content">
            @foreach ($weddingPackages as $wedding_package)
                @php
                    $wp_reception_venue = $reception_venues->where('id',$wedding_package->dinner_venue_id)->first();
                    $slots = json_decode($wedding_package->slot, true);
                    if (is_array($slots)) {
                        $slot = implode(' | ',$slots);
                    } else {
                        $slot = NULL;
                    }
                @endphp
                <div class="card" role="tabpanel">
                    <a href="#" data-toggle="modal" data-target="#detail-wedding-package-{{ $wedding_package->id }}">
                        <div class="modal-image-container">
                            @if ($wedding_package->status == "Draft")
                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}">
                            @else
                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}">
                            @endif
                            
                            <div class="card-price-container">
                                <div class="card-price-full">
                                    <i class="icon-copy fi-torsos-all"></i> {{ $wedding_package->capacity }} 
                                </div>
                            </div>
                            <div class="name-card">
                                <b>{{ $wedding_package->name }}</b>
                            </div>
                        </div>
                    </a>
                    <div class="card-button-container">
                        <a href="#" data-toggle="modal" data-target="#order-wedding-package-venue-{{ $wedding_package->id }}">
                            <button class="btn card-button"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang("messages.Order")</button>
                        </a>
                    </div>
                    {{-- MODAL ORDER WEDDING PACKAGE --}}
                    <div class="modal fade" id="order-wedding-package-venue-{{ $wedding_package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="title"><i class="icon-copy fa fa-cubes" aria-hidden="true"></i> {{ $wedding_package->name }}</div>
                                    </div>
                                    <form id="orderWeddingPackage-{{ $wedding_package->id }}" action="/fadd-order-wedding-package/{{ $wedding_package->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            {{-- Admin create order ================================================================= --}}
                                            @canany(['weddingRsv','weddingDvl','weddingSls','weddingAuthor'])
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="agent_id">Select Agent <span>*</span></label>
                                                        <select name="agent_id" class="custom-select @error('agent_id') is-invalid @enderror" value="{{ old('agent_id') }}" required>
                                                            <option selected value="">Select Agent</option>
                                                            @foreach ($agents as $agent)
                                                                <option class="option-list" value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('agent_id')
                                                            <div class="alert-form">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endcan
                                            {{-- Admin create order ================================================================= --}}
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
                                                    <label for="bride_id">@lang("messages.ID") / @lang('messages.Passport') <span> *</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i></span>
                                                        <input type="text" name="bride_id" class="form-control input-icon @error('bride_id') is-invalid @enderror" placeholder="@lang("messages.ID") / @lang("messages.Passport") @lang("messages.number")"  value="{{ old('bride_id') }}" required>
                                                    </div>
                                                    @error('bride_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fi-calendar"></i></span>
                                                        <input readonly name="wedding_date" type="text" class="wedding-date form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('wedding_date') }}" required>
                                                    </div>
                                                    @error('wedding_date')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if ($slots)
                                                    <div class="form-group">
                                                        <label for="slot">@lang('messages.Slot') <span>*</span></label>
                                                        <select name="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                                <option value="">@lang('messages.Select one')</option>
                                                            @foreach ($slots as $wed_slot)
                                                                <option value="{{ $wed_slot }}">{{ date('h.i A',strtotime($wed_slot)) }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('slot')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="number_of_invitations">@lang('messages.Number of Invitations') <span> *</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fi-torsos-all"></i></span>
                                                        <input name="number_of_invitations" type="number" min="1" max="{{ $wedding_package->capacity }}"  class="form-control input-icon @error('number_of_invitations') is-invalid @enderror" placeholder="Maximum {{ $wedding_package->capacity }} invitations " type="text" value="{{ old('number_of_invitations') }}" required>
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
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="modal-show-price-container">
                                                            <div class="modal-show-price">
                                                                @lang('messages.Week Day Price'): <span>{{ number_format($wedding_package->week_day_price) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="modal-show-price-container">
                                                            <div class="modal-show-price">
                                                                @lang('messages.Holiday Price'): <span>{{ number_format($wedding_package->holiday_price) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="orderWeddingPackage-{{ $wedding_package->id }}" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket"></i> @lang("messages.Order")</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- MODAL WEDDING PACKAGE DETAIL --}}
                <div class="modal fade" id="detail-wedding-package-{{ $wedding_package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-cubes" aria-hidden="true"></i> @lang('messages.Wedding Package')</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="modal-img-container">
                                            <img src="{{ asset ('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}" loading="lazy">
                                            <div class="modal-service-name">
                                                {{ $wedding_package->name }}
                                                <p>{{ $wedding_package->capacity }} @lang('messages.Invitations')</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row ">
                                            <div class="col-sm-12">
                                                <div class="card-text">
                                                    <div class="card-ptext-margin">
                                                        <div class="row ">
                                                            <div class="col-6 col-sm-3">
                                                                <div class="card-subtitle">Package</div>
                                                                <p>{{ $wedding_package->name }}</p>
                                                            </div>
                                                            @if ($slot)
                                                                <div class="col-6 col-sm-3">
                                                                    <div class="card-subtitle">Slot</div>
                                                                    <p>
                                                                        {{ $slot }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                            <div class="col-6 col-sm-3">
                                                                <div class="card-subtitle">Duration</div>
                                                                <p>{{ $wedding_package->duration }} @lang('messages.nights')</p>
                                                            </div>
                                                            <div class="col-6 col-sm-3">
                                                                <div class="card-subtitle">Capacity</div>
                                                                <p>{{ $wedding_package->capacity }} @lang('messages.invitations')</p>
                                                            </div>
                                                            <div class="col-6 col-sm-3">
                                                                <div class="card-subtitle">Period Start</div>
                                                                <p>{{ date('d M Y',strtotime($wedding_package->period_start)) }}</p>
                                                            </div>
                                                            <div class="col-6 col-sm-3">
                                                                <div class="card-subtitle">Period End</div>
                                                                <p>{{ date('d M Y',strtotime($wedding_package->period_end)) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="tab-inner-title-light">@lang('messages.Wedding Venue')</div>
                                                <div class="card-ptext-margin m-b-8">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">@lang('messages.Hotel')</div>
                                                        <div class="ptext-value">{{ $wedding_package->hotels->name }}</div>
                                                        <div class="ptext-title">@lang('messages.Suite')/@lang('messages.Villa')</div>
                                                        <div class="ptext-value">{{ $wedding_package->suite_and_villa->rooms }}</div>
                                                        <div class="ptext-title">@lang('messages.Duration')</div>
                                                        <div class="ptext-value">{{ $wedding_package->duration }} @lang('messages.nights')</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="tab-inner-title-light">@lang('messages.Ceremony Venue')</div>
                                                <div class="card-ptext-margin m-b-8">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">@lang('messages.Venue')</div>
                                                        <div class="ptext-value">{{ $wedding_package->ceremony_venue->name }}</div>
                                                        @if ($wedding_package->ceremony_venue_decoration_id)
                                                            <div class="ptext-title">@lang('messages.Decoration')</div>
                                                            <div class="ptext-value">{{ $wedding_package->vendor_ceremony_venue_decoration->service }}</div>
                                                        @endif
                                                        <div class="ptext-title">@lang('messages.Capacity')</div>
                                                        <div class="ptext-value">{{ $wedding_package->ceremony_venue->capacity }} @lang('messages.Invitations')</div>
                                                        <div class="ptext-title">@lang('messages.Duration')</div>
                                                        <div class="ptext-value">2 @lang('messages.Hours')</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="tab-inner-title-light">@lang('messages.Reception Venue')</div>
                                                <div class="card-ptext-margin m-b-8">
                                                    <div class="card-ptext-content">
                                                        @if ($wedding_package->reception_venue)
                                                            <div class="ptext-title">@lang('messages.Venue')</div>
                                                            <div class="ptext-value">{{ $wedding_package->reception_venue->name }}</div>
                                                        @endif
                                                        @if ($wedding_package->reception_venue_decoration_id)
                                                            <div class="ptext-title">@lang('messages.Decoration')</div>
                                                            <div class="ptext-value">{{ $wedding_package->vendor_reception_venue_decoration->service }}</div>
                                                        @endif
                                                        <div class="ptext-title">@lang('messages.Capacity')</div>
                                                        <div class="ptext-value">{{ $wedding_package->reception_venue->capacity }} @lang('messages.Invitations')</div>
                                                        <div class="ptext-title">@lang('messages.Duration')</div>
                                                        <div class="ptext-value">4 @lang('messages.Hours')</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($wedding_package->transport_id)
                                                <div class="col-sm-6">
                                                    <div class="tab-inner-title-light">@lang('messages.Transport')</div>
                                                    <div class="card-ptext-margin m-b-8">
                                                        <div >
                                                            <div class="card-ptext-content-50">
                                                                @php
                                                                    $wedding_transport = $transports->where('id',$wedding_package->transport_id)->first();
                                                                @endphp
                                                                @if ($wedding_transport)
                                                                    <div class="ptext-title">@lang('messages.Name')</div>
                                                                    <div class="ptext-value">{{ $wedding_transport->brand }} - {{ $wedding_transport->name }}</div>
                                                                    <div class="ptext-title">@lang('messages.Capacity')</div>
                                                                    <div class="ptext-value">{{ $wedding_transport->capacity }} @lang('messages.Seats')</div>
                                                                    <div class="ptext-title">@lang('messages.Type')</div>
                                                                    <div class="ptext-value">@lang('messages.Airport Shuttle')</div>
                                                                    <div class="ptext-title">@lang('messages.Destination')</div>
                                                                    <div class="ptext-value">@lang('messages.Arrival') & @lang('messages.Departure')</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding_package->additional_service_id)
                                                <div class="col-sm-6">
                                                    <div class="tab-inner-title-light">@lang('messages.Additional Services')</div>
                                                    <div class="card-ptext-margin m-b-8">
                                                        @php
                                                            $addser_ids = json_decode($wedding_package->additional_service_id, true);
                                                        @endphp
                                                        <div >
                                                            @if ( $addser_ids)
                                                            <ul class="card-ptext-content-50">

                                                                @foreach ($addser_ids as $addser_no=>$addser_id)
                                                                    @php
                                                                        $additional_service = $vendor_packages->where('id',$addser_id)->first();
                                                                    @endphp
                                                                    @if ($additional_service)
                                                                        <li>{{ $additional_service->service }}</li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                        </div>
                                        
                                        <div class="row ">
                                            @if ($wedding_package->include)
                                                <div class="col-12 col-sm-12">
                                                    <div class="tab-inner-title-light">@lang('messages.Include')</div>
                                                    <div class="card-ptext-margin">
                                                        <p>{!! $wedding_package->include !!}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding_package->payment_process)
                                                <div class="col-12 col-sm-12">
                                                    <div class="tab-inner-title-light">@lang('messages.Payment Proccess')</div>
                                                    <div class="card-ptext-margin">
                                                        <p>{!! $wedding_package->payment_process !!}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding_package->cancellation_policy)
                                                <div class="col-12 col-sm-12">
                                                    <div class="tab-inner-title-light">@lang('messages.Cancellation Policy')</div>
                                                    <div class="card-ptext-margin">
                                                        <p>{!! $wedding_package->cancellation_policy !!}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding_package->terms_and_conditions)
                                                <div class="col-12 col-sm-12">
                                                    <div class="tab-inner-title-light">@lang('messages.Terms and Conditions')</div>
                                                    <div class="card-ptext-margin">
                                                        <p>{!! $wedding_package->terms_and_conditions !!}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="tab-inner-title-light">@lang('messages.Rate')</div>
                                                <div class="card-ptext-margin">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="card-subtitle">@lang('messages.Week Day Price')</div>
                                                            <div class="card-subtitle">@lang('messages.Holiday Price')</div>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="usd-rate">
                                                                {{ currencyFormatUsd($wedding_package->week_day_price) }} 
                                                            </div>
                                                            <div class="usd-rate">
                                                                {{ currencyFormatUsd($wedding_package->holiday_price) }}
                                                            </div>
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>