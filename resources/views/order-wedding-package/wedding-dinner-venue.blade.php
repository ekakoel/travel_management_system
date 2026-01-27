
{{-- DINNER VENUE --}}
@if ($orderWedding->dinner_venue_id)
    <div id="weddingDinnerVenue" class="col-md-6">
        <div class="page-subtitle {{ $dinnerVenue?"":"empty-value" }}">
            @lang('messages.Dinner Venue')
            @if ($dinnerVenues)
                @if ($dinnerVenue)
                    @if ($orderWedding->service != "Wedding Package")
                        <span>
                            <form action="/fdelete-dinner-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                            </form>
                        </span>
                        <span class="p-r-8">
                            <a href="#" data-toggle="modal" data-target="#update-dinner-venue-{{ $orderWedding->id }}"> 
                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    <span class="{{ $orderWedding->service != "Wedding Package"?"p-r-8":"" }}">
                        <a href="#" data-toggle="modal" data-target="#detail-dinner-venue-{{ $dinnerVenue->id }}"> 
                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                        </a>
                    </span>
                @else
                    @if ($orderWedding->service != "Wedding Package")
                        <span>
                            <a href="#" data-toggle="modal" data-target="#update-dinner-venue-{{ $orderWedding->id }}"> 
                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                @endif
            @endif
        </div>
        
        @if ($dinnerVenue)
            {{-- MODAL DETAIL DINNER VENUE --}}
            <div class="modal fade" id="detail-dinner-venue-{{ $dinnerVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Dinner Venue')</div>
                            </div>
                            <div class="modal-img-container">
                                <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/dinner-venues/' . $dinnerVenue->cover) }}" alt="{{ $dinnerVenue->name }}">
                                <div class="modal-service-name">
                                    {{ $dinnerVenue->name }}
                                    <p>{{ '@ '.$hotel->name }} - {{ $dinnerVenue->name }}</p>
                                    <p>{{ dateTimeFormat($orderWedding->dinner_venue_date) }}</p>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-text">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            {!! $dinnerVenue->description !!}
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
                            @lang('messages.Dinner Venue')
                        </td>
                        <td class="htd-2">
                            {{ $dinnerVenue->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Date')
                        </td>
                        <td class="htd-2">
                            {{ dateTimeFormat($orderWedding->dinner_venue_date) }}
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
                @if ($dinnerVenue->capacity < $orderWedding->number_of_invitations)
                    <div class="notification-boxed">
                        <p>The dinner venue can only accommodate {{ $dinnerVenue->capacity }} people out of the total {{ $orderWedding->number_of_invitation }} wedding invitations.</p>
                    </div>
                @endif
            </div>
        @endif
        @if ($orderWedding->service != "Wedding Package")
            {{-- MODAL UPDATE DINNER VENUE --}}
            <div class="modal fade" id="update-dinner-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                @if ($dinnerVenue)
                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Dinner Venue')</div>
                                @else
                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Dinner Venue')</div>
                                @endif
                            </div>
                            <form id="updateDinnerVenue" action="/fupdate-dinner-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang("messages.Select Dinner Venue") <span>*</span></label>
                                            <div class="card-box-content">
                                                @foreach ($dinnerVenues as $rec_v_id=>$dinner_venue)
                                                    
                                                    @if ($orderWedding->number_of_invitation > $dinner_venue->capacity)
                                                        <input disabled type="radio" id="{{ "rv".$rec_v_id }}" name="dinner_venue_id" value="{{ $dinner_venue->id }}">
                                                        <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                                                <div class="name-card">
                                                                    <b>{{ $dinner_venue->name }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $dinner_venue->capacity." guests" }}</div>
                                                            </div>
                                                            <div class="overlay-label-radio">
                                                                @lang('messages.Not enough space')
                                                            </div>
                                                        </label>
                                                    @else
                                                        @if ($orderWedding->dinner_venue_id)
                                                            @if ($orderWedding->dinner_venue_id == $dinner_venue->id)
                                                                <input checked type="radio" id="{{ "rv".$rec_v_id }}" name="dinner_venue_id" value="{{ $dinner_venue->id }}">
                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/dinner-venues/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $dinner_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $dinner_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @else
                                                                <input type="radio" id="{{ "rv".$rec_v_id }}" name="dinner_venue_id" value="{{ $dinner_venue->id }}">
                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/dinner-venues/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $dinner_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $dinner_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @else
                                                            <input type="radio" id="{{ "rv".$rec_v_id }}" name="dinner_venue_id" value="{{ $dinner_venue->id }}">
                                                            <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                <div class="card h-100">
                                                                    <img class="card-img" src="{{ asset ('storage/weddings/dinner-venues/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $dinner_venue->name }}</b>
                                                                    </div>
                                                                    <div class="label-capacity">{{ $dinner_venue->capacity." guests" }}</div>
                                                                </div>
                                                            </label>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @if ($orderWedding->service == "Wedding Package")
                                        {{-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dinner_date_start">@lang('messages.Extra Bed') <span> * </span> <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Choose an extra bed if the room is occupied by more than 2 guests.')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <select name="dinner_date_start" type="text" class="form-control custom-select-icon @error('dinner_date_start') is-invalid @enderror" required>
                                                        @if ($orderWedding->dinner_date_start)
                                                            <option selected value="{{ $orderWedding->dinner_date_start }}">{{ dateFormat($orderWedding->dinner_date_start) }}</option>
                                                        @else
                                                            <option selected value="{{ $orderWedding->dinner_date_start }}">{{ dateFormat($orderWedding->dinner_date_start) }}</option>
                                                        @endif
                                                        @for ($dur = 0; $dur < $orderWedding->duration; $dur++)
                                                            <option selected value="{{ $orderWedding->dinner_date_start }}">{{ dateFormat("+".$dur." days", strtotime($orderWedding->checkin)) }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                @error('extra_bed[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <input type="hidden" name="dinner_date_start" value="{{ $orderWedding->wedding_date." 18:00" }}">
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dinner_date_start">@lang("messages.Dinner Date") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <input disabled name="dinner_date_start" type="text" class="form-control input-icon @error('dinner_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ dateFormat($orderWedding->dinner_date_start) }}" required>
                                                </div>
                                                @error('dinner_date_start')
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
                                @if ($dinnerVenue)
                                    <button type="submit" form="updateDinnerVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                @else
                                    <button type="submit" form="updateDinnerVenue" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
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
