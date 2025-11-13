{{-- TRANSPORT --}}
@if ($orderWedding->status == "Draft")
    @php
        $trns_no = 0;
        $wp_tr_no = 0;
    @endphp
    <div id="weddingTransport" class="col-md-12">
        @if ($orderWedding->service != "Wedding Package")
            <div class="page-subtitle m-b-8">@lang('messages.Transports')
                <span>
                    <a href="#" data-toggle="modal" data-target="#add-order-wedding-transport-{{ $orderWedding->id }}">
                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                    </a>
                </span>
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
                    
                    @foreach ($transport_orders as $wp_transport)
                        <tr>
                            <td class="pd-2-8">
                                {{ ++$trns_no }}
                            </td>
                            <td class="pd-2-8">
                                {{ dateTimeFormat($wp_transport->date) }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->type }} - {{ $wp_transport->desc_type }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->transport->brand." - ".$wp_transport->transport->name }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->passenger }}
                            </td>
                            <td class="pd-2-8">
                                {{ $wp_transport->number_of_guests }} @lang('messages.guests')
                            </td>
                            <td class="pd-2-8 text-right">
                                <div class="table-action">
                                    <a href="#" data-toggle="modal" data-target="#update-order-wedding-transport-{{ $wp_transport->id }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Transport')"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form action="/fremove-order-wedding-transport/{{ $wp_transport->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="page-subtitle m-b-8">@lang('messages.Transports')
                <span>
                    <a href="#" data-toggle="modal" data-target="#add-order-wedding-transport-{{ $orderWedding->id }}">
                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                    </a>
                </span>
            </div>
            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th>@lang('messages.Date')</th>
                        <th>@lang('messages.Type')</th>
                        <th>@lang('messages.Transports')</th>
                        <th>@lang('messages.Passenger')</th>
                        <th>@lang('messages.Number of Guests')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($orderWedding->transport_id)
                        
                        <tr>
                            <td class="pd-2-8">
                                {{ date('d M Y',strtotime($orderWedding->checkin)) }}
                            </td>
                            <td class="pd-2-8">
                                @lang('messages.Airport Shuttle')<br>
                                @lang('messages.Arrival')
                            </td>
                            <td class="pd-2-8">
                                {{ $brides_transport->brand." - ".$brides_transport->name }}
                            </td>
                            <td class="pd-2-8">
                                @lang("messages.Bride's")
                            </td>
                            <td class="pd-2-8">
                                2 @lang('messages.guests')
                            </td>
                            <td class="pd-2-8 text-right">
                                <p><i>@lang('messages.Include')</i></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="pd-2-8">
                                {{ date('d M Y',strtotime($orderWedding->checkin)) }}
                            </td>
                            <td class="pd-2-8">
                                @lang('messages.Airport Shuttle')<br>
                                @lang('messages.Departure')
                            </td>
                            <td class="pd-2-8">
                                {{ $brides_transport->brand." - ".$brides_transport->name }}
                            </td>
                            <td class="pd-2-8">
                                @lang("messages.Bride's")
                            </td>
                            <td class="pd-2-8">
                                2 @lang('messages.guests')
                            </td>
                            <td class="pd-2-8 text-right">
                                <p><i>@lang('messages.Include')</i></p>
                            </td>
                        </tr>
                    @endif
                    @foreach ($transports as $ord_no=>$wp_transport)
                        @if ($transport_orders)
                            @foreach ($transport_orders as $tr_no=>$transport_inv)
                                @if ($wp_transport->id == $transport_inv->transport_id)
                                    <tr>
                                        <td class="pd-2-8">
                                            {{ dateTimeFormat($transport_inv->date)) }}
                                        </td>
                                        <td class="pd-2-8">
                                            @lang('messages.Airport Shuttle')<br>
                                            @if ($transport_inv->desc_type == "In")
                                                @lang('messages.Arrival')
                                            @else
                                                @lang('messages.Departure')
                                            @endif
                                        </td>
                                        <td class="pd-2-8">
                                            {{ $wp_transport->brand." - ".$wp_transport->name }}
                                        </td>
                                        <td class="pd-2-8">
                                            {{ $transport_inv->passenger }}
                                        </td>
                                        <td class="pd-2-8">
                                            {{ $wp_transport->capacity }} @lang('messages.Invitations')
                                        </td>
                                        <td class="pd-2-8 text-right">
                                            <div class="table-action">
                                                <a href="#" data-toggle="modal" data-target="#update-order-wedding-transport-{{ $transport_inv->id }}">
                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Transport')"><i class="icon-copy fa fa-pencil"></i></button>
                                                </a>
                                                <form action="/fremove-order-wedding-transport/{{ $transport_inv->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- MODAL UPDATE TRANSPORT  --}}
                                    <div class="modal fade" id="update-order-wedding-transport-{{ $transport_inv->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Transport')</div>
                                                    </div>
                                                    <form id="updateOrderWeddingTransport-{{ $transport_inv->id }}" action="/fuser-update-order-wedding-transport/{{ $transport_inv->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>@lang('messages.Select Transport') <span>*</span></label>
                                                                    <div class="card-box-content">
                                                                        @foreach ($transports as $tr_no_car=>$car_transport)
                                                                            <input {{ $car_transport->id == $transport_inv->transport_id?'checked':''; }} type="radio" id="{{ "edtr".$ord_no.$tr_no_car.$car_transport->id.$tr_no }}" name="edit_transport_id" value="{{ $car_transport->id }}" data-capacity-edit="{{ $car_transport->capacity }}">
                                                                            <label for="{{ "edtr".$ord_no.$tr_no_car.$car_transport->id.$tr_no }}" class="label-radio">
                                                                                <div class="card h-100">
                                                                                    <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $car_transport->cover) }}" alt="{{ $car_transport->brand." ".$car_transport->name }}">
                                                                                    <div class="name-card">
                                                                                        <b>{{ $car_transport->brand." ".$car_transport->name }}</b>
                                                                                    </div>
                                                                                    <div class="label-capacity">{{ $car_transport->capacity }} @lang('messages.seats')</div>
                                                                                </div>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group ">
                                                                    <label for="date">@lang('messages.Date')<span> *</span></label>
                                                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                                        <option selected value="{{ date('Y-m-d',strtotime($transport_inv->date)) }}">
                                                                            {{ date('Y-m-d',strtotime($transport_inv->date)) }}
                                                                            {{ date('Y-m-d',strtotime($orderWedding->checkin)) == date('Y-m-d',strtotime($transport_inv->date)) ? "Check-in" : "" }}
                                                                            {{ date('Y-m-d',strtotime($orderWedding->wedding_date)) == date('Y-m-d',strtotime($transport_inv->date)) ? "Wedding Ceremony" : "" }}
                                                                            {{ date('Y-m-d',strtotime($orderWedding->checkout)) == date('Y-m-d',strtotime($transport_inv->date)) ? "Check-out" : "" }}
                                                                        </option>
                                                                        @for ($wd = 0; $wd <= $orderWedding->duration; $wd++)
                                                                            @if (date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) != date('Y-m-d',strtotime($transport_inv->date)))
                                                                                <option value="{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }}">
                                                                                    {{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }} 
                                                                                    {{ date('Y-m-d',strtotime($orderWedding->checkin)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-in" : "" }}
                                                                                    {{ date('Y-m-d',strtotime($orderWedding->wedding_date)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Wedding Ceremony" : "" }}
                                                                                    {{ date('Y-m-d',strtotime($orderWedding->checkout)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-out" : "" }}
                                                                                </option>
                                                                            @endif
                                                                        @endfor
                                                                    </select>
                                                                    @error('date')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="time" class="form-label">@lang('messages.Time')</label>
                                                                    <input type="text" name="time" class="form-control time-picker @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ date('H:i',strtotime($transport_inv->date)) }}" required>
                                                                    @error('time')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group ">
                                                                    <label for="type">@lang('messages.Type')<span> *</span></label>
                                                                    <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                                        <option selected value="{{ $transport_inv->type." ".$transport_inv->desc_type }}">
                                                                            @lang('messages.Airport Shuttle') 
                                                                            @if ($transport_inv->desc_type == "In")
                                                                                (@lang('messages.Arrival'))
                                                                            @else
                                                                                (@lang('messages.Departure'))
                                                                            @endif
                                                                        </option>
                                                                        <option value="Airport Shuttle In">@lang('messages.Airport Shuttle') (@lang('messages.Arrival'))</option>
                                                                        <option value="Airport Shuttle Out">@lang('messages.Airport Shuttle') (@lang('messages.Departure'))</option>
                                                                    </select>
                                                                    @error('type')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                                    <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark">{!! $transport_inv->remark !!}</textarea>
                                                                    @error('remark')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="updateOrderWeddingTransport-{{ $transport_inv->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@else
    @if (count($orderWedding->transport_id)>0)
        @php
            $trns_no = 0;
        @endphp
        <div id="weddingTransport" class="col-md-12">
            <div class="page-subtitle m-b-8">@lang('messages.Transports')</div>
            test
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
                    
                    @php
                        $transport_wp = json_decode($orderWedding->transport_id);
                    @endphp
                    @foreach ($transports as $wp_tr_no=>$transport)
                        @if (in_array($transport->id, $transport_wp))
                            <tr>
                                <td class="pd-2-8">
                                    {{ ++$wp_tr_no }}
                                </td>
                                <td class="pd-2-8">
                                    {{ dateTimeFormat($transport->date)) }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $transport->type }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $transport->transport->brand." ".$transport->transport->name }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $transport->passenger }}
                                </td>
                                <td class="pd-2-8">
                                    {{ $transport->number_of_guests }} @lang('messages.guests')
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif
 {{-- MODAL ADD TRANSPORT  --}}
 <div class="modal fade" id="add-order-wedding-transport-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-left">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Transport')</div>
                </div>
                <form id="addWeddingPlannerTransport-{{ $orderWedding->id }}" action="/fadd-order-wedding-transport/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
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
                        @if ($orderWedding->service == "Wedding Package")
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="date">@lang('messages.Date')<span> *</span></label>
                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                        <option selected value="">@lang('messages.Select one')</option>
                                        @for ($wd = 0; $wd <= $orderWedding->duration; $wd++)
                                            <option value="{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }}">{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }} 
                                                {{ date('Y-m-d',strtotime($orderWedding->checkin)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-in" : "" }}
                                                {{ date('Y-m-d',strtotime($orderWedding->wedding_date)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Wedding Ceremony" : "" }}
                                                {{ date('Y-m-d',strtotime($orderWedding->checkout)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-out" : "" }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('date')
                                        <div class="alert-form">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date" class="form-label">@lang('messages.Time')</label>
                                    <input readonly type="text" name="date" class="form-control date-picker td-input @error('date') is-invalid @enderror" placeholder="@lang('messages.Select date')" value="{{ old('date') }}" required>
                                    @error('date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time" class="form-label">@lang('messages.Time')</label>
                                <input type="time" name="time" class="form-control td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ old('time') }}" required>
                                @error('time')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="type">@lang('messages.Type')<span> *</span></label>
                                <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                    <option selected value="">@lang('messages.Select one')</option>
                                    <option value="Airport Shuttle In">@lang('messages.Airport Shuttle') (@lang('messages.Arrival'))</option>
                                    <option value="Airport Shuttle Out">@lang('messages.Airport Shuttle') (@lang('messages.Departure'))</option>
                                </select>
                                @error('type')
                                    <div class="alert-form">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if ($orderWedding->service == "Wedding Package")
                            <input type="hidden" name="passenger" value="Invitations">
                        @else
                            <div class="col-md-6">
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
                        @endif
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
                    <button type="submit" form="addWeddingPlannerTransport-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                </div>
            </div>
        </div>
    </div>
</div>
