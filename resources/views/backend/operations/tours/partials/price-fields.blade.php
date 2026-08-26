@php
    $editingPrice = $price ?? null;
    $formContext = $formContext ?? 'create';
    $useOldInput = old('_tour_price_form_context') === $formContext;
    $fieldValue = static fn (string $name, $fallback = null) => $useOldInput ? old($name, $fallback) : $fallback;
    $fieldError = static fn (string $name) => $useOldInput ? $errors->first($name) : null;
    $selectedMarkupType = $fieldValue(
        'markup_type',
        $editingPrice?->resolvedMarkupType() ?? \App\Models\TourPrices::MARKUP_TYPE_PERCENTAGE
    );
    $storedMarkupValue = match ($editingPrice?->resolvedMarkupType()) {
        \App\Models\TourPrices::MARKUP_TYPE_IDR => number_format((float) $editingPrice->markup_amount, 0, '.', ''),
        \App\Models\TourPrices::MARKUP_TYPE_PERCENTAGE,
        \App\Models\TourPrices::MARKUP_TYPE_USD => number_format((float) $editingPrice->markup_amount, 2, '.', ''),
        default => null,
    };
@endphp

<input type="hidden" name="_tour_price_form_context" value="{{ $formContext }}">

<div class="backend-form-grid tour-detail-form-grid">
    <div class="backend-form-field">
        <label for="{{ $formContext }}-min-qty" class="backend-form-label">Minimum Pax <span>*</span></label>
        <input id="{{ $formContext }}-min-qty" name="min_qty" type="number" min="1" class="backend-form-control {{ $fieldError('min_qty') ? 'is-invalid' : '' }}" value="{{ $fieldValue('min_qty', $editingPrice?->min_qty) }}" required>
        @if ($fieldError('min_qty'))<small class="invalid-feedback d-block">{{ $fieldError('min_qty') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-max-qty" class="backend-form-label">Maximum Pax <span>*</span></label>
        <input id="{{ $formContext }}-max-qty" name="max_qty" type="number" min="1" class="backend-form-control {{ $fieldError('max_qty') ? 'is-invalid' : '' }}" value="{{ $fieldValue('max_qty', $editingPrice?->max_qty) }}" required>
        @if ($fieldError('max_qty'))<small class="invalid-feedback d-block">{{ $fieldError('max_qty') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-contract-rate-idr" class="backend-form-label">Contract Rate IDR <span>*</span></label>
        <input id="{{ $formContext }}-contract-rate-idr" name="contract_rate_idr" type="text" inputmode="numeric" class="backend-form-control {{ $fieldError('contract_rate_idr') ? 'is-invalid' : '' }}" value="{{ $fieldValue('contract_rate_idr', $editingPrice?->contract_rate_idr) }}" placeholder="1000000" data-backend-money-unit="IDR" required>
        @if ($fieldError('contract_rate_idr'))<small class="invalid-feedback d-block">{{ $fieldError('contract_rate_idr') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-markup-type" class="backend-form-label">Markup Type <span>*</span></label>
        <select id="{{ $formContext }}-markup-type" name="markup_type" class="backend-form-control {{ $fieldError('markup_type') ? 'is-invalid' : '' }}" data-tour-markup-type data-backend-money-unit-source-target required>
            <option value="percentage" @selected($selectedMarkupType === 'percentage')>Percentage</option>
            <option value="usd" @selected($selectedMarkupType === 'usd')>USD</option>
            <option value="idr" @selected($selectedMarkupType === 'idr')>IDR</option>
        </select>
        @if ($fieldError('markup_type'))<small class="invalid-feedback d-block">{{ $fieldError('markup_type') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-markup-amount" class="backend-form-label" data-tour-markup-label>Markup <span>*</span></label>
        <input id="{{ $formContext }}-markup-amount" name="markup_amount" type="number" min="0" step="0.01" class="backend-form-control {{ $fieldError('markup_amount') ? 'is-invalid' : '' }}" value="{{ $fieldValue('markup_amount', $storedMarkupValue) }}" placeholder="10.00" data-tour-markup-amount data-backend-money-unit-source="[data-tour-markup-type]" data-backend-money-unit-map="percentage:%|usd:USD|idr:IDR" required>
        <small class="form-text text-muted" data-tour-markup-help></small>
        @if ($fieldError('markup_amount'))<small class="invalid-feedback d-block">{{ $fieldError('markup_amount') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-valid-from" class="backend-form-label">Valid From <span>*</span></label>
        <input id="{{ $formContext }}-valid-from" name="valid_from" type="text" class="backend-form-control {{ $fieldError('valid_from') ? 'is-invalid' : '' }}" value="{{ $fieldValue('valid_from', $editingPrice?->valid_from?->format('Y-m-d')) }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" autocomplete="off" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd" required>
        @if ($fieldError('valid_from'))<small class="invalid-feedback d-block">{{ $fieldError('valid_from') }}</small>@endif
    </div>
    <div class="backend-form-field">
        <label for="{{ $formContext }}-valid-until" class="backend-form-label">Valid Until <span>*</span></label>
        <input id="{{ $formContext }}-valid-until" name="valid_until" type="text" class="backend-form-control {{ $fieldError('valid_until') ? 'is-invalid' : '' }}" value="{{ $fieldValue('valid_until', $editingPrice?->valid_until?->format('Y-m-d')) }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" autocomplete="off" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd" required>
        @if ($fieldError('valid_until'))<small class="invalid-feedback d-block">{{ $fieldError('valid_until') }}</small>@endif
    </div>
</div>

<p class="text-muted mt-3 mb-0">
    The price is activated automatically after valid input is saved. It is usable only within the selected validity dates and matching pax tier.
</p>
