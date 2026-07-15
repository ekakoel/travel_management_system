{{-- ACCOMMODATION --}}
@php
    $acc_no = 0;
@endphp
<div id="accommodations" class="col-md-12">
    <div class="page-subtitle m-b-8">
        @lang('messages.Accommodations')
        <span>
            <a href="#" data-toggle="modal"
                data-target="#add-order-wedding-accommodation-{{ $orderWedding->id }}">
                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top"
                    title="@lang('messages.Add')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
    <table class="data-table table stripe hover nowrap no-footer dtr-inline">
        <thead>
            <tr>
                <th>@lang('messages.Date')</th>
                <th>@lang('messages.Suites & Villas')</th>
                <th>@lang('messages.Guests')</th>
                <th>@lang('messages.Number of Guests')</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if ($bride_accommodation)
                <tr>
                    <td class="pd-2-8">
                        {{ date("m/d/y", strtotime($orderWedding->checkin)) }} -
                        {{ date("m/d/y", strtotime($orderWedding->checkout)) }}
                    </td>
                    <td class="pd-2-8">
                        {{ $bride_accommodation->rooms }}
                    </td>
                    <td class="pd-2-8">
                        {{ $bride->groom }}
                        @if ($bride->groom_chinese)
                            ({{ $bride->groom_chinese }})
                        @endif
                        &
                        {{ $bride->bride }}
                        @if ($bride->bride_chinese)
                            ({{ $bride->bride_chinese }})
                        @endif
                    </td>
                    <td class="pd-2-8">
                        2 <i>(@lang('messages.Bride'))</i>
                    </td>
                    <td class="pd-2-8 text-right">
                        <div class="table-action">
                            <p><i>@lang('messages.Include')</i></p>
                        </div>
                    </td>
                </tr>
            @endif
            @if ($wedding_accommodations)
                @foreach ($wedding_accommodations as $wedding_accommodation)
                    @php
                        $invitation_accommodation = $rooms->where('id', $wedding_accommodation->rooms_id)->first();
                        $extra_bed_order = $wedding_accommodation->extra_bed_order;
                        if ($extra_bed_order) {
                            $extra_bed = $extra_bed_order->extra_bed;
                        }else{
                            $extra_bed = NULL;
                        }
                    @endphp
                    <tr>
                        <td class="pd-2-8">
                            {{ date("m/d/y", strtotime($wedding_accommodation->checkin)) }} -
                            {{ date("m/d/y", strtotime($wedding_accommodation->checkout)) }}
                        </td>
                        <td class="pd-2-8">
                            {{ $invitation_accommodation->rooms }}
                            @if ($wedding_accommodation->extra_bed_order)
                                {{ '+ ' . $extra_bed->type . ' Extra bed ' }}
                            @endif
                        </td>
                        <td class="pd-2-8">
                            {{ $wedding_accommodation->guest_detail }}
                        </td>
                        <td class="pd-2-8">
                            {{ $wedding_accommodation->number_of_guests }} (@lang('messages.Invitations'))
                        </td>
                        <td class="pd-2-8 text-right">
                            <div class="table-action">
                                <a href="#" data-toggle="modal"
                                    data-target="#update-order-wedding-accommodation-{{ $wedding_accommodation->id }}">
                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top"
                                        title="@lang('messages.Edit Accommodation')"><i class="icon-copy fa fa-pencil"></i></button>
                                </a>
                                <form
                                    action="/fdelete-order-wedding-accommodation/{{ $wedding_accommodation->id }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Are you sure?');"
                                        type="submit" data-toggle="tooltip" data-placement="top"
                                        title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    {{-- MODAL UPDATE INVITATIONS ACCOMMODATION --}}
                    <div class="modal fade"
                        id="update-order-wedding-accommodation-{{ $wedding_accommodation->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <i class="icon-copy fa fa-hotel"></i> @lang('messages.Edit Accommodation')
                                    </div>
                                    <form id="updateWeddingAccommodation-{{ $wedding_accommodation->id }}"
                                        action="/fupdate-order-wedding-accommodation/{{ $wedding_accommodation->id }}"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="card-box-content">
                                                        @foreach ($rooms as $no_inv_room => $room_inv)
                                                            @if ($room_inv->id == $wedding_accommodation->rooms_id)
                                                                <input checked type="radio"
                                                                    id="{{ 'inv_acc_rv' . ++$no_inv_room . $wedding_accommodation->id }}"
                                                                    name="rooms_id" value="{{ $room_inv->id }}">
                                                                <label
                                                                    for="{{ 'inv_acc_rv' . $no_inv_room . $wedding_accommodation->id }}"
                                                                    class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img"
                                                                            src="{{ asset('storage/hotels/hotels-room/' . $room_inv->cover) }}"
                                                                            alt="{{ $room_inv->rooms }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $room_inv->rooms }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">
                                                                            {{ $room_inv->capacity . ' guests' }}
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            @else
                                                                <input type="radio"
                                                                    id="{{ 'inv_acc_rv' . ++$no_inv_room . $wedding_accommodation->id }}"
                                                                    name="rooms_id" value="{{ $room_inv->id }}">
                                                                <label
                                                                    for="{{ 'inv_acc_rv' . $no_inv_room . $wedding_accommodation->id }}"
                                                                    class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img"
                                                                            src="{{ asset('storage/hotels/hotels-room/' . $room_inv->cover) }}"
                                                                            alt="{{ $room_inv->rooms }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $room_inv->rooms }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">
                                                                            {{ $room_inv->capacity . ' guests' }}
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($orderWedding->service != "Wedding Package")
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="checkin">@lang('messages.Check-in')</label>
                                                        <input readonly type="text" name="checkin" class="form-control date-picker @error('checkin') is-invalid @enderror" placeholder="@lang('messages.Check-in')" value="{{ $wedding_accommodation->checkin }}" required>
                                                        @error('checkin')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="checkout">@lang('messages.Check-out')</label>
                                                        <input readonly type="text" name="checkout" class="form-control date-picker @error('checkout') is-invalid @enderror" placeholder="@lang('messages.Check-out')" value="{{ $wedding_accommodation->checkout }}" required>
                                                        @error('checkout')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                    <input type="number" name="number_of_guests" min="1"
                                                        max="{{ $room_inv->capacity }}"
                                                        class="form-control @error('number_of_guests') is-invalid @enderror"
                                                        placeholder="@lang('messages.Number of guests')"
                                                        value="{{ $wedding_accommodation->number_of_guests }}">
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
                                                    <input type="text" name="guest_detail"
                                                        class="form-control @error('guest_detail') is-invalid @enderror"
                                                        placeholder="@lang('messages.Guests Name')"
                                                        value="{{ $wedding_accommodation->guest_detail }}">
                                                    @error('guest_detail')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="extra_bed_id">@lang('messages.Extra Bed')
                                                        <span>*</span> </label>
                                                    <select name="extra_bed_id"
                                                        class="custom-select @error('extra_bed_id') is-invalid @enderror">
                                                            <option value="">@lang('messages.None')</option>
                                                            @foreach ($extra_beds as $extraBed)
                                                                @if ($extra_bed_order)
                                                                    <option {{ $extraBed->id == $extra_bed_order->extra_bed_id?"selected":""; }} value="{{ $extraBed->id }}">{{ $extraBed->type }}</option>
                                                                @else
                                                                    <option value="{{ $extraBed->id }}">{{ $extraBed->type }}</option>
                                                                @endif
                                                            @endforeach
                                                    </select>
                                                    @error('extra_bed_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="remark"
                                                        class="form-label">@lang('messages.Remark')</label>
                                                    <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror"
                                                        placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                                    @error('remark')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>

                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit"
                                            form="updateWeddingAccommodation-{{ $wedding_accommodation->id }}"
                                            class="btn btn-primary"><i class="icon-copy fa fa-pencil"
                                                aria-hidden="true"></i> @lang('messages.Update')</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                                class="icon-copy fa fa-close" aria-hidden="true"></i>
                                            @lang('messages.Cancel')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- MODAL ADD ACCOMMODATION  --}}
    <div class="modal fade" id="add-order-wedding-accommodation-{{ $orderWedding->id }}" tabindex="-1"
        role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i>
                            @lang('messages.Accommodation')</div>
                    </div>
                    <form id="addWeddingPlannerAccommodations-{{ $orderWedding->id }}"
                        action="/fadd-order-wedding-accommodation/{{ $orderWedding->id }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="rooms_id">@lang('messages.Select Room') <span>*</span></label>
                                    <div class="card-box-content">
                                        @foreach ($rooms as $room)
                                            <input type="radio" id="{{ 'acc_rv' . $room->id }}" name="rooms_id"
                                                value="{{ $room->id }}"
                                                data-room-capacity="{{ $room->capacity }}">
                                            <label for="{{ 'acc_rv' . $room->id }}" class="label-radio">
                                                <div class="card h-100">
                                                    <img class="card-img"
                                                        src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}"
                                                        alt="{{ $room->rooms }}">
                                                    <div class="name-card">
                                                        <b>{{ $room->rooms }}</b>
                                                    </div>
                                                    <div class="label-capacity">{{ $room->capacity . ' guests' }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if ($orderWedding->service != "Wedding Package")
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="checkin">@lang('messages.Check-in')</label>
                                        <input readonly type="text" name="checkin" class="form-control date-picker @error('checkin') is-invalid @enderror" placeholder="@lang('messages.Check-in')" value="{{ old('checkin') }}" required>
                                        @error('checkin')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="checkout">@lang('messages.Check-out')</label>
                                        <input readonly type="text" name="checkout" class="form-control date-picker @error('checkout') is-invalid @enderror" placeholder="@lang('messages.Check-out')" value="{{ old('checkout') }}" required>
                                        @error('checkout')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div id="numberOfGuestsForm" hidden class="col-md-6">
                                <div class="form-group">
                                    <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                    <input id="number_of_guests_room" type="number" name="number_of_guests"
                                        min="1" max="{{ $room->capacity }}"
                                        class="form-control @error('number_of_guests') is-invalid @enderror"
                                        placeholder="@lang('messages.Number of guests')" value="{{ old('number_of_guests') }}"
                                        required>
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
                                    <input type="text" name="guest_detail"
                                        class="form-control @error('guest_detail') is-invalid @enderror"
                                        placeholder="@lang('messages.Guests Name')" value="{{ old('guest_detail') }}"
                                        required>
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
                                    <select name="extra_bed_id"
                                        class="custom-select col-12 @error('extra_bed_id') is-invalid @enderror">
                                        <option selected value="">@lang('messages.None')</option>
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
                                    <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror"
                                        placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                    @error('remark')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input id="roomForForm" type="hidden" name="room_for" value="Inv">
                        </div>
                    </form>
                    <div class="card-box-footer">
                        <button type="submit" form="addWeddingPlannerAccommodations-{{ $orderWedding->id }}"
                            class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                            @lang('messages.Add')</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

