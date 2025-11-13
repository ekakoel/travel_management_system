{{-- FLIGHT --}}
<div id="flightSchedule" class="col-md-12">
    @if (count($flights)>0)
        <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule')
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-flight-{{ $orderWedding->id }}"> 
                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add Schedule')" aria-hidden="true"></i>
                </a>
            </span>
        </div>
        <div class="row">
            @if ($flights)
                <div class="col-sm-12">
                    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                        <thead>
                            <tr>
                                <th>@lang('messages.Date')</th>
                                <th>@lang('messages.Flight')</th>
                                <th>@lang('messages.Type')</th>
                                <th>@lang('messages.Guests')</th>
                                <th>@lang('messages.Responsible Person')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($flights as $flight_no=>$order_wedding_flight)
                                <tr>
                                    <td class="pd-2-8">
                                        @if ($order_wedding_flight->time)
                                                {{ dateTimeFormat($order_wedding_flight->time) }}
                                            @else
                                                -
                                            @endif
                                    </td>
                                    <td class="pd-2-8">
                                        @if ($order_wedding_flight->flight)
                                            {{ $order_wedding_flight->flight }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="pd-2-8">
                                        {{ $order_wedding_flight->type }}
                                    </td>
                                    
                                    
                                    <td class="pd-2-8">
                                        {{ $order_wedding_flight->number_of_guests }}
                                        @if ($order_wedding_flight->group == "Brides")
                                            <i>(@lang("messages.Brides"))</i>
                                        @endif
                                    </td>
                                    <td class="pd-2-8">
                                        
                                        {{ $order_wedding_flight->guests?$order_wedding_flight->guests:"-"; }} {{ $order_wedding_flight->guests_contact? " ".$order_wedding_flight->guests_contact:""; }}
                                    </td>
                                    <td class="pd-2-8 text-right">
                                        <div class="table-action">
                                            <a href="#" data-toggle="modal" data-target="#update-order-wedding-flight-{{ $order_wedding_flight->id }}"> 
                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Flight')" aria-hidden="true"></i>
                                            </a>
                                            <form action="/fdelete-order-wedding-flight/{{ $order_wedding_flight->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('delete')
                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- MODAL UPDATE ORDER WEDDING FLIGHT  --}}
                                <div class="modal fade" id="update-order-wedding-flight-{{ $order_wedding_flight->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <i class="icon-copy ion-android-plane"></i> @lang('messages.Update Flight')
                                                </div>
                                                <form id="updateInvitationsFlight-{{ $order_wedding_flight->id }}" action="/fupdate-order-wedding-flight/{{ $order_wedding_flight->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="modal-form-container">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="form-group">
                                                                            <label for="flight">@lang('messages.Flight Number')</label>
                                                                            <input type="text" name="flight" class="form-control uppercase @error('flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $order_wedding_flight->flight }}" required>
                                                                            @error('flight')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="flight_group">@lang('messages.Group') <span>*</span></label>
                                                                            <select name="flight_group" class="custom-select col-12 @error('flight_group') is-invalid @enderror" required>
                                                                                @if ($order_wedding_flight->group == "Brides")
                                                                                    <option selected value="{{ $order_wedding_flight->group }}">{{ $order_wedding_flight->group }}</option>
                                                                                    <option value="Invitations">@lang('messages.Invitations')</option>
                                                                                @elseif($order_wedding_flight->group == "Invitations")
                                                                                    <option selected value="{{ $order_wedding_flight->group }}">{{ $order_wedding_flight->group }}</option>
                                                                                    <option value="Brides">@lang("messages.Bride's")</option>
                                                                                @else
                                                                                    <option selected value="">@lang('messages.Select one')</option>
                                                                                    <option value="Invitations">@lang('messages.Invitations')</option>  
                                                                                    <option value="Brides">@lang("messages.Bride's")</option>
                                                                                @endif
                                                                            </select>
                                                                            @error('flight_group')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="type">@lang('messages.Type') <span>*</span></label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                @if ($order_wedding_flight->type == "Arrival")
                                                                                    <option selected value="{{ $order_wedding_flight->type }}">{{ $order_wedding_flight->type }}</option>
                                                                                    <option value="Departure">@lang('messages.Departure')</option>
                                                                                @elseif($order_wedding_flight->type == "Departure")
                                                                                    <option selected value="{{ $order_wedding_flight->type }}">{{ $order_wedding_flight->type }}</option>
                                                                                    <option value="Arrival">@lang("messages.Arrival")</option>
                                                                                @else
                                                                                    <option selected value="">@lang('messages.Select one')</option>
                                                                                    <option value="Departure">@lang('messages.Departure')</option>  
                                                                                    <option value="Arrival">@lang("messages.Arrival")</option>
                                                                                @endif
                                                                            </select>
                                                                            @error('type')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                                            <input type="number" min="1"  name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror"   value="{{ $order_wedding_flight->number_of_guests }}" required>
                                                                            @error('number_of_guests')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="time">@lang('messages.Date and Time')</label>
                                                                            <input type="text" readonly name="time" class="form-control datetimepicker @error('time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ dateTimeFormat($order_wedding_flight->time) }}" required>
                                                                            @error('time')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="guests">@lang('messages.Name')</label>
                                                                            <input type="text" name="guests" class="form-control @error('guests') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ $order_wedding_flight->guests }}" required>
                                                                            @error('guests')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="guests_contact">@lang('messages.Contact')</label>
                                                                            <input type="text" name="guests_contact" class="form-control @error('guests_contact') is-invalid @enderror"  placeholder="@lang('messages.Contact')" value="{{ $order_wedding_flight->guests_contact }}" required>
                                                                            @error('guests_contact')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="updateInvitationsFlight-{{ $order_wedding_flight->id }}" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang('messages.Save')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            @endif
        </div>
    @else
        <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule')
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-flight-{{ $orderWedding->id }}"> 
                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add Schedule')" aria-hidden="true"></i>
                </a>
            </span>
        </div>
        <div class="card-ptext-margin">
            <p class="page-notification">
                @lang('messages.You can add the flight schedule for the wedding couple to make sure everything goes as planned')
            </p>
        </div>
    @endif
    {{-- MODAL ADD WEDDING FLIGHT  --}}
    <div class="modal fade" id="add-wedding-flight-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy ion-android-plane"></i> @lang("messages.Add Flight Schedule")</div>
                    </div>
                    <form id="addInvitationsFlight" action="/fadd-order-wedding-flight/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="modal-form-container">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="flight[]">@lang('messages.Flight Number')</label>
                                                <input type="text" name="flight[]" class="form-control uppercase @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                                                @error('flight[]')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="flight_group[]">@lang('messages.Group') <span>*</span></label>
                                                <select name="flight_group[]" class="custom-select col-12 @error('flight_group[]') is-invalid @enderror" required>
                                                    <option selected value="">@lang('messages.Select one')</option>
                                                    <option value="Brides">@lang("messages.Bride's")</option>
                                                    <option value="Invitations">@lang('messages.Invitations')</option>
                                                </select>
                                                @error('flight_group[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="type[]">@lang('messages.Type') <span>*</span></label>
                                                <select name="type[]" class="custom-select col-12 @error('type[]') is-invalid @enderror" required>
                                                    <option selected value="">Select</option>
                                                    <option value="Arrival">Arrival</option>
                                                    <option value="Departure">Departure</option>
                                                </select>
                                                @error('type[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                                                <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                                                @error('number_of_guests')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="time[]">@lang('messages.Date and Time')</label>
                                                <input type="datetime-local" name="time[]" class="form-control @error('time[]') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ old('time[]') }}" required>
                                                @error('time[]')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="guests[]">@lang('messages.Name')</label>
                                                <input type="text" name="guests[]" class="form-control @error('guests[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guests[]') }}" required>
                                                @error('guests[]')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="guests_contact[]">@lang('messages.Contact')</label>
                                                <input type="text" name="guests_contact[]" class="form-control @error('guests_contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('guests_contact[]') }}" required>
                                                @error('guests_contact[]')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="after-add-more"></div>
                            <div class="col-12 col-sm-12 col-md-12 text-right">
                                <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add More')</button>
                            </div>
                        </div>
                        <input type="hidden" name="orderWedding_id" value="{{ $orderWedding->id }}">
                    </form>
                    <div class="card-box-footer">
                        <button type="submit" form="addInvitationsFlight" class="btn btn-primary"><i class="icon-copy fa fa-floppy-o" aria-hidden="true"></i> @lang('messages.Save')</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copy hide">
        <div class="col-md-12">
            <div class="control-group">
                <div class="modal-form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="flight[]">@lang('messages.Flight Number')</label>
                                        <input type="text" name="flight[]" class="form-control uppercase @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                                        @error('flight[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="flight_group[]">@lang('messages.Group') <span>*</span></label>
                                        <select name="flight_group[]" class="custom-select col-12 @error('flight_group[]') is-invalid @enderror" required>
                                            <option selected value="">@lang('messages.Select one')</option>
                                            <option value="Brides">@lang("messages.Bride's")</option>
                                            <option value="Invitations">@lang('messages.Invitations')</option>
                                        </select>
                                        @error('flight_group[]')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="type[]">@lang('messages.Type') <span>*</span></label>
                                        <select name="type[]" class="custom-select col-12 @error('type[]') is-invalid @enderror" required>
                                            <option selected value="">Select</option>
                                            <option value="Arrival">Arrival</option>
                                            <option value="Departure">Departure</option>
                                        </select>
                                        @error('type[]')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                                        <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                                        @error('number_of_guests')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="time[]">@lang('messages.Date and Time')</label>
                                        <input type="datetime-local" name="time[]" class="form-control @error('time[]') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ old('time[]') }}" required>
                                        @error('time[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="guests[]">@lang('messages.Name')</label>
                                        <input type="text" name="guests[]" class="form-control @error('guests[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guests[]') }}" required>
                                        @error('guests[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="guests_contact[]">@lang('messages.Contact')</label>
                                        <input type="text" name="guests_contact[]" class="form-control @error('guests_contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('guests_contact[]') }}" required>
                                        @error('guests_contact[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="btn-remove-add-more" data-toggle="tooltip" data-placement="top" title="Remove">
                                    <button class="remove" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                </div>
                                <div class="col-sm-12">
                                    <hr class="form-hr">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
