<div class="hotel-form-price-row" data-hotel-price-row>
    <div class="hotel-form-price-row__header">
        <strong>Normal Price</strong>
        <button type="button" class="backend-icon-action backend-icon-action--delete hotel-form-remove-action" data-hotel-price-remove aria-label="Remove price row">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="backend-form-field">
        <label>Room <b>*</b></label>
        <select class="backend-form-control" name="rooms_id[]" required>
            <option selected value="">Select room</option>
            @foreach ($rooms as $sroom)
                <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
            @endforeach
        </select>
    </div>

    <div class="backend-form-field">
        <label>Start Date <b>*</b></label>
        <input class="backend-form-control" name="start_date[]" type="date" value="{{ old('start_date.0') }}" required>
    </div>

    <div class="backend-form-field">
        <label>End Date <b>*</b></label>
        <input class="backend-form-control" name="end_date[]" type="date" value="{{ old('end_date.0') }}" required>
    </div>

    <div class="backend-form-field">
        <label>Contract Rate <b>*</b></label>
        <input class="backend-form-control" type="number" name="contract_rate[]" placeholder="Insert contract rate" value="{{ old('contract_rate.0') }}" required>
    </div>

    <div class="backend-form-field">
        <label>Markup <b>*</b></label>
        <input class="backend-form-control" type="number" name="markup[]" placeholder="Insert markup" value="{{ old('markup.0', $markups->markup ?? 0) }}" required>
    </div>

    <div class="backend-form-field">
        <label>Kick Back</label>
        <input class="backend-form-control" type="number" name="kick_back[]" placeholder="Insert kick back" value="{{ old('kick_back.0', 0) }}">
    </div>
</div>
