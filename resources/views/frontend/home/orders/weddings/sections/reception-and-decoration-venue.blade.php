
{{-- RECEPTION VENUE --}}
@if ($receptionVenues)
    <div id="orderWeddingReceptionVenue" class="col-md-6">
        <div class="page-subtitle {{ $receptionVenue?"":"empty-value" }}">
            @lang('messages.Reception Venue')
            <div class="action-container">
                @if (count($receptionVenues)>0)
                    @if ($receptionVenue)
                        <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-reception-venue-{{ $receptionVenue->id }}">
                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                        </button>
                        @if ($orderWedding->service != "Reception Venue")
                            @if ($orderWedding->service != "Wedding Package")
                                <form id="deleteReceptionVenue" action="/fdelete-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                </form>
                                <button type="submit" form="deleteReceptionVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                    <i class="icon-copy fa fa-trash"></i>
                                </button>
                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-reception-venue-{{ $orderWedding->id }}">
                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                </button>
                            @endif
                        @else
                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-reception-venue-{{ $orderWedding->id }}">
                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                            </button>
                        @endif
                        {{-- MODAL DETAIL RECEPTION VENUE --}}
                        <div class="modal fade" id="detail-reception-venue-{{ $receptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Reception Venue')</div>
                                        </div>
                                        <div class="modal-img-container">
                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/reception-venues/' . $receptionVenue->cover) }}" alt="{{ $receptionVenue->name }}">
                                            <div class="modal-service-name">
                                                {{ $receptionVenue->name }}
                                                <p>{{ '@ '.$hotel->name }} - {{ $receptionVenue->name }}</p>
                                                <p>{{ dateTimeFormat($orderWedding->reception_date_start) }}</p>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-sm-12">
                                                        <div class="modal-subtitle">@lang('messages.Additional Information')</div>
                                                        {!! $receptionVenue->additional_info !!}
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
                    @else
                        @if ($orderWedding->service != "Wedding Package")
                            <span>
                                <a href="#" data-toggle="modal" data-target="#update-reception-venue-{{ $orderWedding->id }}"> 
                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                </a>
                            </span>
                        @endif
                    @endif
                @endif
            </div>
        </div>
        
        @if ($receptionVenue)
            <div class="card-ptext-margin">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Reception Venue')
                        </td>
                        <td class="htd-2">
                            {{ $receptionVenue->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Date')
                        </td>
                        <td class="htd-2">
                            {{ dateTimeFormat($orderWedding->reception_date_start) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Invitations')
                        </td>
                        <td class="htd-2">
                            {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                        </td>
                    </tr>
                </table>
                @if ($receptionVenue->capacity < $orderWedding->number_of_invitations)
                    <div class="notification-boxed">
                        <p>The reception venue can only accommodate {{ $receptionVenue->capacity }} people out of the total {{ $orderWedding->reception_venue_invitations }} wedding invitations.</p>
                    </div>
                @endif
            </div>
        @endif
        @if ($orderWedding->service != "Wedding Package")
            {{-- MODAL UPDATE RECEPTION VENUE --}}
            <div class="modal fade" id="update-reception-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                @if ($receptionVenue)
                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                @else
                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                @endif
                            </div>
                            <form id="updateReceptionVenue" action="/fupdate-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang("messages.Select Reception Venue") <span>*</span></label>
                                            <div class="card-box-content">
                                                @foreach ($receptionVenues as $rec_v_id=>$reception_venue)
                                                    @if ($orderWedding->number_of_invitation > $reception_venue->capacity)
                                                        <input disabled type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                        <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                <div class="name-card">
                                                                    <b>{{ $reception_venue->name }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                            </div>
                                                            <div class="overlay-label-radio">
                                                                @lang('messages.Not enough space')
                                                            </div>
                                                        </label>
                                                    @else
                                                        @if ($orderWedding->reception_venue_id)
                                                            @if ($orderWedding->reception_venue_id == $reception_venue->id)
                                                                <input checked type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $reception_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @else
                                                                <input type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $reception_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @else
                                                            <input type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                            <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $reception_venue->name }}</b>
                                                                    </div>
                                                                    <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                </div>
                                                            </label>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @if ($orderWedding->service == "Wedding Package")
                                        <input type="hidden" name="reception_date_start" value="{{ $orderWedding->wedding_date." 18:00" }}">
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reception_date_start">@lang("messages.Reception Date") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <input name="reception_date_start" type="text" class="form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ dateFormat($orderWedding->reception_date_start) }}" required>
                                                </div>
                                                @error('reception_date_start')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reception_venue_invitations">@lang("messages.Number of Invitations") <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-torsos-all"></i></span>
                                                <input name="reception_venue_invitations" max="{{ $orderWedding->reception_venue_invitation }}" type="number" class="form-control input-icon @error('reception_venue_invitations') is-invalid @enderror" placeholder="@lang('messages.Number of Invitations')" type="text" value="{{ $orderWedding->reception_venue_invitations }}" required>
                                            </div>
                                            @error('reception_venue_invitations')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="card-box-footer">
                                @if ($receptionVenue)
                                    <button type="submit" form="updateReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                @else
                                    <button type="submit" form="updateReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                @endif
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
{{-- DECORATION RECEPTION VENUE --}}
@if ($receptionVenues)
    <div id="orderWeddingDecorationReceptionVenue" class="col-md-6">
        @php
            $reception_v_decorations = $vendor_packages->where('type','Reception Venue Decoration');
        @endphp
        <div class="page-subtitle {{ $orderWedding->reception_venue_decoration_id?"":"empty-value"; }}">
            @lang('messages.Decoration')
            <div class="action-container">
                <form id="deleteDecorationReceptionVenue" action="/fdelete-decoration-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                </form>
                @if (count($reception_v_decorations)>0)
                    @if ($orderWedding->reception_venue_decoration_id)
                        <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}">
                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                        </button>
                        @if ($orderWedding->service != "Wedding Package")
                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-reception-venue-{{ $orderWedding->id }}">
                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                            </button>
                            <button type="submit" form="deleteDecorationReceptionVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                <i class="icon-copy fa fa-trash"></i>
                            </button>
                        @endif
                    @else
                        @if ($orderWedding->service != "Wedding Package")
                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-reception-venue-{{ $orderWedding->id }}">
                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif
                @endif
            </div>
        </div>
        @if ($receptionVenue)
            <div class="card-ptext-margin">
                @if ($orderWedding->reception_venue_decoration_id)
                    {{-- MODAL DETAIL DECORATION RECEPTION VENUE --}}
                    <div class="modal fade" id="detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Reception Venue Decoration')</div>
                                    </div>
                                    <div class="card-banner">
                                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $decorationReceptionVenue->cover) }}" alt="{{ $decorationReceptionVenue->service }}">
                                    </div>
                                    <div class="card-content">
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-12 text-center">
                                                    <div class="card-subtitle">{{ $decorationReceptionVenue->service }}</div>
                                                    <p>{{ '@ '.$receptionVenue->name }}</p>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr class="form-hr">
                                                    {!! $decorationReceptionVenue->description !!}
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
                                {{ $decorationReceptionVenue->service }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Date')
                            </td>
                            <td class="htd-2">
                                {{ dateTimeFormat($orderWedding->reception_date_start) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Invitations')
                            </td>
                            <td class="htd-2">
                                {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="description">
                        @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                    </div>
                @endif
            </div>
            {{-- MODAL ADD DECORATION TO RECEPTION VENUE  --}}
            <div class="modal fade" id="update-decoration-reception-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                @if ($decorationReceptionVenue)
                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Reception Venue Decoration')</div>
                                @else
                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Decoration')</div>
                                @endif
                            </div>
                            <form id="updateDecorationReceptionVenue" action="/fupdate-decoration-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang("messages.Select one") <span>*</span></label>
                                            <div class="card-box-content">
                                                @foreach ($reception_v_decorations as $dcv_id=>$decoration_c_venue)
                                                    @if ($orderWedding->reception_venue_decoration_id)
                                                        @if ($orderWedding->reception_venue_decoration_id == $decoration_c_venue->id)
                                                            <input checked type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                            <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $decoration_c_venue->service }}</b>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @else
                                                            <input type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
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
                                                        <input type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}">
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
                                @if ($decorationReceptionVenue)
                                    <button type="submit" form="updateDecorationReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                @else
                                    <button type="submit" form="updateDecorationReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-Plus-circle"></i> @lang('messages.Add')</button>
                                @endif
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif