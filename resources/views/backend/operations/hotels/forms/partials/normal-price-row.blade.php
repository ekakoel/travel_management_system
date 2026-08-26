<div class="hotel-form-price-row" data-hotel-price-row>
    <div class="hotel-form-price-row__header">
        <strong>Normal Price</strong>
        <button type="button" class="backend-icon-action backend-icon-action--delete hotel-form-remove-action" data-hotel-price-remove aria-label="Remove price row">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label is-required">Room</label>
        <select class="backend-form-control" name="rooms_id[]" required>
            <option selected value="">Select room</option>
            @foreach ($rooms as $sroom)
                <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
            @endforeach
        </select>
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label is-required">Start Date</label>
        <input class="backend-form-control" name="start_date[]" type="text" value="{{ old('start_date.0') }}" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label is-required">End Date</label>
        <input class="backend-form-control" name="end_date[]" type="text" value="{{ old('end_date.0') }}" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label is-required">Contract Rate</label>
        <input class="backend-form-control" type="text" inputmode="numeric" name="contract_rate[]" placeholder="Insert contract rate" value="{{ old('contract_rate.0') }}" required data-backend-money-unit="IDR">
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label is-required">Markup</label>
        <input class="backend-form-control" type="text" inputmode="numeric" name="markup[]" placeholder="Insert markup" value="{{ old('markup.0', $markups->markup ?? 0) }}" required data-backend-money-unit="USD">
    </div>

    <div class="backend-form-field">
        <label class="backend-form-label">Kick Back</label>
        <input class="backend-form-control" type="text" inputmode="numeric" name="kick_back[]" placeholder="Insert kick back" value="{{ old('kick_back.0', 0) }}" data-backend-money-unit="USD">
    </div>
</div>
