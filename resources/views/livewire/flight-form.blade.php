<div>
    <table>
        <thead>
            <tr>
                <th>Arrival Flight</th>
                <th>Arrival Time</th>
                <th>Departure Flight</th>
                <th>Departure Time</th>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <td>{{ $arrivalFlight }}</td>
                <td>{{ $arrivalTime }}</td>
                <td>{{ $departureFlight }}</td>
                <td>{{ $departureTime }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div>
    <form wire:submit.prevent="submitForm">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="arrivalFlight">@lang('messages.Arrival Flight')</label>
                <input type="text" wire:model="arrivalFlight" class="form-control @error('arrivalFlight') is-invalid @enderror" placeholder="@lang('messages.Insert flight number')" required>
                @error('arrivalFlight')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="arrivalTime">@lang('messages.Arrival Time')</label>
                <input type="text" wire:model="arrivalTime" class="form-control @error('arrivalTime') is-invalid @enderror" placeholder="@lang('messages.Insert time')" required>
                @error('arrivalTime')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="departureFlight">@lang('messages.Departure Flight')</label>
                <input type="text" wire:model="departureFlight" class="form-control @error('departureFlight') is-invalid @enderror" placeholder="@lang('messages.Insert flight number')" required>
                @error('departureFlight')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    
        <div class="col-sm-6">
            <div class="form-group">
                <label for="departureTime">@lang('messages.Departure Time')</label>
                <input type="text" wire:model="departureTime" class="form-control @error('departureTime') is-invalid @enderror" placeholder="@lang('messages.Insert time')" required>
                @error('departureTime')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
