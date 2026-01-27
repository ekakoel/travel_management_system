 {{-- WEDDING ADDITIONAL SERVICE --}}

<div id="requestServices" class="col-md-12">
    @php
        $vendorPackages = $vendor_packages->where('type','Other');
        $additional_services = $vendor_packages->where('type','Other');
        $addser_ids = json_decode($orderWedding->additional_services);
        $total_additional_service = 0;
    @endphp
    <div class="page-subtitle">
        @lang('messages.Services') (@lang('messages.Request'))
        <span>
            <a href="#" data-toggle="modal" data-target="#add-order-wedding-additional-service-{{ $orderWedding->id }}">
                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
        <thead>
            <tr>
                <th style="width: 15%" class="datatable-nosort">@lang('messages.Date')</th>
                <th style="width: 15%" class="datatable-nosort">@lang('messages.Services')</th>
                <th style="width: 40%" class="datatable-nosort">@lang('messages.Description')</th>
                <th style="width: 20%" class="datatable-nosort">@lang('messages.Status')</th>
                <th style="width: 10%" class="datatable-nosort">@lang('messages.Price')</th>
                <th style="width: 10%" class="datatable-nosort"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($additionalServices as $additional_service)
                <tr>
                    <td class="pd-2-8">{{ dateTimeFormat($additional_service->date) }}</td>
                    <td class="pd-2-8">{{ $additional_service->quantity }} {{ $additional_service->service }}</td>
                    <td class="pd-2-8">{!! $additional_service->note !!}</td>
                    <td class="pd-2-8"> {{ $additional_service->status }}</td>
                    <td class="pd-2-8"> {{ $additional_service->price > 0? '$ ' . number_format($additional_service->price, 0, ',', '.'):"TBA"; }}</td>
                    <td class="pd-2-8 text-right">
                        <div class="table-action">
                            <a href="#" data-toggle="modal" data-target="#update-order-wedding-request-service-{{ $additional_service->id }}">
                                <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit Transport')"><i class="icon-copy fa fa-pencil"></i></button>
                            </a>
                            <form action="/fremove-order-wedding-request-service/{{ $additional_service->id }}" method="post" enctype="multipart/form-data">
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
</div>
{{-- MODAL ADD ADDITIONAL SERVICE  --}}
<div class="modal fade" id="add-order-wedding-additional-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-left">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Request Service')</div>
                </div>
                <form id="addOrderWeddingAdditionalService-{{ $orderWedding->id }}" action="/fadd-order-wedding-additional-service/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <P><i>@lang('messages.You can request a service by completing the following form')</i></P>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="date">@lang('messages.Date')<span> *</span></label>
                                <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                    <option selected value="">@lang('messages.Select date')</option>
                                    @for ($dt = 0; $dt < $orderWedding->duration+1; $dt++)
                                        <option value="{{ date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}">{{ date('d M Y',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}</option>
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
                                <input readonly type="text" name="time" class="form-control time-picker-default td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ old('time') }}" required>
                                @error('time')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="type">@lang('messages.Type')<span> *</span></label>
                                <select name="type" class="custom-select @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                    <option selected value="">@lang('messages.Select one')</option>
                                    <option value="Other">@lang('messages.Other')</option>
                                    <option value="Accessories">@lang('messages.Accessories')</option>
                                    <option value="Entertainment">@lang('messages.Entertainment')</option>
                                    <option value="Wedding Property">@lang('messages.Wedding Property')</option>
                                </select>
                                @error('type')
                                    <div class="alert-form">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service" class="form-label">@lang('messages.Service')</label>
                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="@lang('messages.Service name')" value="{{ old('service') }}" required>
                                @error('service')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity" class="form-label">@lang('messages.Quantity')</label>
                                <input type="number" min="1" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="@lang('messages.Quantity')" value="{{ old('quantity') }}" required>
                                @error('quantity')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="note" class="form-label">@lang('messages.Note')</label>
                                <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Insert note">{!! old('note') !!}</textarea>
                                @error('note')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-box-footer">
                    <button type="submit" form="addOrderWeddingAdditionalService-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                </div>
            </div>
        </div>
    </div>
</div>