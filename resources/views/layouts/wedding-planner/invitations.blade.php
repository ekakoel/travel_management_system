{{-- INVITATIONS --}}
@if ($wedding_planner->status == "Draft")
    <div id="invitations" class="col-md-12">
        <div class="page-subtitle">@lang("messages.Invitations")</div>
        <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
            <thead>
                <tr>
                    <th style="width: 10%">@lang('messages.No')</th>
                    <th style="width: 40%">@lang('messages.Name')</th>
                    <th style="width: 20%">@lang('messages.Country')</th>
                    <th style="width: 20%">@lang('messages.Passport') / @lang('messages.ID')</th>
                    <th style="width: 10%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invitations as $no_inv=>$invitation)
                    <tr>
                        <td class="pd-2-8">{{ ++$no_inv }}</td>
                        <td class="pd-2-8">
                            {{ $invitation->name }}
                            @if ($invitation->chinese_name)
                                ({{ $invitation->chinese_name }})
                            @endif
                        </td>
                        <td class="pd-2-8">{{ $invitation->country }}</td>
                        <td class="pd-2-8">{{ $invitation->passport_no }}</td>
                        <td class="pd-2-8 text-right">
                            <div class="table-action">
                                <a href="#" data-toggle="modal" data-target="#update-invitation-{{ $invitation->id }}"> 
                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Invitation')" aria-hidden="true"></i>
                                </a>
                                <form action="/fdelete-wedding-planner-invitation/{{ $invitation->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    {{-- MODAL UPDATE INVITATIONS  --}}
                    <div class="modal fade" id="update-invitation-{{ $invitation->id }}"  role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy ion-android-plane"></i> @lang("messages.Update Invitation")</div>
                                    </div>
                                    <form id="updateWeddingPlannerInvitations-{{ $invitation->id }}" action="/fupdate-wedding-planner-invitations/{{ $invitation->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="sex">@lang('messages.Mr./Ms.') <span>*</span></label>
                                                    <select name="sex" class="custom-select col-12 @error('sex') is-invalid @enderror" required>
                                                        @if ($invitation->sex == "m")
                                                            <option selected value="m">Mr.</option>
                                                            <option value="f">Ms.</option>
                                                        @else
                                                            <option selected value="f">Ms.</option>
                                                            <option value="m">Mr.</option>
                                                        @endif
                                                    </select>
                                                    @error('sex')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="name">@lang('messages.Name')</label>
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"  placeholder="@lang('messages.Name')" value="{{ $invitation->name }}" required>
                                                    @error('name')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="passport_no">@lang('messages.Passport')/@lang('messages.ID Number')</label>
                                                    <input type="text" name="passport_no" class="form-control @error('passport_no') is-invalid @enderror"  placeholder="@lang('messages.Passport')/@lang('messages.ID Number')" value="{{ $invitation->passport_no }}" required>
                                                    @error('passport_no')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="updateWeddingPlannerInvitations-{{ $invitation->id }}" class="btn btn-primary"><i class="icon-copy fi-save"></i> @lang('messages.Update')</button>
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
            <div class="col-md-6">
                {{-- PAGINATION --}}
                <div class="pagination">
                    <div class="pagination-panel">
                        {{ $invitations->links() }}
                    </div>
                    <div class="pagination-desk">
                        {{ $invitations->total() }} <span>@lang('messages.Invitations')</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="#" data-toggle="modal" data-target="#add-wedding-planner-invitations-{{ $wedding_planner->id }}">
                    <button class="btn btn-primary"><i class="icon-copy  fa fa-plus-circle" ></i> @lang('messages.Invitations')</button>
                </a>
            </div>
        </div>
        {{-- MODAL ADD INVITATIONS  --}}
        <div class="modal fade" id="add-wedding-planner-invitations-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-left">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-envelope-open" aria-hidden="true"></i> @lang("messages.Add Invitation")</div>
                        </div>
                        <form id="addWeddingInvitations" action="/fadd-wedding-planner-invitations/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="modal-title">@lang('messages.Invitations')</div>
                                    @php
                                        $no_invit = 0;
                                    @endphp
                                    <div class="row">
                                        @foreach ($chunk_invitations as $chunk_inv)
                                            <div class="col-6 col-sm-4">
                                                @foreach ($chunk_inv as $invit)
                                                    <p class="p-b-0">
                                                        {{ ++$no_invit.". ". $invit->name }}
                                                        @if ($invit->chinese_name)
                                                            ({{ $invit->chinese_name }})
                                                        @endif
                                                    </p>
                                                    <hr class="hr">
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <hr class="form-hr">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="sex[]">@lang('messages.Mr./Ms.') <span>*</span></label>
                                        <select name="sex[]" class="custom-select col-12 @error('sex[]') is-invalid @enderror" required>
                                            <option selected value="">-</option>
                                            <option value="m">Mr.</option>
                                            <option value="f">Ms.</option>
                                        </select>
                                        @error('sex[]')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name[]">@lang('messages.Name')</label>
                                        <input type="text" name="name[]" class="form-control @error('name[]') is-invalid @enderror"  placeholder="@lang('messages.Name')" value="{{ old('name[]') }}" required>
                                        @error('name[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="chinese_name[]">@lang('messages.Chinese Name')</label>
                                        <input type="text" name="chinese_name[]" class="form-control @error('chinese_name[]') is-invalid @enderror"  placeholder="@lang('messages.Optional')" value="{{ old('chinese_name[]') }}">
                                        @error('chinese_name[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="country[]" class="form-label">@lang('messages.Country') <span>*</span></label>
                                        <select name="country[]" id="country[]" class="custom-select @error('country[]') is-invalid @enderror" required>
                                                <option value=""><p>@lang('messages.Select one')</p></option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->country_name }}"><p>{{ $country->country_name }} ({{ $country->code2 }})</p></option>
                                            @endforeach
                                        </select>
                                        @error('country[]')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="passport_no[]">@lang('messages.Passport') / @lang('messages.ID')</label>
                                        <input type="text" name="passport_no[]" class="form-control @error('passport_no[]') is-invalid @enderror"  placeholder="@lang('messages.Passport')/@lang('messages.ID Number')" value="{{ old('passport_no[]') }}" required>
                                        @error('passport_no[]')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <hr class="form-hr">
                                </div>
                                <div class="after-add-more-invitaton"></div>
                                <div class="col-12 col-sm-12 col-md-12 text-right">
                                    <button type="button" class="btn btn-primary add-more-invitation"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More</button>
                                </div>
                            </div>
                            <input type="hidden" name="wedding_planner_id" value="{{ $wedding_planner->id }}">
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="addWeddingInvitations" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copy-invitation hide">
                <div class="col-md-12">
                    <div class="control-group-invitation">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="sex[]">@lang('messages.Mr./Ms.') <span>*</span></label>
                                    <select name="sex[]" class="custom-select col-12 @error('sex[]') is-invalid @enderror" required>
                                        <option selected value="">-</option>
                                        <option value="m">Mr.</option>
                                        <option value="f">Ms.</option>
                                    </select>
                                    @error('sex[]')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="name[]">@lang('messages.Name')</label>
                                    <input type="text" name="name[]" class="form-control @error('name[]') is-invalid @enderror"  placeholder="@lang('messages.Name')" value="{{ old('name[]') }}" required>
                                    @error('name[]')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="chinese_name[]">@lang('messages.Chinese Name')</label>
                                    <input type="text" name="chinese_name[]" class="form-control @error('chinese_name[]') is-invalid @enderror"  placeholder="@lang('messages.Optional')" value="{{ old('chinese_name[]') }}">
                                    @error('chinese_name[]')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="country[]" class="form-label">@lang('messages.Country') <span>*</span></label>
                                    <select name="country[]" id="country[]" class="custom-select @error('country[]') is-invalid @enderror" required>
                                            <option value=""><p>@lang('messages.Select one')</p></option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_name }}"><p>{{ $country->country_name }} ({{ $country->code2 }})</p></option>
                                        @endforeach
                                    </select>
                                    @error('country[]')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="passport_no[]">@lang('messages.Passport') / @lang('messages.ID')</label>
                                    <input type="text" name="passport_no[]" class="form-control @error('passport_no[]') is-invalid @enderror"  placeholder="@lang('messages.Passport')/@lang('messages.ID Number')" value="{{ old('passport_no[]') }}" required>
                                    @error('passport_no[]')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr class="form-hr">
                            </div>
                            <div class="btn-remove-addmore" data-toggle="tooltip" data-placement="top" title="Remove">
                                <button class="remove" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @if (count($invitations)>0)
        <div id="invitations" class="col-md-12">
            <div class="page-subtitle">@lang("messages.Invitations")</div>
            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                <thead>
                    <tr>
                        <th style="width: 10%">@lang('messages.No')</th>
                        <th style="width: 40%">@lang('messages.Name')</th>
                        <th style="width: 20%">@lang('messages.Country')</th>
                        <th style="width: 20%">@lang('messages.Passport') / @lang('messages.ID')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invitations as $no_inv=>$invitation)
                        <tr>
                            <td class="pd-2-8">{{ ++$no_inv }}</td>
                            <td class="pd-2-8">{{ $invitation->name }} ({{ $invitation->chinese_name }})</td>
                            <td class="pd-2-8">{{ $invitation->country }}</td>
                            <td class="pd-2-8">{{ $invitation->passport_no }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- PAGINATION --}}
            <div class="pagination">
                <div class="pagination-panel">
                    {{ $invitations->links() }}
                </div>
                <div class="pagination-desk">
                    {{ $invitations->total() }} <span>@lang('messages.Invitations')</span>
                </div>
            </div>
        </div>
    @endif
@endif