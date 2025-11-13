<div class="row">
    <div class="col-6 col-sm-6">
        <div class="form-group">
            <label for="hotel_id"class="form-label">Hotel<span>*</span></label>
            <select wire:model="selectedHotel" name="hotel_id" class="custom-select @error('hotel_id') is-invalid @enderror" required>
                <option value="">Select Hotel</option>
                @foreach($hotels as $hotel)
                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-6 col-sm-6">
        <div class="form-group">
            <label for="wedding_venue_id" class="form-label">Wedding Venue<span>*</span></label>
            <select wire:model="selectedVenue" name="wedding_venue_id" class="custom-select @error('wedding_venue_id') is-invalid @enderror" required>
                <option selected value="">Select Wedding Venue</option>
                @if ($weddingVenues)
                    @foreach($weddingVenues as $venue)
                        <option value="{{ $venue->id }}">{{ $venue->name." (".$venue->capacity." guests)" }}</option>
                    @endforeach
                @endif
            </select>
            @error('wedding_venue_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
