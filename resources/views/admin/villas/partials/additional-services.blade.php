<div id="additionalServices" class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="fa fa-asterisk" aria-hidden="true"></i> Additional Service</div>
            </div>
            @if (count($additional_services) > 0)
                <table id="tbAdditionalService" class="data-table table stripe nowrap" >
                    <thead>
                        <tr>
                            <th data-priority="1" style="width: 30%;">Type</th>
                            <th style="width: 50%;">Description</th>
                            <th style="width: 10%;">Published Rate</th>
                            @canany(['posDev','posAuthor'])
                                <th data-priority="1" class="datatable-nosort text-center" style="width: 10%;">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($additional_services as $additional_service)
                            @php
                                $usdrates_additional_services = ceil($additional_service->contract_rate / $usdrates->rate);
                                $tax_additional_services = $taxes->tax / 100;
                                $pajak_additional_services = ceil(($usdrates_additional_services + $additional_service->markup)*$tax_additional_services);
                            @endphp
                            <tr>
                                <td>
                                    <b>{{ $additional_service->type }}</b>
                                </td>
                                <td>
                                    <p>{{ $additional_service->name }}</p>
                                    {!! $additional_service->description !!}
                                </td>
                                <td>
                                    <div class="rate-usd">{{ currencyFormatUsd($usdrates_additional_services + $additional_service->markup + $pajak_additional_services) }}</div>
                                </td>
                                @canany(['posDev','posAuthor'])
                                    <td class="text-right">
                                        <div class="table-action">
                                            <a href="#" data-toggle="modal" data-target="#editAdditionalService-{{ $additional_service->id }}">
                                                <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                            </a>
                                            <form action="{{ route('func.villa-additional-service.delete',$additional_service->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                <input type="hidden" name="villas_id" value="{{ $villa->id }}">
                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                            @canany(['posDev','posAuthor'])
                                {{-- MODAL ADDITIONAL SERVICE EDIT --}}
                                <div class="modal fade" id="editAdditionalService-{{ $additional_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Additional Service</div>
                                                </div>
                                                <form id="edit-additional-service-{{ $additional_service->id }}" action="{{ route('func.villa-additional-service.update',$additional_service->id) }}" method="post" enctype="multipart/form-data">
                                                    @method('put')
                                                    {{ csrf_field() }}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="type">Type </label>
                                                                <input name="type" id="type" wire:model="type" class="form-control @error('type') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ $additional_service->type }}" required>
                                                                @error('type')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="name">Name </label>
                                                                <input name="name" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ $additional_service->name }}" required>
                                                                @error('name')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="contract_rate" class="form-label">Contract Rate <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span>Rp</span>
                                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $additional_service->contract_rate }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="markup" class="form-label">Markup <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $additional_service->markup }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="tab-inner-title">Description</div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="description" class="form-label">English</label>
                                                                        <textarea name="description" id="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $additional_service->description !!}</textarea>
                                                                        @error('description')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="description_traditional" class="form-label">Traditional</label>
                                                                        <textarea name="description_traditional" id="description_traditional" class="textarea_editor form-control @error('description_traditional') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $additional_service->description_traditional !!}</textarea>
                                                                        @error('description_traditional')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="description_simplified" class="form-label">Simplified</label>
                                                                        <textarea name="description_simplified" id="description_simplified" class="textarea_editor form-control @error('description_simplified') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $additional_service->description_simplified !!}</textarea>
                                                                        @error('description_simplified')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="edit-additional-service-{{ $additional_service->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcanany
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="notif">Additional services not found!</div>
            @endif
            @canany(['posDev','posAuthor'])
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#addAdditionalService-{{ $villa->id }}" data-toggle="tooltip" data-placement="top" title="Detail">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Service</button>
                    </a>
                </div>
                {{-- MODAL ADD ADDITIONAL SERVICE --}}
                <div class="modal fade" id="addAdditionalService-{{ $villa->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Service</div>
                                </div>
                                <form id="addAdditionalService" action="{{ route('func.villa-additional-service.add') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="type">Type </label>
                                                        <input name="type" id="type"  type="text" wire:model="type" class="form-control @error('type') is-invalid @enderror" placeholder="Insert type" value="{{ old('type') }}" required>
                                                        @error('type')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">Name </label>
                                                        <input name="name" id="name"  type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert name" value="{{ old('name') }}" required>
                                                        @error('name')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contract_rate">Contract Rate<span> *</span></label>
                                                        <div class="btn-icon">
                                                            <span>Rp</span>
                                                            <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="markup">Markup<span> *</span></label>
                                                        <div class="btn-icon">
                                                            <span>$</span>
                                                            <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="tab-inner-title">Description</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="description" class="form-label">English</label>
                                                                <textarea name="description" id="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('description') }}</textarea>
                                                                @error('description')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="description_traditional" class="form-label">Traditional</label>
                                                                <textarea name="description_traditional" id="description_traditional" class="textarea_editor form-control @error('description_traditional') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('description_traditional') }}</textarea>
                                                                @error('description_traditional')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="description_simplified" class="form-label">Simplified</label>
                                                                <textarea name="description_simplified" id="description_simplified" class="textarea_editor form-control @error('description_simplified') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('description_simplified') }}</textarea>
                                                                @error('description_simplified')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input name="villa_id" value="{{ $villa->id }}" type="hidden">
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="service_id" name="service_id" value="{{ $villa->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Price</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcanany
        </div>
    </div>
</div>