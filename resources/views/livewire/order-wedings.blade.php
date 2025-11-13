<div>
    @if(!empty($successMsg))
        <div class="alert alert-success">
            {{ $successMsg }}
        </div>
    @endif

    <div class="pd-ltr-20">
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="stepwizard">
                        <div class="stepwizard-row setup-panel">
                            <div class="multi-wizard-step">
                                <a href="#step-1" type="button"
                                    class="btn {{ $currentStep != 1 ? 'btn-default' : 'btn-primary' }}">1</a>
                                <p>Bride's Details</p>
                            </div>
                            <div class="multi-wizard-step">
                                <a href="#step-2" type="button"
                                    class="btn {{ $currentStep != 2 ? 'btn-default' : 'btn-primary' }}">2</a>
                                <p>Step 2</p>
                            </div>
                            <div class="multi-wizard-step">
                                <a href="#step-3" type="button"
                                    class="btn {{ $currentStep != 3 ? 'btn-default' : 'btn-primary' }}"
                                    disabled="disabled">3</a>
                                <p>Step 3</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card-box">
                    <div class="setup-content {{ $currentStep != 1 ? 'display-none' : '' }}" id="step-1">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i> @lang("messages.Bride's Details")</div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="groom">Groom:</label>
                                <input name="groom" type="text" wire:model="groom" class="form-control" id="groom">
                                @error('groom') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="bride">Bride:</label>
                                <input name="bride" type="text" wire:model="bride" class="form-control" id="bride" />
                                @error('bride') <span class="error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="card-box-footer">
                            <button class="btn btn-primary" wire:click="firstStepSubmit"
                            type="button">Next</button>
                        </div>
                    </div>
                    <div class="setup-content {{ $currentStep != 2 ? 'display-none' : '' }}" id="step-2">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i> @lang("messages.Wedding Details")</div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="wedding_date">Wedding date:</label>
                                <input name="wedding_date" type="date" wire:model="wedding_date" class="form-control date-picker" id="wedding_date">
                                @error('wedding_date') <span class="error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="card-box-footer">
                            <button class="btn btn-primary" type="button" wire:click="secondStepSubmit">Next</button>
                            <button class="btn btn-danger" type="button" wire:click="back(1)">Back</button>
                        </div>
                    </div>
                    <div class="row setup-content {{ $currentStep != 3 ? 'display-none' : '' }}" id="step-3">
                        <div class="col-md-12">
                            <h3> Step 3</h3>
                            <table class="table">
                                <tr>
                                    <td>Team Name:</td>
                                    <td><strong>{{$groom}}</strong></td>
                                </tr>
                                <tr>
                                    <td>Team Price:</td>
                                    <td><strong>{{$bride}}</strong></td>
                                </tr>
                                <tr>
                                    <td>Team Detail:</td>
                                    <td><strong>{{$wedding_date}}</strong></td>
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
            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>