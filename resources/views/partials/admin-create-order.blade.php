<div class="col-md-6">
    <div class="mobile">
        <hr class="form-hr">
    </div>
    <div class="form-group ">
        <label for="user_id">Select Agent <span>*</span></label>
        <div class="col-sm-12">
            <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                <option selected value="">Select Agent</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->username." (".$agent->code.") @".$agent->office }}</option>
                @endforeach
            </select>
            @error('user_id')
                <div class="alert-form">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>