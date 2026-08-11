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

<div class="tour-detail-form-grid">
    <label class="backend-form-field">
        <span>Minimum Pax <em>*</em></span>
        <input name="min_qty" type="number" min="1" class="backend-form-control {{ $fieldError('min_qty') ? 'is-invalid' : '' }}" value="{{ $fieldValue('min_qty', $editingPrice?->min_qty) }}" required>
        @if ($fieldError('min_qty'))<small class="invalid-feedback">{{ $fieldError('min_qty') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span>Maximum Pax <em>*</em></span>
        <input name="max_qty" type="number" min="1" class="backend-form-control {{ $fieldError('max_qty') ? 'is-invalid' : '' }}" value="{{ $fieldValue('max_qty', $editingPrice?->max_qty) }}" required>
        @if ($fieldError('max_qty'))<small class="invalid-feedback">{{ $fieldError('max_qty') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span>Contract Rate (IDR) <em>*</em></span>
        <input name="contract_rate_idr" type="text" inputmode="numeric" class="backend-form-control {{ $fieldError('contract_rate_idr') ? 'is-invalid' : '' }}" value="{{ $fieldValue('contract_rate_idr', $editingPrice?->contract_rate_idr) }}" placeholder="1000000" required>
        @if ($fieldError('contract_rate_idr'))<small class="invalid-feedback">{{ $fieldError('contract_rate_idr') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span>Markup Type <em>*</em></span>
        <select name="markup_type" class="backend-form-control {{ $fieldError('markup_type') ? 'is-invalid' : '' }}" data-tour-markup-type data-backend-money-unit-source-target required>
            <option value="percentage" @selected($selectedMarkupType === 'percentage')>Percentage</option>
            <option value="usd" @selected($selectedMarkupType === 'usd')>USD</option>
            <option value="idr" @selected($selectedMarkupType === 'idr')>IDR</option>
        </select>
        @if ($fieldError('markup_type'))<small class="invalid-feedback">{{ $fieldError('markup_type') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span data-tour-markup-label>Markup <em>*</em></span>
        <input name="markup_amount" type="number" min="0" step="0.01" class="backend-form-control {{ $fieldError('markup_amount') ? 'is-invalid' : '' }}" value="{{ $fieldValue('markup_amount', $storedMarkupValue) }}" placeholder="10.00" data-tour-markup-amount data-backend-money-unit-source="[data-tour-markup-type]" data-backend-money-unit-map="percentage:%|usd:USD|idr:IDR" required>
        <small class="form-text text-muted" data-tour-markup-help></small>
        @if ($fieldError('markup_amount'))<small class="invalid-feedback">{{ $fieldError('markup_amount') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span>Valid From</span>
        <input name="valid_from" type="text" class="backend-form-control {{ $fieldError('valid_from') ? 'is-invalid' : '' }}" value="{{ $fieldValue('valid_from', $editingPrice?->valid_from?->format('Y-m-d')) }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" autocomplete="off" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
        @if ($fieldError('valid_from'))<small class="invalid-feedback">{{ $fieldError('valid_from') }}</small>@endif
    </label>
    <label class="backend-form-field">
        <span>Valid Until</span>
        <input name="valid_until" type="text" class="backend-form-control {{ $fieldError('valid_until') ? 'is-invalid' : '' }}" value="{{ $fieldValue('valid_until', $editingPrice?->valid_until?->format('Y-m-d')) }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" autocomplete="off" data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
        @if ($fieldError('valid_until'))<small class="invalid-feedback">{{ $fieldError('valid_until') }}</small>@endif
    </label>
</div>

<p class="text-muted mt-3 mb-0">
    The price is activated automatically after valid input is saved. It is usable only within the selected validity dates and matching pax tier.
</p>
