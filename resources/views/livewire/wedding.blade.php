<div>
    @if(!empty($successMsg))
    <div class="alert alert-success">
        {{ $successMsg }}
    </div>
    @endif
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="multi-wizard-step">
                <a href="#step-1" type="button"
                    class="btn {{ $currentStep != 1 ? 'btn-off' : 'btn-primary' }}">Step 1</a>
                <p>Bride's</p>
            </div>
            <div class="multi-wizard-step">
                <a href="#step-2" type="button"
                    class="btn {{ $currentStep != 2 ? 'btn-off' : 'btn-primary' }}">Step 2</a>
                <p>Wedding</p>
            </div>
            <div class="multi-wizard-step">
                <a href="#step-3" type="button"
                    class="btn {{ $currentStep != 3 ? 'btn-off' : 'btn-primary' }}">Step 3</a>
                <p>Flight</p>
            </div>
            <div class="multi-wizard-step">
                <a href="#step-4" type="button"
                    class="btn {{ $currentStep != 4 ? 'btn-off' : 'btn-primary' }}">Step 4</a>
                <p>Service</p>
            </div>
            <div class="multi-wizard-step">
                <a href="#step-5" type="button"
                    class="btn {{ $currentStep != 5 ? 'btn-off' : 'btn-primary' }}"
                    disabled="disabled">Step 5</a>
                <p>Submit</p>
            </div>
        </div>
    </div>
    <div class="row setup-content {{ $currentStep != 1 ? 'display-none' : '' }}" id="step-1">
        <div class="col-md-12">
            <div class="tab-inner-title"> Bride's Details</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="groom">Groom Name</label>
                        <input type="text" wire:model="groom" class="form-control" id="groomName" required>
                        @error('groom') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="groom_chinese">Groom Chinese Name</label>
                        <input type="text" wire:model="groom_chinese" class="form-control" id="groomChinese">
                        @error('groom_chinese') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="groom_contact">Groom Contact</label>
                        <input type="number" wire:model="groom_contact" class="form-control" id="groomContact" required>
                        @error('groom_contact') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bride">Bride Name</label>
                        <input type="text" wire:model="bride" class="form-control" id="brideName" required>
                        @error('bride') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bride_chinese">Bride Chinese Name</label>
                        <input type="text" wire:model="bride_chinese" class="form-control" id="brideChinese">
                        @error('bride_chinese') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bride_contact">Bride Contact</label>
                        <input type="number" wire:model="bride_contact" class="form-control" id="brideContact" required>
                        @error('bride_contact') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" wire:click="firstStepSubmit" type="button">Next</button>
        </div>
    </div>
    <div class="row setup-content {{ $currentStep != 2 ? 'display-none' : '' }}" id="step-2">
        <div class="col-md-12">
            <div class="tab-inner-title"> Wedding Details</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="checkin">Check-In</label>
                        <input type="text" wire:model="checkin" class="form-control date-picker" id="checkIn" required>
                        @error('checkin') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="wedding_date">Wedding Date</label>
                        <input type="text" wire:model="wedding_date" class="form-control" id="weddingDate" required>
                        @error('wedding_date') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="number_of_guests">Number of Invitations</label>
                        <input type="number" wire:model="number_of_guests" class="form-control" id="numberOfGuests" required>
                        @error('number_of_guests') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            {{-- <div class="form-group">
                <label for="description">Team Status</label><br />
                <label class="radio-inline"><input type="radio" wire:model="status" value="1"
                        {{{ $status == '1' ? "checked" : "" }}}> Active</label>
                <label class="radio-inline"><input type="radio" wire:model="status" value="0"
                        {{{ $status == '0' ? "checked" : "" }}}> DeActive</label>
                @error('status') <span class="error">{{ $message }}</span> @enderror
            </div> --}}
            <button class="btn btn-primary" type="button" wire:click="secondStepSubmit">Next</button>
            <button class="btn btn-danger" type="button" wire:click="back(1)">Back</button>
        </div>
    </div>
    <div class="row setup-content {{ $currentStep != 3 ? 'display-none' : '' }}" id="step-3">
        <div class="col-md-12">
            <h3> Step 3</h3>
            <table class="table">
                <tr>
                    <td>Groom</td>
                    <td><strong>{{$groom." ".$groom_chinese}}</strong></td>
                </tr>
                <tr>
                    <td>Groom Contact</td>
                    <td><strong>{{$groom_contact}}</strong></td>
                </tr>
                <tr>
                    <td>Bride</td>
                    <td><strong>{{$bride." ".$bride_chinese}}</strong></td>
                </tr>
                <tr>
                    <td>Bride Contact</td>
                    <td><strong>{{$bride_contact}}</strong></td>
                </tr>
               
                <tr>
                    <td>Team status:</td>
                    <td><strong>{{$status ? 'Active' : 'DeActive'}}</strong></td>
                </tr>
                
            </table>
            <button class="btn btn-success btn-lg pull-right" wire:click="submitForm" type="button">Finish!</button>
            <button class="btn btn-danger nextBtn btn-lg pull-right" type="button" wire:click="back(2)">Back</button>
        </div>
    </div>
</div>