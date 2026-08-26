@php
    $isEdit = ($mode ?? 'create') === 'edit' && $additionalCharge;
    $formId = $modalId.'Form';
    $title = $isEdit ? 'Edit Additional Charge' : 'Add Additional Charge';
    $subtitle = $isEdit ? ($additionalCharge->name ?: 'Additional Charge') : $hotel->name;
    $selectedType = old('type', $isEdit ? $additionalCharge->type : '');
    $mandatoryValue = (int) old('mandatory', $isEdit ? $additionalCharge->mandatory : 0);
    $mandatoryStart = $isEdit && $additionalCharge->mandatory_start
        ? \Carbon\Carbon::parse($additionalCharge->mandatory_start)->format('Y-m-d')
        : '';
    $mandatoryEnd = $isEdit && $additionalCharge->mandatory_end
        ? \Carbon\Carbon::parse($additionalCharge->mandatory_end)->format('Y-m-d')
        : '';
@endphp

<div class="modal fade hotel-detail-modal hotel-additional-charge-form-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <section class="backend-modal">
                <div class="backend-modal__header">
                    <div>
                        <span>Additional Charge</span>
                        <h2 id="{{ $modalId }}Title">{{ $title }}</h2>
                        <p>{{ $subtitle }}</p>
                    </div>
                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <form id="{{ $formId }}" class="backend-form hotel-additional-charge-form" action="{{ $isEdit ? route('admin.hotels.additional-charges.update', $additionalCharge->id) : route('admin.hotels.additional-charges.store') }}" method="post">
                    @csrf
                    @if ($isEdit)
                        @method('put')
                    @endif

                    <div class="backend-modal__body">
                        <div class="backend-form-grid backend-form-grid--2">
                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Type" class="backend-form-label is-required">Type</label>
                                <select class="backend-form-control" id="{{ $modalId }}Type" name="type" required>
                                    <option value="">Select type</option>
                                    @foreach (['Per Guest', 'Per Booking', 'Per Room', 'Per Night'] as $type)
                                        <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Name" class="backend-form-label is-required">Name</label>
                                <input class="backend-form-control" id="{{ $modalId }}Name" name="name" value="{{ old('name', $isEdit ? $additionalCharge->name : '') }}" placeholder="Charge name" type="text" required>
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Mandatory" class="backend-form-label is-required">Mandatory</label>
                                <select class="backend-form-control" id="{{ $modalId }}Mandatory" name="mandatory" required>
                                    <option value="0" @selected($mandatoryValue === 0)>No</option>
                                    <option value="1" @selected($mandatoryValue === 1)>Yes</option>
                                </select>
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}MandatoryStart" class="backend-form-label">Mandatory Date Start</label>
                                <input id="{{ $modalId }}MandatoryStart" name="mandatory_start" class="backend-form-control" value="{{ old('mandatory_start', $mandatoryStart) }}" type="text" placeholder="YYYY-MM-DD" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}MandatoryEnd" class="backend-form-label">Mandatory Date End</label>
                                <input id="{{ $modalId }}MandatoryEnd" name="mandatory_end" class="backend-form-control" value="{{ old('mandatory_end', $mandatoryEnd) }}" type="text" placeholder="YYYY-MM-DD" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}ContractRate" class="backend-form-label is-required">Contract Rate</label>
                                <input class="backend-form-control" id="{{ $modalId }}ContractRate" name="contract_rate" value="{{ old('contract_rate', $isEdit ? $additionalCharge->contract_rate : '') }}" type="text" inputmode="numeric" placeholder="Insert contract rate" required data-backend-money-unit="IDR">
                            </div>

                            <div class="backend-form-field">
                                <label for="{{ $modalId }}Markup" class="backend-form-label is-required">Markup</label>
                                <input class="backend-form-control" id="{{ $modalId }}Markup" name="markup" value="{{ old('markup', $isEdit ? $additionalCharge->markup : 0) }}" type="text" inputmode="numeric" placeholder="Insert markup" required data-backend-money-unit="USD">
                            </div>

                            @foreach ([
                                'description' => 'Description',
                                'description_traditional' => 'Description - Chinese Traditional',
                                'description_simplified' => 'Description - Chinese Simplified',
                            ] as $field => $label)
                                <div class="backend-form-field is-wide">
                                    <label for="{{ $modalId }}{{ \Illuminate\Support\Str::studly($field) }}" class="backend-form-label">{{ $label }}</label>
                                    <textarea class="backend-form-control textarea_editor" id="{{ $modalId }}{{ \Illuminate\Support\Str::studly($field) }}" name="{{ $field }}" rows="5" data-backend-richtext="true">{{ old($field, $isEdit ? $additionalCharge->{$field} : '') }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        @unless ($isEdit)
                            <input name="hotel_id" value="{{ $hotel->id }}" type="hidden">
                        @endunless
                    </div>

                    <div class="backend-modal__footer">
                        <button type="submit" class="backend-button backend-button-primary">
                            <i class="fa fa-floppy-o"></i>
                            {{ $isEdit ? 'Update Charge' : 'Save Charge' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
