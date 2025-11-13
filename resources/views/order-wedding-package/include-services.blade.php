 {{-- INCLUDE --}}
 @if ($orderWedding->service == "Wedding Package")
    <div id="additionalServices" class="col-md-12">
        @php
            $additional_services = $vendor_packages->where('type','Other');
            $addser_ids = json_decode($orderWedding->additional_services);
            $total_additional_service = 0;
        @endphp
        @if ($addser_ids)
            <div class="page-subtitle">
        @else
            <div class="page-subtitle empty-value">
        @endif
            @lang('messages.Additional Services') (@lang('messages.Include'))
        </div>
        @if ($addser_ids)
            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th style="width: 15%" class="datatable-nosort">@lang('messages.Date')</th>
                        <th style="width: 45%" class="datatable-nosort">@lang('messages.Services')</th>
                        <th style="width: 30%" class="datatable-nosort">@lang('messages.Venue')</th>
                        <th style="width: 10%" class="datatable-nosort"></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($addser_ids)
                        @foreach ($addser_ids as $addser_id)
                            @php
                                $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                $total_additional_service = $additionalService->publish_rate + $total_additional_service;
                            @endphp
                            <tr>
                                <td class="pd-2-8">
                                    @if ($additionalService->venue == "Ceremony Venue")
                                        {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                    @elseif ($additionalService->venue == "Reception Venue")
                                        {{ date("m/d/y",strtotime($orderWedding->reception_date_start)) }}
                                    @else
                                        {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                    @endif
                                </td>
                                <td class="pd-2-8">{{ $additionalService->service }}</td>
                                <td class="pd-2-8">
                                    {{ $additionalService->venue?$additionalService->venue:"-" }}
                                </td>
                                <td class="pd-2-8 text-right">
                                    <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                            <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-left">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                            </div>
                                            <div class="card-banner">
                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                            </div>
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-sm-12 text-center">
                                                            <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                <p>
                                                                    {{ $additionalService->duration }}
                                                                    @if ($additionalService->time == "days")
                                                                        @if ($additionalService->duration > 1)
                                                                            @lang('messages.days')
                                                                        @else
                                                                            @lang('messages.day')
                                                                        @endif
                                                                    @else
                                                                        @lang('messages.'.$additionalService->time)
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        <div class="col-sm-12">
                                                            <hr class="form-hr">
                                                            {!! $additionalService->description !!}
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
                        @endforeach
                    @endif
                </tbody>
            </table>
        @endif
    </div>
 {{-- ADDITIONAL SERVICE --}}
 @else
    @php
            $additional_service_makeups = $vendor_packages->where('type','Make-up');
            $additional_service_documentations = $vendor_packages->where('type','Documentation');
        if ($orderWedding->service == "Ceremony Venue") {
            $additional_service_entertainments = $vendor_packages->where('type','Entertainment')->where('venue','Ceremony Venue');
            $additional_service_others = $vendor_packages->where('type','Other')->where('venue','Ceremony Venue');
            $additional_service_staf_manager = $vendor_packages->where('type','Other')->where('venue','Ceremony and Reception');
        }elseif($orderWedding->service == "Reception Venue") {
            $additional_service_entertainments = $vendor_packages->where('type','Entertainment')->where('venue','Reception Venue');
            $additional_service_others = $vendor_packages->where('type','Other')->where('venue','Reception Venue');
            $additional_service_staf_manager = $vendor_packages->where('type','Other')->where('venue','Ceremony and Reception');
        }
    @endphp
    <div id="additionalServices" class="col-md-12">
        @php
        if ($orderWedding->service == "Ceremony Venue") {
            $additional_services = $vendor_packages->where('type','Other')->where('venue','!=','Reception Venue');
        }else{
            $additional_services = $vendor_packages->where('type','Other')->where('venue','!=','Ceremony Venue');
        }
            $addser_ids = json_decode($orderWedding->additional_services);
            $total_additional_service = 0;
        @endphp
        @if ($addser_ids)
            <div class="page-subtitle">
        @else
            <div class="page-subtitle empty-value">
        @endif
            @lang('messages.Additional Services')
            <span>
                <a href="#" data-toggle="modal" data-target="#add-additional-service-{{ $orderWedding->id }}"> 
                    @if ($addser_ids)
                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                    @else
                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                    @endif
                </a>
            </span>
        </div>
        @if ($addser_ids)
            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th style="width: 5%" class="datatable-nosort">@lang('messages.No')</th>
                        <th style="width: 55%" class="datatable-nosort">@lang('messages.Services')</th>
                        <th style="width: 30%" class="datatable-nosort">@lang('messages.Price')</th>
                        <th style="width: 10%" class="datatable-nosort"></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($addser_ids)
                        @foreach ($addser_ids as $no_addser=>$addser_id)
                            @php
                                $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                $total_additional_service = $additionalService->publish_rate + $total_additional_service;
                            @endphp
                            <tr>
                                <td class="pd-2-8">{{ ++$no_addser }}</td>
                                <td class="pd-2-8">{{ $additionalService->service }}</td>
                                <td class="pd-2-8">
                                    {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }}
                                </td>
                                <td class="pd-2-8 text-right">
                                    <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                            <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-left">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                            </div>
                                            <div class="card-banner">
                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                            </div>
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-sm-12 text-center">
                                                            <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                <p>
                                                                    {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }} / {{ $additionalService->duration }}
                                                                    @if ($additionalService->time == "days")
                                                                        @if ($additionalService->duration > 1)
                                                                            @lang('messages.days')
                                                                        @else
                                                                            @lang('messages.day')
                                                                        @endif
                                                                    @else
                                                                        @lang('messages.'.$additionalService->time)
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        <div class="col-sm-12">
                                                            <hr class="form-hr">
                                                            {!! $additionalService->description !!}
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
                        @endforeach
                        <tr style="border-top: 2px solid black !important">
                            <td colspan="2" class="text-center" style="border-top: 2px solid #bbbbbb;">@lang('messages.Total Additional Services')</td>
                            <td colspan="2" class="text-left" style="border-top: 2px solid #bbbbbb;">{{ "$".number_format($total_additional_service,0,',','.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
        {{-- MODAL ADD ADDITIONAL SERVICE  --}}
        <div class="modal fade" id="add-additional-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-left">
                    <div class="card-box">
                        <div class="card-box-title">
                            @if ($addser_ids)
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                            @else
                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                            @endif
                        </div>
                        <form id="addAdditionalService" action="/fadd-additional-service-to-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        @if (count($additional_service_entertainments)>0)
                                            <div class="subtitle m-t-8">@lang('messages.Entertainment')</div>
                                            <div class="card-box-content">
                                                @foreach ($additional_service_entertainments as $service_entertainment)
                                                    <div class="card-checkbox" onclick="toggleCard(this)">
                                                        <img class="card-img" src="{{ asset('storage/vendors/package/' . $service_entertainment->cover) }}" alt="{{ $service_entertainment->service }}">
                                                        @if ($addser_ids)
                                                            <input type="checkbox" id="addser_id_{{ $service_entertainment->id }}" name="addser_id[]" value="{{ $service_entertainment->id }}" class="d-none checkbox-input" @if(in_array($service_entertainment->id, $addser_ids)) checked @endif>
                                                        @else
                                                            <input type="checkbox" id="addser_id_{{ $service_entertainment->id }}" name="addser_id[]" value="{{ $service_entertainment->id }}" class="d-none checkbox-input" @if($service_entertainment->isSelected) checked @endif>
                                                        @endif
                                                        <div class="card-lable-left">
                                                            <div class="meta-box lable-text-black">
                                                                {{ '$ ' . number_format($service_entertainment->publish_rate, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="name-card">
                                                            <b>{{ $service_entertainment->service }}</b>
                                                        </div>
                                                    </div>
                                                    @error('addser_id[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (count($additional_service_makeups)>0)
                                            <div class="subtitle m-t-8">@lang('messages.Make-up')</div>
                                            <div class="card-box-content">
                                                @foreach ($additional_service_makeups as $service_makeup)
                                                    <div class="card-checkbox" onclick="toggleCard(this)">
                                                        <img class="card-img" src="{{ asset('storage/vendors/package/' . $service_makeup->cover) }}" alt="{{ $service_makeup->service }}">
                                                        @if ($addser_ids)
                                                            <input type="checkbox" id="addser_id_{{ $service_makeup->id }}" name="addser_id[]" value="{{ $service_makeup->id }}" class="d-none checkbox-input" @if(in_array($service_makeup->id, $addser_ids)) checked @endif>
                                                        @else
                                                            <input type="checkbox" id="addser_id_{{ $service_makeup->id }}" name="addser_id[]" value="{{ $service_makeup->id }}" class="d-none checkbox-input" @if($service_makeup->isSelected) checked @endif>
                                                        @endif
                                                        <div class="card-lable-left">
                                                            <div class="meta-box lable-text-black">
                                                                {{ '$ ' . number_format($service_makeup->publish_rate, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="name-card">
                                                            <b>{{ $service_makeup->service }}</b>
                                                        </div>
                                                    </div>
                                                    @error('addser_id[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (count($additional_service_documentations)>0)
                                            <div class="subtitle m-t-8">@lang('messages.Documentation')</div>
                                            <div class="card-box-content">
                                                @foreach ($additional_service_documentations as $service_documentation)
                                                    <div class="card-checkbox" onclick="toggleCard(this)">
                                                        <img class="card-img" src="{{ asset('storage/vendors/package/' . $service_documentation->cover) }}" alt="{{ $service_documentation->service }}">
                                                        @if ($addser_ids)
                                                            <input type="checkbox" id="addser_id_{{ $service_documentation->id }}" name="addser_id[]" value="{{ $service_documentation->id }}" class="d-none checkbox-input" @if(in_array($service_documentation->id, $addser_ids)) checked @endif>
                                                        @else
                                                            <input type="checkbox" id="addser_id_{{ $service_documentation->id }}" name="addser_id[]" value="{{ $service_documentation->id }}" class="d-none checkbox-input" @if($service_documentation->isSelected) checked @endif>
                                                        @endif
                                                        <div class="card-lable-left">
                                                            <div class="meta-box lable-text-black">
                                                                {{ '$ ' . number_format($service_documentation->publish_rate, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="name-card">
                                                            <b>{{ $service_documentation->service }}</b>
                                                        </div>
                                                    </div>
                                                    @error('addser_id[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (count($additional_service_others)>0)
                                            <div class="subtitle m-t-8">@lang('messages.Other')</div>
                                            <div class="card-box-content">
                                                @foreach ($additional_service_others as $service_other)
                                                    <div class="card-checkbox" onclick="toggleCard(this)">
                                                        <img class="card-img" src="{{ asset('storage/vendors/package/' . $service_other->cover) }}" alt="{{ $service_other->service }}">
                                                        @if ($addser_ids)
                                                            <input type="checkbox" id="addser_id_{{ $service_other->id }}" name="addser_id[]" value="{{ $service_other->id }}" class="d-none checkbox-input" @if(in_array($service_other->id, $addser_ids)) checked @endif>
                                                        @else
                                                            <input type="checkbox" id="addser_id_{{ $service_other->id }}" name="addser_id[]" value="{{ $service_other->id }}" class="d-none checkbox-input" @if($service_other->isSelected) checked @endif>
                                                        @endif
                                                        <div class="card-lable-left">
                                                            <div class="meta-box lable-text-black">
                                                                {{ '$ ' . number_format($service_other->publish_rate, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="name-card">
                                                            <b>{{ $service_other->service }}</b>
                                                        </div>
                                                    </div>
                                                    @error('addser_id[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endforeach
                                                @if (count($additional_service_staf_manager)>0)
                                                    @foreach ($additional_service_staf_manager as $staf_manager)
                                                        <div class="card-checkbox" onclick="toggleCard(this)">
                                                            <img class="card-img" src="{{ asset('storage/vendors/package/' . $staf_manager->cover) }}" alt="{{ $staf_manager->service }}">
                                                            @if ($addser_ids)
                                                                <input type="checkbox" id="addser_id_{{ $staf_manager->id }}" name="addser_id[]" value="{{ $staf_manager->id }}" class="d-none checkbox-input" @if(in_array($staf_manager->id, $addser_ids)) checked @endif>
                                                            @else
                                                                <input type="checkbox" id="addser_id_{{ $staf_manager->id }}" name="addser_id[]" value="{{ $staf_manager->id }}" class="d-none checkbox-input" @if($staf_manager->isSelected) checked @endif>
                                                            @endif
                                                            <div class="card-lable-left">
                                                                <div class="meta-box lable-text-black">
                                                                    {{ '$ ' . number_format($staf_manager->publish_rate, 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <div class="name-card">
                                                                <b>{{ $staf_manager->service }}</b>
                                                            </div>
                                                        </div>
                                                        @error('addser_id[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="card-box-footer">
                            @if ($addser_ids)
                                <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                            @else
                                <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Add')</button>
                            @endif
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 @endif