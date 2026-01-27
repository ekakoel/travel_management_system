{{-- ACCOMMODATION --}}
@if ($wedding_planner->status == "Draft")
    @php
        $acc_no = 0;
    @endphp
    <div id="accommodations" class="col-md-12">
        <div class="page-subtitle m-b-8">@lang('messages.Accommodation')
        </div>
        <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
            <thead>
                <tr>
                    <th>@lang('messages.No')</th>
                    <th>@lang('messages.Date')</th>
                    <th>@lang('messages.Hotel')</th>
                    <th>@lang('messages.Suites & Villas')</th>
                    <th>@lang('messages.Guests')</th>
                    <th>@lang('messages.Number of Guests')</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ($bride_suite_villa)
                    <tr>
                        <td class="pd-2-8">
                            {{ ++$acc_no }}
                        </td>
                        <td class="pd-2-8">
                            {{ date('m/d',strtotime($bride_wedding_accommodation->checkin)) }} - {{ date('m/d',strtotime($bride_wedding_accommodation->checkout)) }}
                        </td>
                        <td class="pd-2-8">
                            {{ $hotel->name }}
                        </td>
                        <td class="pd-2-8">
                            {{ $bride_suite_villa->rooms }}
                        </td>
                        <td class="pd-2-8">
                            {{ $bride_wedding_accommodation->guest_detail }}
                        </td>
                        <td class="pd-2-8">
                            2 (@lang('messages.Bride'))
                        </td>
                        <td class="pd-2-8 text-right">
                            <div class="table-action">
                                <a href="#" data-toggle="modal" data-target="#update-wedding-planner-bride-accommodation-{{ $bride_wedding_accommodation->id }}">
                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Accommodation')"><i class="icon-copy fa fa-pencil"></i></button>
                                </a>
                                <form action="/fdelete-wedding-planner-bride-accommodation/{{ $bride_wedding_accommodation->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </div>
                            {{-- MODAL UPDATE WEDDING PLANNER BRIDE ACCOMMODATION --}}
                            <div class="modal fade" id="update-wedding-planner-bride-accommodation-{{ $bride_wedding_accommodation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-left">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <i class="icon-copy fa fa-hotel"></i> @lang('messages.Update Bride Accommodation')
                                            </div>
                                            <form id="updateWeddingPlannerBrideAccommodation-{{ $bride_wedding_accommodation->id }}" action="/fupdate-wedding-planner-bride-accommodation/{{ $bride_wedding_accommodation->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <div class="card-box-content">
                                                                <input checked type="radio" id="{{ "acc_rv".$bride_wedding_accommodation->id }}" name="rooms_id" value="{{ $bride_suite_villa->id }}">
                                                                <label for="{{ "acc_rv".$bride_wedding_accommodation->id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $bride_suite_villa->cover) }}" alt="{{ $bride_suite_villa->service }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $bride_suite_villa->rooms }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $bride_suite_villa->capacity." guests" }}</div>
                                                                    </div>
                                                                </label>
                                                                @foreach ($rooms as $room)
                                                                    @if ($room->id !== $bride_wedding_accommodation->rooms_id)
                                                                        <input type="radio" id="{{ "room_rv".$room->id }}" name="rooms_id" value="{{ $room->id }}">
                                                                        <label for="{{ "room_rv".$room->id }}" class="label-radio">
                                                                            <div class="card h-100">
                                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}">
                                                                                <div class="name-card">
                                                                                    <b>{{ $room->rooms }}</b>
                                                                                </div>
                                                                                <div class="label-capacity">{{ $room->capacity." guests" }}</div>
                                                                            </div>
                                                                        </label>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="updateWeddingPlannerBrideAccommodation-{{ $bride_wedding_accommodation->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
                @if ($invitations_wedding_accommodation)
                    @foreach ($invitations_wedding_accommodation as $inv_suite_villa)
                        @php
                            $invitation_accommodation = $rooms->where('id',$inv_suite_villa->rooms_id)->first();
                            $xbed = $extra_beds->where('id',$inv_suite_villa->extra_bed_id)->first();
                        @endphp
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$acc_no }}
                                </td>
                                <td class="pd-2-8">
                                    {{ date('m/d',strtotime($inv_suite_villa->checkin)) }} - {{ date('m/d',strtotime($inv_suite_villa->checkout)) }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $hotel->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $invitation_accommodation->rooms }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $inv_suite_villa->guest_detail }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $inv_suite_villa->number_of_guests }} (@lang('messages.Invitations'))
                                </td>
                                <td class="pd-2-8 text-right">
                                    <div class="table-action">
                                        <a href="#" data-toggle="modal" data-target="#update-wedding-planner-invitations-accommodation-{{ $inv_suite_villa->id }}">
                                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Accommodation')"><i class="icon-copy fa fa-pencil"></i></button>
                                        </a>
                                        <form action="/fdelete-wedding-planner-bride-accommodation/{{ $inv_suite_villa->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- MODAL UPDATE WEDDING PLANNER INVITATIONS ACCOMMODATION --}}
                            <div class="modal fade" id="update-wedding-planner-invitations-accommodation-{{ $inv_suite_villa->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-left">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <i class="icon-copy fa fa-hotel"></i> @lang('messages.Update Invitations Accommodation')
                                            </div>
                                            <form id="updateWeddingPlannerInvitationsAccommodation-{{ $inv_suite_villa->id }}" action="/fupdate-wedding-planner-invitations-accommodation/{{ $inv_suite_villa->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <div class="card-box-content">
                                                                @foreach ($rooms as $no_inv_room=>$room_inv)
                                                                    @php
                                                                        
                                                                    @endphp
                                                                    @if ($room_inv->id == $inv_suite_villa->rooms_id)
                                                                        <input checked type="radio" id="{{ "inv_acc_rv".++$no_inv_room.$inv_suite_villa->id }}" name="rooms_id" value="{{ $room_inv->id }}">
                                                                        <label for="{{ "inv_acc_rv".$no_inv_room.$inv_suite_villa->id }}" class="label-radio">
                                                                            <div class="card h-100">
                                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $room_inv->cover) }}" alt="{{ $room_inv->rooms }}">
                                                                                <div class="name-card">
                                                                                    <b>{{ $room_inv->rooms }}</b>
                                                                                </div>
                                                                                <div class="label-capacity">{{ $room_inv->capacity." guests" }}</div>
                                                                            </div>
                                                                        </label>
                                                                    @else
                                                                        <input type="radio" id="{{ "inv_acc_rv".++$no_inv_room.$inv_suite_villa->id }}" name="rooms_id" value="{{ $room_inv->id }}">
                                                                        <label for="{{ "inv_acc_rv".$no_inv_room.$inv_suite_villa->id }}" class="label-radio">
                                                                            <div class="card h-100">
                                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $room_inv->cover) }}" alt="{{ $room_inv->rooms }}">
                                                                                <div class="name-card">
                                                                                    <b>{{ $room_inv->rooms }}</b>
                                                                                </div>
                                                                                <div class="label-capacity">{{ $room_inv->capacity." guests" }}</div>
                                                                            </div>
                                                                        </label>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                            <input type="number" name="number_of_guests" min="1" max="{{ $room_inv->capacity }}" class="form-control @error('number_of_guests') is-invalid @enderror"  placeholder="@lang('messages.Number of guests')" value="{{ $inv_suite_villa->number_of_guests }}">
                                                            @error('number_of_guests')
                                                                <span class="invalid-feedback">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="guest_detail">@lang('messages.Guests Name')</label>
                                                            <input type="text" name="guest_detail" class="form-control @error('guest_detail') is-invalid @enderror"  placeholder="@lang('messages.Guests Name')" value="{{ $inv_suite_villa->guest_detail }}">
                                                            @error('guest_detail')
                                                                <span class="invalid-feedback">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="extra_bed_id">@lang('messages.Extra Bed') <span>*</span></label>
                                                            <select name="extra_bed_id" class="custom-select @error('extra_bed_id') is-invalid @enderror">
                                                                @if ($xbed)
                                                                    <option value="">@lang('messages.None')</option>
                                                                    <option selected value="{{ $inv_suite_villa->extra_bed_id }}">{{ $xbed->type }}</option>
                                                                @else
                                                                    <option selected value="">@lang('messages.None')</option>
                                                                @endif
                                                                @foreach ($extra_beds as $extra_bed)
                                                                    <option value="{{ $extra_bed->id }}">{{ $extra_bed->type }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('extra_bed_id')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                            <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                                            @error('remark')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="updateWeddingPlannerInvitationsAccommodation-{{ $inv_suite_villa->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                @endif
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="#" data-toggle="modal" data-target="#add-wedding-planner-accommodation-{{ $wedding_planner->id }}">
                    <button class="btn btn-primary"><i class="icon-copy  fa fa-plus-circle"></i> @lang('messages.Accommodation')</button>
                </a>
                {{-- MODAL ADD ACCOMMODATION  --}}
                <div class="modal fade" id="add-wedding-planner-accommodation-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Accommodation')</div>
                                </div>
                                <form id="addWeddingPlannerAccommodations-{{ $wedding_planner->id }}" action="/fadd-wedding-planner-accommodation/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="rooms_id">@lang('messages.Select Room') <span>*</span></label>
                                                <div class="card-box-content">
                                                    @foreach ($rooms as $room)
                                                        <input type="radio" id="{{ "acc_rv".$room->id }}" name="rooms_id" value="{{ $room->id }}" data-room-capacity="{{ $room->capacity }}">
                                                        <label for="{{ "acc_rv".$room->id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}">
                                                                <div class="name-card">
                                                                    <b>{{ $room->rooms }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $room->capacity." guests" }}</div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @if (!$bride_wedding_accommodation)
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="room_for">@lang('messages.Guests') <span>*</span></label>
                                                    <select id="roomForForm" name="room_for" class="custom-select @error('room_for') is-invalid @enderror" required>
                                                        <option selected value="Couple">@lang('messages.Bride')</option>
                                                        <option value="Inv">@lang('messages.Invitations')</option>
                                                    </select>
                                                    @error('room_for')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <input id="roomForForm" type="hidden" name="room_for" value="Inv">
                                        @endif
                                        <div id="numberOfGuestsForm" hidden class="col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                <input id="number_of_guests_room" type="number" name="number_of_guests" min="1" max="{{ $room->capacity }}" class="form-control @error('number_of_guests') is-invalid @enderror"  placeholder="@lang('messages.Number of guests')" value="{{ old('number_of_guests') }}">
                                                @error('number_of_guests')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="guestsNameForm" hidden class="col-md-6">
                                            <div class="form-group">
                                                <label for="guest_detail">@lang('messages.Guests Name')</label>
                                                <input type="text" name="guest_detail" class="form-control @error('guest_detail') is-invalid @enderror"  placeholder="@lang('messages.Guests Name')" value="{{ old('guest_detail') }}">
                                                @error('guest_detail')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="extraBedIdForm" hidden class="col-sm-6">
                                            <div class="form-group">
                                                <label for="extra_bed_id">@lang('messages.Extra Bed') <span>*</span></label>
                                                <select name="extra_bed_id" class="custom-select col-12 @error('extra_bed_id') is-invalid @enderror">
                                                    <option selected value="0">@lang('messages.None')</option>
                                                    @foreach ($extra_beds as $extra_bed)
                                                        <option value="{{ $extra_bed->id }}">{{ $extra_bed->type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('extra_bed_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                                @error('remark')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addWeddingPlannerAccommodations-{{ $wedding_planner->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @if ($bride_wedding_accommodation or count($invitations_wedding_accommodation)>0)
        @php
            $acc_no = 0;
        @endphp
        <div id="accommodations" class="col-md-12">
            <div class="page-subtitle m-b-8">@lang('messages.Accommodation')</div>
            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th>@lang('messages.No')</th>
                        <th>@lang('messages.Date')</th>
                        <th>@lang('messages.Hotel')</th>
                        <th>@lang('messages.Suites & Villas')</th>
                        <th>@lang('messages.Guests')</th>
                        <th>@lang('messages.Number of Guests')</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($bride_suite_villa)
                        <tr>
                            <td class="pd-2-8">
                                {{ ++$acc_no }}
                            </td>
                            <td class="pd-2-8">
                                {{ date('m/d',strtotime($bride_wedding_accommodation->checkin)) }} - {{ date('m/d',strtotime($bride_wedding_accommodation->checkout)) }}
                            </td>
                            <td class="pd-2-8">
                                {{ $hotel->name }}
                            </td>
                            <td class="pd-2-8">
                                {{ $bride_suite_villa->rooms }}
                            </td>
                            <td class="pd-2-8">
                                {{ $bride_wedding_accommodation->guest_detail }}
                            </td>
                            <td class="pd-2-8">
                                2 (@lang('messages.Bride'))
                            </td>
                        </tr>
                    @endif
                    @if ($invitations_wedding_accommodation)
                        @foreach ($invitations_wedding_accommodation as $inv_suite_villa)
                            @php
                                $invitation_accommodation = $rooms->where('id',$inv_suite_villa->rooms_id)->first();
                                $xbed = $extra_beds->where('id',$inv_suite_villa->extra_bed_id)->first();
                            @endphp
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$acc_no }}
                                </td>
                                <td class="pd-2-8">
                                    {{ date('m/d',strtotime($inv_suite_villa->checkin)) }} - {{ date('m/d',strtotime($inv_suite_villa->checkout)) }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $hotel->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $invitation_accommodation->rooms }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $inv_suite_villa->guest_detail }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $inv_suite_villa->number_of_guests }} (@lang('messages.Invitations'))
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @endif
@endif