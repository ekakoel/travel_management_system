{{-- FLIGHT BRIDE--}}
@if ($wedding_planner->status == "Draft")
    <div class="col-md-12">
        @if ($wedding_planner->arrival_flight)
            <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang("messages.Bride"))
                <span>
                    <a href="#" data-toggle="modal" data-target="#update-flight-bride-{{ $wedding_planner->id }}"> 
                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Flight')" aria-hidden="true"></i>
                    </a>
                </span>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <table class="table tb-list" >
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Arrival Flight')
                            </td>
                            <td class="htd-2">
                                @if ($wedding_planner->arrival_flight)
                                    {{ $wedding_planner->arrival_flight }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Arrival Time')
                            </td>
                            <td class="htd-2">
                                @if ($wedding_planner->arrival_time)
                                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->arrival_time)) }}
                                @else
                                    -
                                @endif
                            
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                    <table class="table tb-list" >
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Departure Flight')
                            </td>
                            <td class="htd-2">
                                @if ($wedding_planner->departure_flight)
                                    {{ $wedding_planner->departure_flight }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Departure Time')
                            </td>
                            <td class="htd-2">
                                @if ($wedding_planner->departure_time)
                                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->departure_time)) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang("messages.Bride"))
                <span>
                    <a href="#" data-toggle="modal" data-target="#add-flight-bride-{{ $wedding_planner->id }}"> 
                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add Schedule')" aria-hidden="true"></i>
                    </a>
                </span>
            </div>
            <div class="page-text">
                <div class="page-notification">
                    "@lang('messages.You can add the flight schedule for the wedding couple to make sure everything goes as planned')"
                </div>
            </div>
        @endif
        {{-- MODAL ADD BRIDE FLIGHT  --}}
        <div class="modal fade" id="add-flight-bride-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-left">
                    <div class="card-box">
                        <div class="card-box-title">
                            @if (isset($wedding_planner->arrival_flight)  or isset($wedding_planner->arrival_time) or isset($wedding_planner->departure_flight) or isset($wedding_planner->departure_time))
                                <i class="icon-copy ion-android-plane"></i> @lang('messages.Update Flight')
                            @else
                                <div class="subtitle"><i class="icon-copy ion-android-plane"></i> @lang("messages.Add Bride Flight Schedule")</div>
                            @endif
                        </div>
                        <form id="addBridesFlight" action="/fadd-wedding-planner-brides-flight/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                        <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->arrival_flight }}" required>
                                        @error('arrival_flight')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="arrival_time">@lang('messages.Arrival Time')</label>
                                        <input type="text" readonly name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->arrival_time)) }}" required>
                                        @error('arrival_time')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                        <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->departure_flight }}" required>
                                        @error('departure_flight')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="departure_time">@lang('messages.Departure Time')</label>
                                        <input type="text" readonly name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->departure_time)) }}" required>
                                        @error('departure_time')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            
                            </div>
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="addBridesFlight" class="btn btn-primary"><i class="icon-copy fa fa-floppy-o" aria-hidden="true"></i> @lang('messages.Save')</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL UPDATE BRIDE'S FLIGHT  --}}
        <div class="modal fade" id="update-flight-bride-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-left">
                    <div class="card-box">
                        <div class="card-box-title">
                            @if (isset($wedding_planner->arrival_flight)  or isset($wedding_planner->arrival_time) or isset($wedding_planner->departure_flight) or isset($wedding_planner->departure_time))
                                <i class="icon-copy ion-android-plane"></i> @lang('messages.Update Flight') (@lang('messages.Bride'))
                            @else
                                <div class="subtitle"><i class="icon-copy ion-android-plane"></i> @lang("messages.Add Flight Schedule") (@lang('messages.Bride'))</div>
                            @endif
                        </div>
                        <form id="updateBridesFlight" action="/fupdate-wedding-planner-brides-flight/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                        <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->arrival_flight }}" required>
                                        @error('arrival_flight')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="arrival_time">@lang('messages.Arrival Time')</label>
                                        <input type="text"  readonly name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->arrival_time)) }}" required>
                                        @error('arrival_time')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="arrival_flight_status">@lang('messages.Status') <span>*</span></label>
                                        <select id="arrival_flight_status" name="arrival_flight_status" class="custom-select col-12 @error('arrival_flight_status') is-invalid @enderror" required>
                                            <option selected value="Active">Active</option>
                                            <option value="Cancel">Cancel</option>
                                        </select>
                                        @error('type[]')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                        <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->departure_flight }}" required>
                                        @error('departure_flight')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="departure_time">@lang('messages.Departure Time')</label>
                                        <input type="text"  readonly name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->departure_time)) }}" required>
                                        @error('departure_time')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="departure_flight_status">@lang('messages.Status') <span>*</span></label>
                                        <select id="departure_flight_status" name="departure_flight_status" class="custom-select col-12 @error('departure_flight_status') is-invalid @enderror" required>
                                            <option selected value="Active">Active</option>
                                            <option value="Cancel">Cancel</option>
                                        </select>
                                        @error('type[]')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="wedding_planner_id" value="{{ $wedding_planner->id }}">
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="updateBridesFlight" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @if ($wedding_planner->arrival_flight)
        <div class="col-md-12">
            <div class="page-subtitle m-b-8">@lang('messages.Flight Schedule') (@lang("messages.Bride"))</div>
            <div class="row">
                <div class="col-sm-6">
                    <table class="table tb-list" >
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Arrival Flight')
                            </td>
                            <td class="htd-2">
                                {{ $wedding_planner->arrival_flight }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Arrival Time')
                            </td>
                            <td class="htd-2">
                                {{ date('Y-m-d (H.i)',strtotime($wedding_planner->arrival_time)) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                    <table class="table tb-list" >
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Departure Flight')
                            </td>
                            <td class="htd-2">
                                {{ $wedding_planner->departure_flight }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Departure Time')
                            </td>
                            <td class="htd-2">
                                {{ date('Y-m-d (H.i)',strtotime($wedding_planner->departure_time)) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif
