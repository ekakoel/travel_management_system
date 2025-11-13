{{-- FLIGHT INVITATIONS --}}
@if ($wedding_planner->status == "Draft")
<div id="flightInvitations" class="col-md-12">
    @if (count($flight_invitations)>0)
        <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang('messages.Invitations'))</div>
        <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
            <thead>
                <tr>
                    <th>@lang('messages.Type')</th>
                    <th class="datatable-nosort">@lang('messages.Flight')</th>
                    <th>@lang('messages.Time')</th>
                    <th class="datatable-nosort">@lang('messages.Coordinator')</th>
                    <th class="datatable-nosort text-center">@lang('messages.Guests')</th>
                    <th class="datatable-nosort"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($flight_invitations as $fl_no=>$flight_invitation)
                    @php
                        $guest = $guests->where('id',$flight_invitation->guests_id)->first();
                    @endphp
                    <tr>
                        <td class="pd-2-8">
                            {{ $flight_invitation->type }}
                        </td>
                        <td class="pd-2-8">
                            {{ $flight_invitation->flight }}
                        </td>
                        <td class="pd-2-8">
                            {{ date('Y-m-d (H.i)',strtotime($flight_invitation->time)) }}
                        </td>
                        <td class="pd-2-8">
                            
                            {{ $guest->name }} ({{ $guest->phone }})
                        </td>
                        <td class="pd-2-8 text-center">
                            {{ $flight_invitation->number_of_guests }}
                        </td>
                        <td class="pd-2-8 text-right">
                            <div class="table-action">
                                <a href="#" data-toggle="modal" data-target="#update-flight-invitation-{{ $flight_invitation->id }}">
                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Flight')"><i class="icon-copy fa fa-pencil"></i></button>
                                </a>
                                <form action="/fdelete-wedding-planner-invitations-flight/{{ $flight_invitation->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                        
                    </tr>
                    {{-- MODAL UPDATE INVITATIONS FLIGHT  --}}
                    <div class="modal fade" id="update-flight-invitation-{{ $flight_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <i class="icon-copy ion-android-plane"></i> @lang('messages.Update Flight')
                                    </div>
                                    <form id="updateInvitationsFlight-{{ $flight_invitation->id }}" action="/fupdate-wedding-planner-invitations-flight/{{ $flight_invitation->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="status">@lang('messages.Status') <span>*</span></label>
                                                    <select name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                        <option selected value="Active">@lang('messages.Active')</option>
                                                        <option value="Cancel">@lang('messages.Cancel')</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="guest_name">@lang('messages.Name')</label>
                                                    <input type="text" name="guest_name" class="form-control @error('guest_name') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ $guest->name }}" required>
                                                    @error('guest_name')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="contact">@lang('messages.Contact')</label>
                                                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ $guest->phone }}" required>
                                                    @error('contact')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="type">@lang('messages.Type') <span>*</span></label>
                                                    <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                        @if ($flight_invitation->type == "Arrival")
                                                            <option selected value="{{ $flight_invitation->type }}">{{ $flight_invitation->type }}</option>
                                                            <option value="Departure">@lang('messages.Departure')</option>
                                                        @else
                                                            <option selected value="{{ $flight_invitation->type }}">{{ $flight_invitation->type }}</option>
                                                            <option value="Arrival">@lang('messages.Arrival')</option>
                                                        @endif
                                                    </select>
                                                    @error('type')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="flight">@lang('messages.Flight')</label>
                                                    <input type="text" name="flight" class="form-control @error('flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $flight_invitation->flight }}" required>
                                                    @error('flight')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="time">@lang('messages.Time')</label>
                                                    <input type="text"  readonly name="time" class="form-control datetimepicker @error('time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($flight_invitation->time)) }}" required>
                                                    @error('time')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                    <input type="number" min="1"  name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror"   value="{{ $flight_invitation->number_of_guests }}" required>
                                                    @error('time')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="updateInvitationsFlight-{{ $flight_invitation->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="#" data-toggle="modal" data-target="#add-flight-invitation-{{ $wedding_planner->id }}">
                    <button class="btn btn-primary"><i class="icon-copy  fa fa-plus-circle"></i> @lang('messages.Schedule')</button>
                </a>
            </div>
        </div>
    @else
        <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang("messages.Invitations"))</div>
        <div class="page-text">
            <div class="page-notification">
                "@lang('messages.You can add the flight schedule for the wedding couple to make sure everything goes as planned')"
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="#" data-toggle="modal" data-target="#add-flight-invitation-{{ $wedding_planner->id }}">
                    <button class="btn btn-primary"><i class="icon-copy  fa fa-plus-circle"></i> @lang('messages.Schedule')</button>
                </a>
            </div>
        </div>
    @endif
    {{-- MODAL ADD INVITATIONS FLIGHT  --}}
    <div class="modal fade" id="add-flight-invitation-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy ion-android-plane"></i> @lang("messages.Add Flight Schedule")</div>
                    </div>
                    <form id="addInvitationsFlight" action="/fadd-wedding-planner-invitations-flight/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="guest_name[]">@lang('messages.Name')</label>
                                    <input type="text" name="guest_name[]" class="form-control @error('guest_name[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guest_name[]') }}" required>
                                    @error('guest_name[]')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="contact[]">@lang('messages.Contact')</label>
                                    <input type="text" name="contact[]" class="form-control @error('contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('contact[]') }}" required>
                                    @error('contact[]')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="type[]">@lang('messages.Type') <span>*</span></label>
                                    <select name="type[]" class="custom-select @error('type[]') is-invalid @enderror" required>
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
                                    <label for="flight[]">@lang('messages.Flight')</label>
                                    <input type="text" name="flight[]" class="form-control @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                                    @error('flight[]')
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
                                    <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                                    <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                                    @error('number_of_guests')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr class="form-hr">
                            </div>
                            <div class="after-add-more"></div>
                            <div class="col-12 col-sm-12 col-md-12 text-right">
                                <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add More')</button>
                            </div>
                        </div>
                        <input type="hidden" name="wedding_planner_id" value="{{ $wedding_planner->id }}">
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
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="guest_name[]">@lang('messages.Name')</label>
                            <input type="text" name="guest_name[]" class="form-control @error('guest_name[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guest_name[]') }}" required>
                            @error('guest_name[]')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="contact[]">@lang('messages.Contact')</label>
                            <input type="text" name="contact[]" class="form-control @error('contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('contact[]') }}" required>
                            @error('contact[]')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type[]">@lang('messages.Type') <span>*</span></label>
                            <select name="type[]" class="custom-select @error('type[]') is-invalid @enderror" required>
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
                            <label for="flight[]">@lang('messages.Flight')</label>
                            <input type="text" name="flight[]" class="form-control @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                            @error('flight[]')
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
                            <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                            <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                            @error('number_of_guests')
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
@else
    @if ($wedding_planner->invitation_flight)
        <div id="flightInvitations" class="col-md-12">
            <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang('messages.Invitations'))</div>
            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th>@lang('messages.Type')</th>
                        <th class="datatable-nosort">@lang('messages.Flight')</th>
                        <th>@lang('messages.Time')</th>
                        <th class="datatable-nosort">@lang('messages.Coordinator')</th>
                        <th class="datatable-nosort text-center">@lang('messages.Guests')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($flight_invitations as $fl_no=>$flight_invitation)
                        @php
                            $guest = $guests->where('id',$flight_invitation->guests_id)->first();
                        @endphp
                        <tr>
                            <td class="pd-2-8">
                                {{ $flight_invitation->type }}
                            </td>
                            <td class="pd-2-8">
                                {{ $flight_invitation->flight }}
                            </td>
                            <td class="pd-2-8">
                                {{ date('Y-m-d (H.i)',strtotime($flight_invitation->time)) }}
                            </td>
                            <td class="pd-2-8">
                                
                                {{ $guest->name }} ({{ $guest->phone }})
                            </td>
                            <td class="pd-2-8 text-center">
                                {{ $flight_invitation->number_of_guests }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif