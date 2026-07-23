<label class="backend-form-field">
    <span>Type <b>*</b></span>
    <select name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
        <option value="">Select type</option>
        @foreach (['Daily Rent', 'Airport Shuttle', 'Transfers'] as $transportPriceType)
            <option value="{{ $transportPriceType }}" @selected(old('type', $price?->type) === $transportPriceType)>{{ $transportPriceType }}</option>
        @endforeach
    </select>
    @error('type')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Duration <b>*</b></span>
    <input type="number" name="duration" class="backend-form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ old('duration', $price?->duration) }}" required>
    @error('duration')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Origin</span>
    <input type="text" name="src" class="backend-form-control @error('src') is-invalid @enderror" placeholder="Optional" value="{{ old('src', $price?->src) }}">
    @error('src')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Destination</span>
    <input type="text" name="dst" class="backend-form-control @error('dst') is-invalid @enderror" placeholder="Optional" value="{{ old('dst', $price?->dst) }}">
    @error('dst')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Contract Rate <b>*</b></span>
    <input type="number" name="contract_rate" class="backend-form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate', $price?->contract_rate) }}" required>
    @error('contract_rate')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Markup <b>*</b></span>
    <input type="number" name="markup" class="backend-form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup', $price?->markup) }}" required>
    @error('markup')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Extra Time <b>*</b></span>
    <input type="number" name="extra_time" class="backend-form-control @error('extra_time') is-invalid @enderror" placeholder="Insert extra time" value="{{ old('extra_time', $price?->extra_time) }}" required>
    @error('extra_time')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field is-wide">
    <span>Additional Information</span>
    <textarea name="additional_info" class="backend-form-control @error('additional_info') is-invalid @enderror" data-backend-richtext="true" placeholder="Optional">{{ old('additional_info', $price?->additional_info) }}</textarea>
    @error('additional_info')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>
