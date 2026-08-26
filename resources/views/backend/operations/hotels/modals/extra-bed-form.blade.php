@php
    $isEdit = ($mode ?? 'create') === 'edit' && $extraBed;
    $formId = $modalId.'Form';
    $title = $isEdit ? 'Edit Extra Bed' : 'Add Extra Bed';
    $subtitle = $isEdit ? ($extraBed->name ?: 'Extra Bed') : $hotel->name;
    $selectedType = old('type', $isEdit ? $extraBed->type : 'Adult');
@endphp

<div class="modal fade hotel-detail-modal hotel-extra-bed-form-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <section class="backend-modal">
                <div class="backend-modal__header">
                    <div>
                        <span>Extra Bed</span>
                        <h2 id="{{ $modalId }}Title">{{ $title }}</h2>
                        <p>{{ $subtitle }}</p>
                    </div>
                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <form id="{{ $formId }}" class="backend-form hotel-extra-bed-form" action="{{ $isEdit ? route('func.extrabed.edit', $extraBed->id) : route('func.extrabed.add') }}" method="post">
                    @csrf
                    @if ($isEdit)
                        @method('put')
                    @endif

                    <div class="backend-modal__body">
                        <div class="backend-form-grid backend-form-grid--2">
                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Type" class="backend-form-label is-required">Type</label>
                                <select id="{{ $modalId }}Type" name="type" class="backend-form-control" required>
                                    <option value="Adult" @selected($selectedType === 'Adult')>Adult</option>
                                    <option value="Children" @selected($selectedType === 'Children')>Child</option>
                                    <option value="Guest" @selected($selectedType === 'Guest')>Guest</option>
                                </select>
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}MinAge" class="backend-form-label">Minimum Age</label>
                                <input id="{{ $modalId }}MinAge" name="min_age" class="backend-form-control" type="number" min="0" max="120" value="{{ old('min_age', $isEdit ? $extraBed->min_age : '') }}" placeholder="Insert minimum age">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}MaxAge" class="backend-form-label">Maximum Age</label>
                                <input id="{{ $modalId }}MaxAge" name="max_age" class="backend-form-control" type="number" min="0" max="120" value="{{ old('max_age', $isEdit ? $extraBed->max_age : '') }}" placeholder="Insert maximum age">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}ContractRate" class="backend-form-label is-required">Contract Rate</label>
                                <input id="{{ $modalId }}ContractRate" name="contract_rate" class="backend-form-control" type="text" inputmode="numeric" value="{{ old('contract_rate', $isEdit ? $extraBed->contract_rate : '') }}" placeholder="Insert contract rate" required data-backend-money-unit="IDR">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Markup" class="backend-form-label is-required">Markup</label>
                                <input id="{{ $modalId }}Markup" name="markup" class="backend-form-control" type="text" inputmode="numeric" value="{{ old('markup', $isEdit ? $extraBed->markup : '') }}" placeholder="Insert markup" required data-backend-money-unit="USD">
                            </div>

                            <div class="backend-form-field is-wide">
                                <label for="{{ $modalId }}Description" class="backend-form-label">Description</label>
                                <textarea id="{{ $modalId }}Description" name="description" class="backend-form-control textarea_editor" rows="5" placeholder="Insert extra bed description" data-backend-richtext="true">{{ old('description', $isEdit ? $extraBed->description : '') }}</textarea>
                            </div>
                        </div>

                        @unless ($isEdit)
                            <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                        @endunless
                    </div>

                    <div class="backend-modal__footer">
                        <button type="submit" class="backend-button backend-button-primary">
                            <i class="fa fa-floppy-o"></i>
                            {{ $isEdit ? 'Update Extra Bed' : 'Save Extra Bed' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
