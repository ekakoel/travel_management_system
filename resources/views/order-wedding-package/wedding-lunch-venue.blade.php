
{{-- LUNCH VENUE --}}
@if ($orderWedding->lunch_venue_id)
    @if ($orderWedding->service != "Wedding Package")
        <div id="weddingLunchVenue" class="col-md-6">
            @if ($lunchVenue)
                <div class="page-subtitle">
            @else
                <div class="page-subtitle empty-value">
            @endif
                @lang('messages.Lunch Venue')
                @if ($lunchVenues)
                    @if ($lunchVenue)
                        @if ($orderWedding->service != "Wedding Package")
                            <span>
                                <form action="/fdelete-lunch-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </span>
                            <span class="p-r-8">
                                <a href="#" data-toggle="modal" data-target="#update-lunch-venue-{{ $orderWedding->id }}"> 
                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                </a>
                            </span>
                        @endif
                        <span class="{{ $orderWedding->service != 'Wedding Package'?"p-r-8":""; }}">
                            <a href="#" data-toggle="modal" data-target="#detail-lunch-venue-{{ $lunchVenue->id }}"> 
                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                            </a>
                        </span>
                    @else
                        @if ($orderWedding->service != "Wedding Package")
                            <span>
                                <a href="#" data-toggle="modal" data-target="#update-lunch-venue-{{ $orderWedding->id }}"> 
                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                </a>
                            </span>
                        @endif
                    @endif
                @endif
            </div>
            @if ($orderWedding->service != "Wedding Package")
                {{-- MODAL UPDATE LUNCH VENUE --}}
                <div class="modal fade" id="update-lunch-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    @if ($lunchVenue)
                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Lunch Venue')</div>
                                    @else
                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Lunch Venue')</div>
                                    @endif
                                </div>
                                <form id="updateLunchVenue" action="/fupdate-lunch-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>@lang("messages.Select Lunch Venue") <span>*</span></label>
                                                <div class="card-box-content">
                                                    @foreach ($lunchVenues as $rec_v_id=>$lunch_venue)
                                                        
                                                        @if ($orderWedding->number_of_invitation > $lunch_venue->capacity)
                                                            <input disabled type="radio" id="{{ "rv".$rec_v_id }}" name="lunch_venue_id" value="{{ $lunch_venue->id }}">
                                                            <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/weddings/wedding-lunch/' . $lunch_venue->cover) }}" alt="{{ $lunch_venue->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $lunch_venue->name }}</b>
                                                                    </div>
                                                                    <div class="label-capacity">{{ $lunch_venue->capacity." guests" }}</div>
                                                                </div>
                                                                <div class="overlay-label-radio">
                                                                    @lang('messages.Not enough space')
                                                                </div>
                                                            </label>
                                                        @else
                                                            @if ($orderWedding->lunch_venue_id)
                                                                @if ($orderWedding->lunch_venue_id == $lunch_venue->id)
                                                                    <input checked type="radio" id="{{ "rv".$rec_v_id }}" name="lunch_venue_id" value="{{ $lunch_venue->id }}">
                                                                    <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                        <div class="card h-100">
                                                                            <img class="card-img" src="{{ asset ('storage/weddings/lunch-venues/' . $lunch_venue->cover) }}" alt="{{ $lunch_venue->name }}">
                                                                            <div class="name-card">
                                                                                <b>{{ $lunch_venue->name }}</b>
                                                                            </div>
                                                                            <div class="label-capacity">{{ $lunch_venue->capacity." guests" }}</div>
                                                                        </div>
                                                                    </label>
                                                                @else
                                                                    <input type="radio" id="{{ "rv".$rec_v_id }}" name="lunch_venue_id" value="{{ $lunch_venue->id }}">
                                                                    <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                        <div class="card h-100">
                                                                            <img class="card-img" src="{{ asset ('storage/weddings/lunch-venues/' . $lunch_venue->cover) }}" alt="{{ $lunch_venue->name }}">
                                                                            <div class="name-card">
                                                                                <b>{{ $lunch_venue->name }}</b>
                                                                            </div>
                                                                            <div class="label-capacity">{{ $lunch_venue->capacity." guests" }}</div>
                                                                        </div>
                                                                    </label>
                                                                @endif
                                                            @else
                                                                <input type="radio" id="{{ "rv".$rec_v_id }}" name="lunch_venue_id" value="{{ $lunch_venue->id }}">
                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/lunch-venues/' . $lunch_venue->cover) }}" alt="{{ $lunch_venue->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $lunch_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $lunch_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @if ($orderWedding->service == "Wedding Package")
                                            <input type="hidden" name="lunch_date_start" value="{{ $orderWedding->wedding_date." 18:00" }}">
                                        @else
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lunch_date_start">@lang("messages.Lunch Date") <span> *</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fi-calendar"></i></span>
                                                        <input disabled name="lunch_date_start" type="text" class="form-control input-icon @error('lunch_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ dateFormat($orderWedding->lunch_date_start) }}" required>
                                                    </div>
                                                    @error('lunch_date_start')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_invitation">@lang("messages.Number of Invitations") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-torsos-all"></i></span>
                                                    <input name="number_of_invitation" max="{{ $orderWedding->number_of_invitation }}" type="number" class="form-control input-icon @error('number_of_invitation') is-invalid @enderror" placeholder="@lang('messages.Number of Invitations')" type="text" value="{{ $orderWedding->number_of_invitation }}" required>
                                                </div>
                                                @error('number_of_invitation')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    @if ($lunchVenue)
                                        <button type="submit" form="updateLunchVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                    @else
                                        <button type="submit" form="updateLunchVenue" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                    @endif
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if ($lunchVenue)
            {{-- MODAL DETAIL LUNCH VENUE --}}
            <div class="modal fade" id="detail-lunch-venue-{{ $lunchVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Lunch Venue')</div>
                            </div>
                            <div class="modal-img-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/lunch-venues/' . $lunchVenue->cover) }}" alt="{{ $lunchVenue->name }}">
                                <div class="modal-service-name">
                                    {{ $lunchVenue->name }}
                                    <p>{{ '@ '.$hotel->name }} - {{ $lunchVenue->name }}</p>
                                    <p>{{ dateTimeFormat($orderWedding->lunch_venue_date) }}</p>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-text">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            <div class="modal-subtitle">@lang('messages.Additional Information')</div>
                                            {!! $lunchVenue->additional_info !!}
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
            <div class="box-price-container">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Lunch Venue')
                        </td>
                        <td class="htd-2">
                            {{ $lunchVenue->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Date')
                        </td>
                        <td class="htd-2">
                            {{ dateTimeFormat($orderWedding->lunch_venue_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Invitations')
                        </td>
                        <td class="htd-2">
                            {{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')
                        </td>
                    </tr>
                </table>
                @if ($lunchVenue->capacity < $orderWedding->number_of_invitations)
                    <div class="notification-boxed">
                        <p>The lunch venue can only accommodate {{ $lunchVenue->capacity }} people out of the total {{ $orderWedding->number_of_invitation }} wedding invitations.</p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div id="weddingLunchVenue" class="col-md-6">
            <div class="page-subtitle">
                @lang('messages.Lunch Venue')
                @if ($lunchVenue)
                    <span class="{{ $orderWedding->service != 'Wedding Package'?"p-r-8":""; }}">
                        <a href="#" data-toggle="modal" data-target="#detail-lunch-venue-{{ $lunchVenue->id }}"> 
                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                        </a>
                    </span>
                @endif
            </div>
            <div class="box-price-container">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Lunch Venue')
                        </td>
                        <td class="htd-2">
                            {{ $lunchVenue->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Date')
                        </td>
                        <td class="htd-2">
                            {{ dateTimeFormat($orderWedding->lunch_venue_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Invitations')
                        </td>
                        <td class="htd-2">
                            {{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if ($lunchVenue)
            {{-- MODAL DETAIL LUNCH VENUE --}}
            <div class="modal fade" id="detail-lunch-venue-{{ $lunchVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Lunch Venue')</div>
                            </div>
                            <div class="modal-img-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/lunch-venues/' . $lunchVenue->cover) }}" alt="{{ $lunchVenue->name }}">
                                <div class="modal-service-name">
                                    {{ $lunchVenue->name }}
                                    <p>{{ '@ '.$hotel->name }} - {{ $lunchVenue->name }}</p>
                                    <p>{{ dateTimeFormat($orderWedding->lunch_venue_date) }}</p>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-text">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            {!! $lunchVenue->description !!}
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
            
            @if ($lunchVenue->capacity < $orderWedding->number_of_invitations)
                <div class="notification-boxed">
                    <p>The lunch venue can only accommodate {{ $lunchVenue->capacity }} people out of the total {{ $orderWedding->number_of_invitation }} wedding invitations.</p>
                </div>
            @endif
        @endif
    @endif
@endif