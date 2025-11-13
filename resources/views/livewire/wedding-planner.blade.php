<div class="card-box">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fi-page-edit"></i> @lang("messages.Wedding Planner")</div>
    </div>
    <div>
        <form wire:submit.prevent="submitForm">
            <div class="form-group">
                <label for="brideName">Bride's Name</label>
                <input type="text" class="form-control" id="brideName" wire:model="brideName">
            </div>
            <div class="form-group">
                <label for="groomName">Groom's Name</label>
                <input type="text" class="form-control" id="groomName" wire:model="groomName">
            </div>
            <div class="form-group">
                <label for="weddingDate">Wedding Date</label>
                <input type="date" class="form-control" wire:model="weddingDate">
            </div>
            <div class="form-group">
                <label for="weddingLocation">Wedding Location</label>
                <select class="custom-select" id="weddingLocation" wire:model="weddingLocation">
                    <option selected value="">Select Location</option>
                    @foreach ($hotels as $wedding_location)
                        @if (count($wedding_location->weddings)>0)
                            <option value="{{ $wedding_location->id }}">{{ $wedding_location->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
