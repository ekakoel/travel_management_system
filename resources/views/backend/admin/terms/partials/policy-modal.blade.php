@php
    $selectedType = old('type', optional($policy)->type);
    $selectedStatus = old('status', optional($policy)->status ?? 'Draft');
@endphp

<div class="modal fade terms-admin-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="terms-admin-modal__header">
                <div>
                    <span>Policy Editor</span>
                    <h3 id="{{ $modalId }}Label">{{ $title }}</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="{{ $formId }}" action="{{ $action }}" method="post">
                @csrf
                @method($method)
                <div class="terms-admin-modal__body">
                    <div class="backend-form-grid terms-admin-form-grid terms-admin-form-grid--meta">
                        <label>
                            <span>Policy Type <b>*</b></span>
                            <select name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select policy type</option>
                                @foreach ($policyTypes as $type)
                                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </label>

                        <label>
                            <span>Status <b>*</b></span>
                            <select name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                <option value="Draft" @selected($selectedStatus === 'Draft')>Draft</option>
                                <option value="Active" @selected($selectedStatus === 'Active')>Active</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </label>
                    </div>

                    <div class="terms-admin-language-grid">
                        <section class="terms-admin-language-panel">
                            <header>
                                <span>ID</span>
                                <strong>Indonesian Content</strong>
                            </header>
                            <label>
                                <span>Title <b>*</b></span>
                                <input type="text" name="name_id" value="{{ old('name_id', optional($policy)->name_id) }}" class="backend-form-control @error('name_id') is-invalid @enderror" required>
                                @error('name_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Policy <b>*</b></span>
                                <textarea name="policy_id" class="textarea_editor backend-form-control @error('policy_id') is-invalid @enderror" data-backend-richtext="true" required>{!! old('policy_id', optional($policy)->policy_id) !!}</textarea>
                                @error('policy_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                        </section>

                        <section class="terms-admin-language-panel">
                            <header>
                                <span>EN</span>
                                <strong>English Content</strong>
                            </header>
                            <label>
                                <span>Title <b>*</b></span>
                                <input type="text" name="name_en" value="{{ old('name_en', optional($policy)->name_en) }}" class="backend-form-control @error('name_en') is-invalid @enderror" required>
                                @error('name_en')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Policy <b>*</b></span>
                                <textarea name="policy_en" class="textarea_editor backend-form-control @error('policy_en') is-invalid @enderror" data-backend-richtext="true" required>{!! old('policy_en', optional($policy)->policy_en) !!}</textarea>
                                @error('policy_en')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                        </section>

                        <section class="terms-admin-language-panel">
                            <header>
                                <span>ZH</span>
                                <strong>Chinese Content</strong>
                            </header>
                            <label>
                                <span>Title <b>*</b></span>
                                <input type="text" name="name_zh" value="{{ old('name_zh', optional($policy)->name_zh) }}" class="backend-form-control @error('name_zh') is-invalid @enderror" required>
                                @error('name_zh')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Policy <b>*</b></span>
                                <textarea name="policy_zh" class="textarea_editor backend-form-control @error('policy_zh') is-invalid @enderror" data-backend-richtext="true" required>{!! old('policy_zh', optional($policy)->policy_zh) !!}</textarea>
                                @error('policy_zh')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                        </section>
                    </div>
                </div>

                <div class="backend-form-actions terms-admin-modal__footer">
                    <button type="submit" class="backend-button backend-button-primary">
                        <i class="fa fa-floppy-o"></i>
                        Save Policy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
