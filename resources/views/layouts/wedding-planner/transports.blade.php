{{-- ACCOMMODATION --}}
@if ($wedding_planner->status == "Draft")
    @php
        $trns_no = 0;
    @endphp
    <div id="weddingTransport" class="col-md-12">
        <div class="page-subtitle m-b-8">@lang('messages.Transports')
        </div>
        <table class="data-table table stripe nowrap no-footer dtr-inline" >
            <thead>
                <tr>
                    <th>@lang('messages.No')</th>
                    <th>@lang('messages.Date')</th>
                    <th>@lang('messages.Type')</th>
                    <th>@lang('messages.Transports')</th>
                    <th>@lang('messages.Passenger')</th>
                    <th>@lang('messages.Number of Guests')</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wedding_planner->transports as $wp_tr_no=>$wp_transport)
                    <tr>
                        <td class="pd-2-8">
                            {{ ++$trns_no }}
                        </td>
                        <td class="pd-2-8">
                            {{ date('m/d (H.i)',strtotime($wp_transport->date)) }}
                        </td>
                        <td class="pd-2-8">
                            {{ $wp_transport->type }}
                        </td>
                        <td class="pd-2-8">
                            {{ $wp_transport->transport->brand." ".$wp_transport->transport->name }}
                        </td>
                        <td class="pd-2-8">
                            {{ $wp_transport->passenger }}
                        </td>
                        <td class="pd-2-8">
                            {{ $wp_transport->number_of_guests }} @lang('messages.guests')
                        </td>
                        <td class="pd-2-8 text-right">
                            <div class="table-action">
                                <a href="#" data-toggle="modal" data-target="#update-wedding-planner-transport-{{ $wp_transport->id }}">
                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Transport')"><i class="icon-copy fa fa-pencil"></i></button>
                                </a>
                                <form action="/fremove-wedding-planner-transport/{{ $wp_transport->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    {{-- MODAL UPDATE TRANSPORT  --}}
                    <div class="modal fade" id="update-wedding-planner-transport-{{ $wp_transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Transport')</div>
                                    </div>
                                    <form id="updateWeddingPlannerTransport-{{ $wp_transport->id }}" action="/fupdate-wedding-planner-transport/{{ $wp_transport->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>@lang('messages.Select Transport') <span>*</span></label>
                                                    <div class="card-box-content">
                                                        @foreach ($transports as $car_transport)
                                                            @if ($car_transport->id == $wp_transport->transport_id)
                                                                <input checked type="radio" id="{{ "edtr".$wp_tr_no.$car_transport->id }}" name="edit_transport_id" value="{{ $car_transport->id }}" data-capacity-edit="{{ $car_transport->capacity }}">
                                                                <label for="{{ "edtr".$wp_tr_no.$car_transport->id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $car_transport->cover) }}" alt="{{ $car_transport->brand." ".$car_transport->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $car_transport->brand." ".$car_transport->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $car_transport->capacity }} @lang('messages.seats')</div>
                                                                    </div>
                                                                </label>
                                                            @else
                                                                <input type="radio" id="{{ "edtr".$wp_tr_no.$car_transport->id }}" name="edit_transport_id" value="{{ $car_transport->id }}" data-capacity-edit="{{ $car_transport->capacity }}">
                                                                <label for="{{ "edtr".$wp_tr_no.$car_transport->id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $car_transport->cover) }}" alt="{{ $car_transport->brand." ".$car_transport->name }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $car_transport->brand." ".$car_transport->name }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $car_transport->capacity }} @lang('messages.seats')</div>
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group ">
                                                    <label for="date">@lang('messages.Date')<span> *</span></label>
                                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                        <option selected value="{{ date('Y-m-d',strtotime($wp_transport->date)) }}">
                                                            {{ date('Y-m-d',strtotime($wp_transport->date)) }}
                                                            {{ date('Y-m-d',strtotime($wedding_planner->checkin)) == date('Y-m-d',strtotime($wp_transport->date)) ? "Check-in" : "" }}
                                                            {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) == date('Y-m-d',strtotime($wp_transport->date)) ? "Wedding Ceremony" : "" }}
                                                            {{ date('Y-m-d',strtotime($wedding_planner->checkout)) == date('Y-m-d',strtotime($wp_transport->date)) ? "Check-out" : "" }}
                                                        </option>
                                                        @for ($wd = 0; $wd <= $wedding_planner->duration; $wd++)
                                                            @if (date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) != date('Y-m-d',strtotime($wp_transport->date)))
                                                                <option value="{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) }}">
                                                                    {{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) }} 
                                                                    {{ date('Y-m-d',strtotime($wedding_planner->checkin)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Check-in" : "" }}
                                                                    {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Wedding Ceremony" : "" }}
                                                                    {{ date('Y-m-d',strtotime($wedding_planner->checkout)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Check-out" : "" }}
                                                                </option>
                                                            @endif
                                                        @endfor
                                                    </select>
                                                    @error('date')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="time" class="form-label">@lang('messages.Time')</label>
                                                    <input readonly name="time" class="form-control time-picker @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ date('H.i a',strtotime($wp_transport->date)) }}" required>
                                                    @error('time')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group ">
                                                    <label for="type">@lang('messages.Type')<span> *</span></label>
                                                    <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                        <option selected value="{{ $wp_transport->type }}">{{ $wp_transport->type }}</option>
                                                        <option value="Airport Shuttle">@lang('messages.Airport Shuttle')</option>
                                                        <option value="Daily">@lang('messages.Daily')</option>
                                                        <option value="Transfer">@lang('messages.Transfer')</option>
                                                    </select>
                                                    @error('type')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group ">
                                                    <label for="passenger">@lang('messages.Passenger')<span> *</span></label>
                                                    <select name="passenger" class="custom-select @error('passenger') is-invalid @enderror" value="{{ old('passenger') }}" required>
                                                        <option selected value="{{ $wp_transport->passenger }}">{{ $wp_transport->passenger }}</option>
                                                        <option value="Bride">@lang('messages.Bride')</option>
                                                        <option value="Invitations">@lang('messages.Invitations')</option>
                                                    </select>
                                                    @error('passenger')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                    <input id="nogTransportEdit" type="number" name="number_of_guests" min="1"  class="form-control @error('number_of_guests') is-invalid @enderror"  placeholder="@lang('messages.Number of guests')" value="{{ $wp_transport->number_of_guests }}" required>
                                                    @error('number_of_guests')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                    <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark">{!! $wp_transport->remark !!}</textarea>
                                                    @error('remark')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="updateWeddingPlannerTransport-{{ $wp_transport->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
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
                <a href="#" data-toggle="modal" data-target="#add-wedding-planner-transport-{{ $wedding_planner->id }}">
                    <button class="btn btn-primary"><i class="icon-copy  fa fa-plus-circle"></i> @lang('messages.Transport')</button>
                </a>
                {{-- MODAL ADD TRANSPORT  --}}
                <div class="modal fade" id="add-wedding-planner-transport-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Transport')</div>
                                </div>
                                <form id="addWeddingPlannerTransport-{{ $wedding_planner->id }}" action="/fadd-wedding-planner-transport/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('messages.Select Transport') <span>*</span></label>
                                                <div class="card-box-content">
                                                    @foreach ($transports as $transport)
                                                        <input type="radio" id="{{ "trns_rv".$transport->id }}" name="transport_id" value="{{ $transport->id }}" data-capacity="{{ $transport->capacity }}">
                                                        <label for="{{ "trns_rv".$transport->id }}" class="label-radio">
                                                            <div class="card h-100">
                                                                <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->brand." ".$transport->name }}">
                                                                <div class="name-card">
                                                                    <b>{{ $transport->brand." ".$transport->name }}</b>
                                                                </div>
                                                                <div class="label-capacity">{{ $transport->capacity }} @lang('messages.seats')</div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="date">@lang('messages.Date')<span> *</span></label>
                                                <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                    <option selected value="">@lang('messages.Select one')</option>
                                                    @for ($wd = 0; $wd <= $wedding_planner->duration; $wd++)
                                                        <option value="{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) }}">{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) }} 
                                                            {{ date('Y-m-d',strtotime($wedding_planner->checkin)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Check-in" : "" }}
                                                            {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Wedding Ceremony" : "" }}
                                                            {{ date('Y-m-d',strtotime($wedding_planner->checkout)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($wedding_planner->checkin))) ? "Check-out" : "" }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('date')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="time" class="form-label">@lang('messages.Time')</label>
                                                <input readonly name="time" class="form-control time-picker td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ old('time') }}" required>
                                                @error('time')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="type">@lang('messages.Type')<span> *</span></label>
                                                <select name="type" class="custom-select @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                                    <option selected value="">@lang('messages.Select one')</option>
                                                    <option value="Airport Shuttle">@lang('messages.Airport Shuttle')</option>
                                                    <option value="Daily">@lang('messages.Daily')</option>
                                                    <option value="Transfer">@lang('messages.Transfer')</option>
                                                </select>
                                                @error('type')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="passenger">@lang('messages.Passenger')<span> *</span></label>
                                                <select name="passenger" class="custom-select @error('passenger') is-invalid @enderror" value="{{ old('passenger') }}" required>
                                                    <option selected value="">@lang('messages.Select one')</option>
                                                    <option value="Bride">@lang('messages.Bride')</option>
                                                    <option value="Invitations">@lang('messages.Invitations')</option>
                                                </select>
                                                @error('passenger')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                <input id="nogTransport" type="number" name="number_of_guests" min="1" max="{{ $transport->capacity }}" class="form-control @error('number_of_guests') is-invalid @enderror"  placeholder="@lang('messages.Number of guests')" value="{{ old('number_of_guests') }}" required>
                                                @error('number_of_guests')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark">{!! old('remark') !!}</textarea>
                                                @error('remark')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addWeddingPlannerTransport-{{ $wedding_planner->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
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
    @if (count($wedding_planner->transports)>0)
        @php
            $trns_no = 0;
        @endphp
        <div id="weddingTransport" class="col-md-12">
            <div class="page-subtitle m-b-8">@lang('messages.Transports')</div>
            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th>@lang('messages.No')</th>
                        <th>@lang('messages.Date')</th>
                        <th>@lang('messages.Type')</th>
                        <th>@lang('messages.Transports')</th>
                        <th>@lang('messages.Passenger')</th>
                        <th>@lang('messages.Number of Guests')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wedding_planner->transports as $wp_tr_no=>$wp_transport)
                        <tr>
                            <td class="pd-2-8">
                                {{ ++$trns_no }}
                            </td>
                            <td class="pd-2-8">
                                {{ date('m/d (H.i)',strtotime($wp_transport->date)) }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->type }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->transport->brand." ".$wp_transport->transport->name }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->passenger }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->number_of_guests }} @lang('messages.guests')
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif
