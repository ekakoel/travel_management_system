
{{-- CEREMONY VENUE --}}
    <div id="ceremonyVenue" class="col-md-6">
        <div class="page-subtitle {{ $ceremonyVenue?"":"empty-value" }}">
            @lang('messages.Ceremony Venue')
            <div class="action-container">
                @if ($ceremonyVenue)
                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-ceremony-venue-{{ $ceremonyVenue->id }}">
                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                    </button>
                @else
                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                    </button>
                @endif
                @if ($orderWedding->service != "Wedding Package")
                    @if ($orderWedding->service != "Ceremony Venue")
                        @if ($orderWedding->ceremony_venue_id)
                            <form id="deleteCeremonyVenue" action="/fdelete-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                            </form>
                            <button form="deleteCeremonyVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove">
                                <i class="icon-copy fa fa-trash"></i>
                            </button>
                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                <i class="icon-copy fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                        </button>
                    @endif
                @endif
            </div>
        </div>
        @if ($ceremonyVenue)
            {{-- MODAL DETAIL CEREMONY VENUE --}}
            <div class="modal fade" id="detail-ceremony-venue-{{ $ceremonyVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-bank"></i>@lang('messages.Ceremony Venue')</div>
                            </div>
                            <div class="modal-img-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremonyVenue->cover) }}" alt="{{ $ceremonyVenue->type }}">
                                <div class="modal-service-name">
                                    <b>{{ $ceremonyVenue->name }}</b>
                                    <p>{{ '@ '.$hotel->name }}</p>
                                    <p>{{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                </div>
                            </div>
                            
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-ptext-margin">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Ceremony Venue')
                        </td>
                        <td class="htd-2">
                            {{ $ceremonyVenue->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Date')
                        </td>
                        <td class="htd-2">
                            {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                            {{ date('(H.i)',strtotime($orderWedding->slot)) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Invitations')
                        </td>
                        <td class="htd-2">
                            {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                            @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                <i>(@lang('messages.Max'): {{ $ceremonyVenue->capacity }})</i>
                            @endif
                        </td>
                    </tr>
                </table>
                @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                    @php
                        $guest_outside = $orderWedding->number_of_invitation - $ceremonyVenue->capacity;
                    @endphp
                    <div class="notification-boxed">
                        <p>The ceremony venue can only accommodate {{ $ceremonyVenue->capacity }} guests, {{ $guest_outside }} guests will not have a place during the wedding ceremony.</p>
                    </div>
                @endif
            </div>
        @endif
        @if ($orderWedding->service != "Wedding Package")
            {{-- MODAL UPDATE CEREMONY VENUE  --}}
            <div class="modal fade" id="update-wedding-order-ceremony-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
                            </div>
                            <form id="updateWeddingOrderCeremonyVenue" action="/fupdate-wedding-order-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang("messages.Select one") <span>*</span></label>
                                            <div class="card-box-content">
                                                @foreach ($ceremonyVenues as $cv_id=>$c_venue)
                                                    @if ($orderWedding->number_of_invitation > $c_venue->capacity)
                                                        <input disabled type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                        <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                <div class="name-card">
                                                                    <b>{{ $c_venue->name }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                            </div>
                                                            <div class="overlay-label-radio">
                                                                @lang('messages.Not enough space')
                                                            </div>
                                                        </label>
                                                        @error('ceremonial_venue_id')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    @else
                                                        <input {{ $orderWedding->ceremony_venue_id == $c_venue->id?'checked':'' }} type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                        <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                <div class="name-card">
                                                                    <b>{{ $c_venue->name }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                            </div>
                                                        </label>
                                                        @error('ceremonial_venue_id')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr class="form-hr">
                                    </div>
                                    @if ($orderWedding->service == "Reception Venue")
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    @if ($orderWedding->wedding_date)
                                                        <input readonly name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" value="{{ date("m/d/Y",strtotime($orderWedding->wedding_date)) }}" required>
                                                    @else
                                                        <input readonly name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" value="{{ old("wedding_date") }}" required>
                                                    @endif
                                                </div>
                                                @error('wedding_date')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="slot">Slot<span> *</span></label>
                                            <select name="slot" id="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                @if ($ceremonyVenue)
                                                    <option value="">Select Slot</option>
                                                    @php
                                                        $wv_slots = json_decode($ceremonyVenue->slot);
                                                        $wv_basic_price = json_decode($ceremonyVenue->basic_price);
                                                        $wv_arrangement_price = json_decode($ceremonyVenue->arrangement_price);
                                                        $c_slot = count($wv_slots);
                                                    @endphp
                                                    @for ($slt = 0; $slt < $c_slot; $slt++)
                                                        <option value="{{ $wv_slots[$slt] }}" {{ $orderWedding->slot == $wv_slots[$slt]?'selected':'' }} data-basic-price="{{ $wv_basic_price[$slt] }}" data-arrangement-price="{{ $wv_arrangement_price[$slt] }}">{{ $wv_slots[$slt] }}</option>
                                                    @endfor
                                                @endif
                                                
                                            </select>
                                            @error('slot')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="number_of_invitation">@lang("messages.Number of Invitations") <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                <input name="number_of_invitation" type="number" max="" class="form-control input-icon @error('number_of_invitation') is-invalid @enderror" placeholder="Number of invitations" value="{{ $orderWedding->number_of_invitation }}" required>
                                            </div>
                                            @error('number_of_invitation')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="basic_price" id="basic_price">
                                <input type="hidden" name="arrangement_price" id="arrangement_price">
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="updateWeddingOrderCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    {{-- DECORATION CEREMONY VENUE --}}
    <div id="decoration-ceremony-venue" class="col-md-6">
        @php
            $cv_decorations = $vendor_packages->where('type','Ceremony Venue Decoration');
        @endphp
        {{-- icon pada page subtitle masih menumpuk --}}
        <div class="page-subtitle {{ $orderWedding->ceremony_venue_decoration_id?"":"empty-value" }}">
            @lang('messages.Decoration')
            <div class="action-container">
                @if ($ceremonyVenue)
                    @if (count($cv_decorations)>0)
                        @if ($orderWedding->ceremony_venue_decoration_id)
                            <form id="deleteDecorationCeremony" action="/fdelete-decoration-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                            </form>
                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}">
                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                            </button>
                            @if ($orderWedding->service != "Wedding Package")
                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                    <i class="icon-copy fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                </button>
                                <button type="submit" class="btn-icon" form="deleteDecorationCeremony" onclick="return confirm('Are you sure?');"  data-toggle="tooltip" data-placement="top" title="Remove">
                                    <i class="icon-copy fa fa-trash"></i>
                                </button>
                            @endif
                        @else
                            @if ($orderWedding->service != "Wedding Package")
                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                </button>
                            @endif
                        @endif
                    @endif
                @endif
            </div>
        </div>
        @if ($ceremonyVenue)
            <div class="card-ptext-margin">
                @if ($orderWedding->ceremony_venue_decoration_id)
                    {{-- MODAL DETAIL DECORATION CEREMONY VENUE --}}
                    <div class="modal fade" id="detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Ceremony Venue Decoration')</div>
                                    </div>
                                    <div class="modal-img-container">
                                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $ceremonyVenueDecoration->cover) }}" alt="{{ $ceremonyVenueDecoration->service }}">
                                        <div class="modal-service-name">
                                            <b>{{ $ceremonyVenueDecoration->service }}</b>
                                            <p>{{ '@ '.$ceremonyVenue->name }}</p>
                                            <p>{{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-12 text-center">
                                                    {!! $ceremonyVenueDecoration->description !!}
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
                    <table class="table tb-list">
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Decoration')
                            </td>
                            <td class="htd-2">
                                {{ $ceremonyVenueDecoration->service }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Date')
                            </td>
                            <td class="htd-2">
                                {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                {{ date('(H.i)',strtotime($orderWedding->slot)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Invitations')
                            </td>
                            <td class="htd-2">
                                {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                    <i>(@lang('messages.Max'): {{ $ceremonyVenue->capacity }})</i>
                                @endif
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="description">
                        @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                    </div>
                @endif
            </div>
            {{-- MODAL ADD DECORATION TO CEREMONY VENUE  --}}
            <div class="modal fade" id="update-decoration-ceremony-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Decoration')</div>
                            </div>
                            <form id="updateDecorationCeremonyVenue" action="/fupdate-decoration-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang("messages.Select one") <span>*</span></label>
                                            <div class="card-box-content">
                                                @foreach ($cv_decorations as $dcv_id=>$decoration_c_venue)
                                                    @if ($orderWedding->ceremony_venue_decoration_id)
                                                        @if ($orderWedding->ceremony_venue_decoration_id == $decoration_c_venue->id)
                                                            <input checked type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                            <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $decoration_c_venue->service }}</b>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @else
                                                            <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                            <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $decoration_c_venue->service }}</b>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endif
                                                    @else
                                                        <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}">
                                                        <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                <div class="name-card">
                                                                    <b>{{ $decoration_c_venue->service }}</b>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="basic_price" id="basic_price">
                                <input type="hidden" name="arrangement_price" id="arrangement_price">
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="updateDecorationCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Change')</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
