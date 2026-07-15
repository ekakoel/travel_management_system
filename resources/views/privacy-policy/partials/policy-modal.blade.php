@php
    $selectedType = old('type', optional($policy)->type);
    $selectedStatus = old('status', optional($policy)->status ?? 'Draft');
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="card-box m-b-0">
                <div class="card-box-title">
                    <div class="title" id="{{ $modalId }}Label">{{ $title }}</div>
                </div>
                <form id="{{ $formId }}" action="{{ $action }}" method="post" enctype="multipart/form-data">
                    @method($method)
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $formId }}-type">Policy Type</label>
                                <select id="{{ $formId }}-type" name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Policy type</option>
                                    @foreach ($policyTypes as $type)
                                        <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $formId }}-status">Policy Status</label>
                                <select id="{{ $formId }}-status" name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                    <option value="Draft" {{ $selectedStatus === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Active" {{ $selectedStatus === 'Active' ? 'selected' : '' }}>Active</option>
                                </select>
                                @error('status')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="{{ $formId }}-name-id">Policy Title ID</label>
                                <input id="{{ $formId }}-name-id" type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" value="{{ old('name_id', optional($policy)->name_id) }}" required>
                                @error('name_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="{{ $formId }}-policy-id">Policy ID</label>
                                <textarea id="{{ $formId }}-policy-id" name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" required>{!! old('policy_id', optional($policy)->policy_id) !!}</textarea>
                                @error('policy_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="{{ $formId }}-name-en">Policy Title EN</label>
                                <input id="{{ $formId }}-name-en" type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', optional($policy)->name_en) }}" required>
                                @error('name_en')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="{{ $formId }}-policy-en">Policy EN</label>
                                <textarea id="{{ $formId }}-policy-en" name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" required>{!! old('policy_en', optional($policy)->policy_en) !!}</textarea>
                                @error('policy_en')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="{{ $formId }}-name-zh">Policy Title ZH</label>
                                <input id="{{ $formId }}-name-zh" type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" value="{{ old('name_zh', optional($policy)->name_zh) }}" required>
                                @error('name_zh')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="{{ $formId }}-policy-zh">Policy ZH</label>
                                <textarea id="{{ $formId }}-policy-zh" name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" required>{!! old('policy_zh', optional($policy)->policy_zh) !!}</textarea>
                                @error('policy_zh')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <p class="form-text text-muted">Use type FAQ for public FAQ items. Only Active content is shown on public pages.</p>
                        </div>
                    </div>
                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                </form>
                <div class="card-box-footer">
                    <button type="submit" form="{{ $formId }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
