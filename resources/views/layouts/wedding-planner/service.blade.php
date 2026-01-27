{{-- SERVICE --}}
@if ($wedding_planner->status == "Draft")
    <div id="weddingPlannerService" class="col-md-12">
        <div class="page-subtitle">
            @lang("messages.Services")
        </div>
        <div class="row">
            <div class="col-md-12">
                @php
                    $tb_no = 0;
                @endphp
                <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                    <thead>
                        <tr>
                            <th style="width: 5%" class="datatable-nosort">@lang('messages.No')</th>
                            <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')/@lang('messages.Time')</th>
                            <th style="width: 20%" class="datatable-nosort">@lang('messages.Type')</th>
                            <th style="width: 30%" class="datatable-nosort">@lang('messages.Name')</th>
                            <th style="width: 15%" class="datatable-nosort">@lang('messages.Capacity')</th>
                            <th style="width: 10%" class="datatable-nosort"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- CEREMONY VENUE --}}
                        @if ($ceremony_venue)
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$tb_no }}
                                </td>
                                <td class="pd-2-8">
                                {{ date('m/d',strtotime($wedding_planner->wedding_date)) }} {{ date('(H.i)',strtotime($wedding_planner->slot)) }}
                            </td>
                                <td class="pd-2-8">
                                    @lang('messages.Ceremony Venue')
                                </td>
                                <td class="pd-2-8">
                                    {{ $ceremony_venue->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $ceremony_venue->capacity }} @lang('messages.guests')
                                </td>
                                <td class="pd-2-8 text-right">
                                    <div class="table-action">
                                        <a href="#" data-toggle="modal" data-target="#detail-ceremonial-venue-{{ $ceremony_venue->id }}">
                                            <i class="icon-copy fa fa-eye" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="modal" data-target="#update-wedding-ceremonial-venue-{{ $ceremony_venue->id }}">
                                            <i class="icon-copy fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                        </a>
                                        <form action="/fdelete-wedding-planner-ceremonial-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                                {{-- MODAL DETAIL CEREMONIAL VENUE --}}
                                <div class="modal fade" id="detail-ceremonial-venue-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy fa fa-bank" aria-hidden="true"></i>@lang('messages.Ceremony Venue')</div>
                                                </div>
                                                <div class="card-banner">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}">
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-text">
                                                        <div class="row ">
                                                            <div class="col-sm-12 text-center">
                                                                <div class="card-subtitle">{{ $ceremony_venue->name }}</div>
                                                                <p><i class="icon-copy dw dw-group"></i> {{ $wedding_planner->number_of_invitations }} @lang('messages.Invitations')</p>
                                                                <p>{{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) }} | {{ date('H.i',strtotime($wedding_planner->slot)) }} - {{ date('H.i',strtotime('+2 hours',strtotime($wedding_planner->slot))) }}</p>
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
                                {{-- MODAL UPDATE CEREMONIAL VENUE  --}}
                                <div class="modal fade" id="update-wedding-ceremonial-venue-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
                                                </div>
                                                <form id="updateWeddingPlannerCeremonialVenue-{{ $ceremony_venue }}" action="/fupdate-wedding-planner-ceremonial-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>@lang("messages.Select one") <span>*</span></label>
                                                                <div class="card-box-content">
                                                                    @foreach ($ceremony_venues as $cv_id=>$c_venue)
                                                                        @if ($c_venue->id == $wedding_planner->ceremonial_venue_id)
                                                                            <input checked type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
                                                                            <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                <div class="card h-100">
                                                                                    <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                                    <div class="name-card">
                                                                                        <b>{{ $c_venue->name }}</b>
                                                                                    </div>
                                                                                    <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                </div>
                                                                            </label>
                                                                        @else
                                                                            @if ($c_venue->capacity < $wedding_planner->number_of_invitations)
                                                                                <input disabled type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
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
                                                                            @else
                                                                                <input type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
                                                                                <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $c_venue->name }}1</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                    </div>
                                                                                </label>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="slot">@lang('messages.Slot') <span> *</span></label>
                                                                <select name="slot" id="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                                    @if ($wedding_planner->slot)
                                                                        <option value="{{ $wedding_planner->slot }}">{{ $wedding_planner->slot }}</option>
                                                                        @if ($ceremony_venue)
                                                                            @php
                                                                                $cv_slots = json_decode($ceremony_venue->slot);
                                                                            @endphp
                                                                            @foreach ($cv_slots as $slt)
                                                                                @if ($slt != $wedding_planner->slot)
                                                                                    <option value="{{ $slt }}">{{ $slt }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    @endif
                                                                </select>
                                                                @error('slot')
                                                                    <div class="alert-form">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="updateWeddingPlannerCeremonialVenue-{{ $ceremony_venue }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endif
                        {{-- RECEPTION VENUE --}}
                        @if ($reception_venue)
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$tb_no }}
                                </td>
                                <td class="pd-2-8">
                                @if ($wedding_planner->dinner_venue_time_start and $wedding_planner->dinner_venue_time_end)
                                    {{ date('m/d',strtotime($wedding_planner->dinner_venue_time_start)) }} | {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_start)) }} - {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_end)) }}
                                @else
                                    -
                                @endif
                            </td>
                                <td class="pd-2-8">
                                    @lang('messages.Reception Venue')
                                </td>
                                <td class="pd-2-8">
                                    {{ $reception_venue->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $reception_venue->capacity }} @lang('messages.guests')
                                </td>
                                <td class="pd-2-8 text-right">
                                    <div class="table-action">
                                        <a href="#" data-toggle="modal" data-target="#detail-reception-venue-{{ $reception_venue->id }}">
                                            <i class="icon-copy fa fa-eye" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="modal" data-target="#update-wedding-reception-venue-{{ $reception_venue->id }}">
                                            <i class="icon-copy fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                        </a>
                                        <form action="/fdelete-wedding-planner-reception-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                                {{-- MODAL DETAIL RECEPTION VENUE --}}
                                <div class="modal fade" id="detail-reception-venue-{{ $reception_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy fa fa-bank" aria-hidden="true"></i>@lang('messages.Reception Venue')</div>
                                                </div>
                                                <div class="card-banner">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/weddings/wedding-dinner/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-text">
                                                        <div class="row ">
                                                            <div class="col-sm-12 text-center">
                                                                <div class="card-subtitle">{{ $reception_venue->name }}</div>
                                                                <p><i class="icon-copy dw dw-group"></i> {{ $wedding_planner->number_of_invitations }} @lang('messages.Guests')</p>
                                                                <p>{{ date('Y-m-d',strtotime($wedding_planner->dinner_venue_time_start)) }} | {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_start)) }} - {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_end)) }}</p>
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
                                {{-- MODAL UPDATE RECEPTION VENUE --}}
                                <div class="modal fade" id="update-wedding-reception-venue-{{ $reception_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                                </div>
                                                <form id="updateReceptionVenue" action="/fupdate-wedding-planner-reception-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>@lang("messages.Select one") <span>*</span></label>
                                                                <div class="card-box-content">
                                                                    @foreach ($reception_venues as $add_rv_id=>$rec_venue)
                                                                        @if ($rec_venue->id == $wedding_planner->dinner_venue_id)
                                                                            <input checked type="radio" id="{{ "add_rv".$add_rv_id }}" name="dinner_venue_id" value="{{ $rec_venue->id }}" data-slots="{{ $rec_venue->slot }}">
                                                                            <label for="{{ "add_rv".$add_rv_id }}" class="label-radio">
                                                                                <div class="card h-100">
                                                                                    <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $rec_venue->cover) }}" alt="{{ $rec_venue->service }}">
                                                                                    <div class="name-card">
                                                                                        <b>{{ $rec_venue->name }}</b>
                                                                                    </div>
                                                                                    <div class="label-capacity">{{ $rec_venue->capacity." guests" }}</div>
                                                                                </div>
                                                                            </label>
                                                                        @else
                                                                            @if ($rec_venue->capacity < $wedding_planner->number_of_invitations)
                                                                                <input disabled  type="radio" id="{{ "add_rv".$add_rv_id }}" name="dinner_venue_id" value="{{ $rec_venue->id }}" data-slots="{{ $rec_venue->slot }}">
                                                                                <label for="{{ "add_rv".$add_rv_id }}" class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $rec_venue->cover) }}" alt="{{ $rec_venue->service }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $rec_venue->name }}</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">{{ $rec_venue->capacity." guests" }}</div>
                                                                                    </div>
                                                                                    <div class="overlay-label-radio">
                                                                                        @lang('messages.Not enough space')
                                                                                    </div>
                                                                                </label>
                                                                            @else
                                                                                <input  type="radio" id="{{ "add_rv".$add_rv_id }}" name="dinner_venue_id" value="{{ $rec_venue->id }}" data-slots="{{ $rec_venue->slot }}">
                                                                                <label for="{{ "add_rv".$add_rv_id }}" class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $rec_venue->cover) }}" alt="{{ $rec_venue->service }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $rec_venue->name }}</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">{{ $rec_venue->capacity." guests" }}</div>
                                                                                    </div>
                                                                                </label>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="dinner_venue_time_start">@lang('messages.Select Date') <span>*</span></label>
                                                                <select name="dinner_venue_time_start" class="custom-select @error('dinner_venue_time_start') is-invalid @enderror" required>
                                                                    @php
                                                                        $wp_duration = $wedding_planner->duration+1;
                                                                    @endphp
                                                                    <option selected value="{{ $wedding_planner->dinner_venue_time_start }}">{{ date('Y-m-d',strtotime($wedding_planner->dinner_venue_time_start)) }}</option>
                                                                    @for ($wp_day = 0; $wp_day < $wp_duration; $wp_day++)
                                                                        <option value="{{ date('Y-m-d',strtotime('+'.$wp_day.' days',strtotime($wedding_planner->checkin))) }}">
                                                                            {{ date('Y-m-d',strtotime('+'.$wp_day.' days',strtotime($wedding_planner->checkin))) }}
                                                                            @if ( date('Y-m-d',strtotime('+'.$wp_day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->checkin)))
                                                                                - @lang('messages.Check-in')
                                                                            @endif
                                                                            @if ( date('Y-m-d',strtotime('+'.$wp_day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->wedding_date)))
                                                                                - @lang('messages.Wedding Ceremony')
                                                                            @endif
                                                                            @if ( date('Y-m-d',strtotime('+'.$wp_day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->checkout)))
                                                                                - @lang('messages.Check-out')
                                                                            @endif
                                                                        </option>
                                                                    @endfor
                                                                </select>
                                                                @error('dinner_venue_time_start')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="time">@lang('messages.Slot') <span>*</span></label>
                                                                <select name="time" class="custom-select @error('time') is-invalid @enderror" required>
                                                                        <option selected value="{{ date('H.i',strtotime($wedding_planner->dinner_venue_time_start)) }}">{{ date('H.i',strtotime($wedding_planner->dinner_venue_time_start)) }}</option>
                                                                        <option value="16:00">04:00 pm</option>
                                                                        <option value="17:00">05:00 pm</option>
                                                                        <option value="18:00">06:00 pm</option>
                                                                        <option value="19:00">07:00 pm</option>
                                                                </select>
                                                                @error('time')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="updateReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                            
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 text-right">
                @if (!$wedding_planner->ceremonial_venue_id)
                    <a href="#" data-toggle="modal" data-target="#add-wedding-ceremonial-venue">
                        <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle"></i> @lang('messages.Ceremony Venue')</button>
                    </a>
                    {{-- MODAL ADD CEREMONIAL VENUE  --}}
                    <div class="modal fade" id="add-wedding-ceremonial-venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
                                    </div>
                                    <form id="addWeddingPlannerCeremonialVenue-{{ $wedding_planner->id }}" action="/fupdate-wedding-planner-ceremonial-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>@lang("messages.Select one") <span>*</span></label>
                                                    <div class="card-box-content">
                                                        @foreach ($ceremony_venues as $cv_id=>$c_venue)
                                                            @if ($c_venue->capacity < $wedding_planner->number_of_invitations)
                                                                <input disabled type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
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
                                                            @else
                                                                <input type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
                                                                <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $c_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="slot">@lang('messages.Slot') <span> *</span></label>
                                                    <select name="slot" id="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                        @if ($wedding_planner->slot)
                                                            <option value="{{ $wedding_planner->slot }}">{{ $wedding_planner->slot }}</option>
                                                            @if ($ceremony_venue)
                                                                @php
                                                                    $cv_slots = json_decode($ceremony_venue->slot);
                                                                @endphp
                                                                @foreach ($cv_slots as $slt)
                                                                    @if ($slt != $wedding_planner->slot)
                                                                        <option value="{{ $slt }}">{{ $slt }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                    </select>
                                                    @error('slot')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addWeddingPlannerCeremonialVenue-{{ $wedding_planner->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (!$wedding_planner->dinner_venue_id)
                    <a href="#" data-toggle="modal" data-target="#add-wedding-reception-venue">
                        <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle"></i> @lang('messages.Reception Venue')</button>
                    </a>
                    {{-- MODAL ADD RECEPTION VENUE  --}}
                    <div class="modal fade" id="add-wedding-reception-venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                    </div>
                                    <form id="addWeddingPlannerReceptionVenue-{{ $wedding_planner->id }}" action="/fupdate-wedding-planner-reception-venue/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>@lang("messages.Ceremony Venue")<span> *</span></label>
                                                    <p>@lang('messages.Select one')</p>
                                                    <div class="card-box-content">
                                                        @foreach ($reception_venues as $add_rv_id=>$rec_venue)
                                                            @if ($rec_venue->capacity < $wedding_planner->number_of_invitations)
                                                                <input disabled  type="radio" id="{{ "add_rv".$add_rv_id }}" name="dinner_venue_id" value="{{ $rec_venue->id }}" data-slots="{{ $rec_venue->slot }}">
                                                                <label for="{{ "add_rv".$add_rv_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $rec_venue->cover) }}" alt="{{ $rec_venue->service }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $rec_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $rec_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                    <div class="overlay-label-radio">
                                                                        @lang('messages.Not enough space')
                                                                    </div>
                                                                </label>
                                                            @else
                                                                <input  type="radio" id="{{ "add_rv".$add_rv_id }}" name="dinner_venue_id" value="{{ $rec_venue->id }}" data-slots="{{ $rec_venue->slot }}">
                                                                <label for="{{ "add_rv".$add_rv_id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/weddings/wedding-dinner/' . $rec_venue->cover) }}" alt="{{ $rec_venue->service }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $rec_venue->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $rec_venue->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="dinner_venue_time_start">@lang('messages.Select Date') <span>*</span></label>
                                                    <select name="dinner_venue_time_start" class="custom-select @error('dinner_venue_time_start') is-invalid @enderror" required>
                                                        @php
                                                            $range = $wedding_planner->duration+1;
                                                        @endphp
                                                        <option selected value="">@lang('messages.Select one')</option>
                                                        @for ($day = 0; $day < $range; $day++)
                                                            <option value="{{ date('Y-m-d',strtotime('+'.$day.' days',strtotime($wedding_planner->checkin))) }}">
                                                                {{ date('Y-m-d',strtotime('+'.$day.' days',strtotime($wedding_planner->checkin))) }}
                                                                @if ( date('Y-m-d',strtotime('+'.$day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->checkin)))
                                                                    - @lang('messages.Check-in')
                                                                @endif
                                                                @if ( date('Y-m-d',strtotime('+'.$day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->wedding_date)))
                                                                    - @lang('messages.Wedding Ceremony')
                                                                @endif
                                                                @if ( date('Y-m-d',strtotime('+'.$day.' days',strtotime($wedding_planner->checkin))) == date('Y-m-d',strtotime($wedding_planner->checkout)))
                                                                    - @lang('messages.Check-out')
                                                                @endif
                                                            </option>
                                                        @endfor
                                                        
                                                    </select>
                                                    @error('dinner_venue_time_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="time">@lang('messages.Slot') <span>*</span></label>
                                                    <select name="time" class="custom-select @error('time') is-invalid @enderror" required>
                                                            <option selected value="">@lang('messages.Select one')</option>
                                                            <option value="16:00">04:00 pm</option>
                                                            <option value="17:00">05:00 pm</option>
                                                            <option value="18:00">06:00 pm</option>
                                                            <option value="19:00">07:00 pm</option>
                                                    </select>
                                                    @error('time')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addWeddingPlannerReceptionVenue-{{ $wedding_planner->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    <div id="weddingPlannerService" class="col-md-12">
        <div class="page-subtitle">@lang("messages.Services")</div>
        <div class="row">
            <div class="col-md-12">
                @php
                    $tb_no = 0;
                @endphp
                <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                    <thead>
                        <tr>
                            <th style="width: 5%" class="datatable-nosort">@lang('messages.No')</th>
                            <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')/@lang('messages.Time')</th>
                            <th style="width: 20%" class="datatable-nosort">@lang('messages.Type')</th>
                            <th style="width: 30%" class="datatable-nosort">@lang('messages.Name')</th>
                            <th style="width: 15%" class="datatable-nosort">@lang('messages.Capacity')</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- WEDDING VENUE --}}
                        <tr>
                            <td class="pd-2-8">
                                {{ ++$tb_no }}
                            </td>
                            <td class="pd-2-8">
                                {{ date('m/d',strtotime($wedding_planner->checkin)) }} - {{ date('m/d',strtotime($wedding_planner->checkout)) }}
                            </td>
                            <td class="pd-2-8">
                                @lang('messages.Wedding Venue')
                            </td>
                            <td class="pd-2-8">
                                {{ $hotel->name }} (@lang('messages.Bride'))
                            </td>
                            <td class="pd-2-8">
                                2 @lang('messages.guests')
                            </td>
                        </tr>
                        {{-- CEREMONY VENUE --}}
                        @if ($ceremony_venue)
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$tb_no }}
                                </td>
                                <td class="pd-2-8">
                                    {{ date('m/d',strtotime($wedding_planner->wedding_date)) }} {{ date('(H.i)',strtotime($wedding_planner->slot)) }}
                                </td>
                                <td class="pd-2-8">
                                    @lang('messages.Ceremony Venue')
                                </td>
                                <td class="pd-2-8">
                                    {{ $ceremony_venue->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $ceremony_venue->capacity }} @lang('messages.guests')
                                </td>
                            </tr>
                        @endif
                        {{-- RECEPTION VENUE --}}
                        @if ($reception_venue)
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$tb_no }}
                                </td>
                                <td class="pd-2-8">
                                    @if ($wedding_planner->dinner_venue_time_start and $wedding_planner->dinner_venue_time_end)
                                        {{ date('m/d',strtotime($wedding_planner->dinner_venue_time_start)) }} | {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_start)) }} - {{ date('H.i',strtotime($wedding_planner->dinner_venue_time_end)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="pd-2-8">
                                    @lang('messages.Reception Venue')
                                </td>
                                <td class="pd-2-8">
                                    {{ $reception_venue->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $reception_venue->capacity }} @lang('messages.guests')
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
@endif