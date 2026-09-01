<div class="modal fade backend-modal transport-spk-detail-modal" id="editSpkDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="backend-modal__header transport-spk-detail-modal__header">
                <div>
                    <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.modal.edit_spk_eyebrow')</span>
                    <h3>@lang('transport-management.detail.actions.edit_spk')</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="backend-modal__body transport-spk-detail-modal__body">
                <form id="updateSpkDetail" class="transport-spk-detail-form" action="{{ route('admin.spk.update', $spk->id) }}" method="POST">
                    @csrf
                    <div class="transport-spk-detail-note">
                        <span>@lang('transport-management.detail.modal.spk_help_1')</span>
                        <span>@lang('transport-management.detail.modal.spk_help_2')</span>
                    </div>
                    <label>
                        <span>@lang('transport-management.form.operator') <b>*</b></span>
                        <select class="backend-form-control" name="operator_id" required>
                            <option disabled value="">@lang('transport-management.form.select_operator')</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}" @selected($operator->id == $spk->operator_id)>{{ $operator->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.order_number') <b>*</b></span>
                        <input class="backend-form-control" name="order_number" type="text" value="{{ old('order_number', $spk->order_number) }}" placeholder="{{ __('transport-management.form.order_number_placeholder') }}" required>
                    </label>
                    <label>
                        <span>@lang('transport-management.table.status') <b>*</b></span>
                        <select class="backend-form-control" name="status" required>
                            <option disabled value="">@lang('transport-management.detail.form.select_status')</option>
                            @foreach (['Canceled', 'Pending', 'In Progress', 'Completed'] as $status)
                                <option value="{{ $status }}" @selected($spk->status === $status)>{{ $status === 'Canceled' ? __('transport-management.detail.status.canceled') : $status }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.service') <b>*</b></span>
                        <select class="backend-form-control" name="spk_type" required>
                            <option disabled value="">@lang('transport-management.form.select_service')</option>
                            @foreach (['Airport Shuttle', 'Hotel Transfer', 'Tour', 'Daily Rent'] as $type)
                                <option value="{{ $type }}" @selected($spk->type === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.spk_date') <b>*</b></span> 
                        <input class="backend-form-control js-datepicker" readonly autocomplete="off" name="spk_date" type="text" value="{{ old('spk_date', $spk->spk_date ? dateFormat($spk->spk_date) : '') }}" placeholder="{{ __('transport-management.form.select_date') }}" required>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.guests') <b>*</b></span>
                        <input class="backend-form-control" name="number_of_guests" min="1" type="number" value="{{ old('number_of_guests', $spk->number_of_guests) }}" placeholder="{{ __('transport-management.form.guests_placeholder') }}" required>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.vehicle') <b>*</b></span>
                        <select class="backend-form-control" name="transport_id" required>
                            <option disabled value="">@lang('transport-management.form.select_vehicle')</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected($spk->transport_id == $vehicle->id)>
                                    {{ trim($vehicle->brand . ' ' . $vehicle->name) }}{{ $vehicle->number_plate ? ' (' . $vehicle->number_plate . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>@lang('transport-management.form.plate_number') <b>*</b></span>
                        <input class="backend-form-control" name="plate_number" type="text" value="{{ old('plate_number', $spk->plate_number) }}" placeholder="{{ __('transport-management.form.plate_number_placeholder') }}" required>
                    </label>
                    <label class="is-wide">
                        <span>@lang('transport-management.form.driver') <b>*</b></span>
                        <select class="backend-form-control" name="driver_id" required>
                            <option disabled value="">@lang('transport-management.form.select_driver')</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected($spk->driver_id == $driver->id)>{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </form>
            </div>
            <div class="backend-modal__footer transport-spk-detail-modal__footer">
                <button type="submit" form="updateSpkDetail" class="backend-button backend-button-primary">
                    <i class="icon-copy dw dw-diskette1" aria-hidden="true"></i>
                    @lang('transport-management.detail.actions.save')
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade backend-modal transport-spk-detail-modal" id="addGuest" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="backend-modal__header transport-spk-detail-modal__header">
                <div>
                    <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.guests.eyebrow')</span>
                    <h3>@lang('transport-management.detail.actions.add_guest')</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="backend-modal__body transport-spk-detail-modal__body">
                <form id="addMoreGuest" class="transport-spk-detail-form" action="{{ route('func.spk-guest.add', $spk->id) }}" method="POST">
                    @csrf
                    @include('admin.transportmanagement.spks.partials.guest-form', ['guest' => null])
                </form>
            </div>
            <div class="backend-modal__footer transport-spk-detail-modal__footer">
                <button type="submit" form="addMoreGuest" class="backend-button backend-button-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    @lang('transport-management.detail.actions.add')
                </button>
            </div>
        </div>
    </div>
</div>

@foreach ($guests as $guest)
    <div class="modal fade backend-modal transport-spk-detail-modal" id="editGuest{{ $guest->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="backend-modal__header transport-spk-detail-modal__header">
                    <div>
                        <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.guests.eyebrow')</span>
                        <h3>{{ $guest->name }}</h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="backend-modal__body transport-spk-detail-modal__body">
                    <form id="updateGuest{{ $guest->id }}" class="transport-spk-detail-form" action="{{ route('func.spk-guest.update', $guest->id) }}" method="POST">
                        @csrf
                        @include('admin.transportmanagement.spks.partials.guest-form', ['guest' => $guest])
                    </form>
                </div>
                <div class="backend-modal__footer transport-spk-detail-modal__footer">
                    <button type="submit" form="updateGuest{{ $guest->id }}" class="backend-button backend-button-primary">
                        <i class="icon-copy dw dw-diskette1" aria-hidden="true"></i>
                        @lang('transport-management.detail.actions.save')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if ($spk->type === 'Airport Shuttle')
    <div class="modal fade backend-modal transport-spk-detail-modal" id="addAirportShuttle" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="backend-modal__header transport-spk-detail-modal__header">
                    <div>
                        <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.flight.eyebrow')</span>
                        <h3>@lang('transport-management.detail.actions.add_airport_shuttle')</h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="backend-modal__body transport-spk-detail-modal__body">
                    <form id="addAirportShuttleForm" class="transport-spk-detail-form" action="{{ route('func.spk-airport-shuttle.add', $spk->id) }}" method="POST">
                        @csrf
                        @include('admin.transportmanagement.spks.partials.airport-shuttle-form', ['airport_shuttle' => null])
                    </form>
                </div>
                <div class="backend-modal__footer transport-spk-detail-modal__footer">
                    <button type="submit" form="addAirportShuttleForm" class="backend-button backend-button-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        @lang('transport-management.detail.actions.add')
                    </button>
                </div>
            </div>
        </div>
    </div>

    @foreach ($airport_shuttles as $airport_shuttle)
        <div class="modal fade backend-modal transport-spk-detail-modal" id="editAirportShuttle{{ $airport_shuttle->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="backend-modal__header transport-spk-detail-modal__header">
                        <div>
                            <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.flight.eyebrow')</span>
                            <h3>{{ $airport_shuttle->flight_number ?? __('transport-management.modal.airport_shuttle') }}</h3>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="backend-modal__body transport-spk-detail-modal__body">
                        <form id="updateAirportShuttle{{ $airport_shuttle->id }}" class="transport-spk-detail-form" action="{{ route('func.spk-airport-shuttle.update', $airport_shuttle->id) }}" method="POST">
                            @csrf
                            @include('admin.transportmanagement.spks.partials.airport-shuttle-form', ['airport_shuttle' => $airport_shuttle])
                        </form>
                    </div>
                    <div class="backend-modal__footer transport-spk-detail-modal__footer">
                        <button type="submit" form="updateAirportShuttle{{ $airport_shuttle->id }}" class="backend-button backend-button-primary">
                            <i class="icon-copy dw dw-diskette1" aria-hidden="true"></i>
                            @lang('transport-management.detail.actions.save')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<div class="modal fade backend-modal transport-spk-detail-modal" id="addDestination" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="backend-modal__header transport-spk-detail-modal__header">
                <div>
                    <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.destinations.eyebrow')</span>
                    <h3>@lang('transport-management.detail.actions.add_destination')</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="backend-modal__body transport-spk-detail-modal__body">
                <form id="addSpkDestination" class="transport-spk-detail-form" action="{{ route('admin.spk-destinations.add', $spk->id) }}" method="POST">
                    @csrf
                    @include('admin.transportmanagement.spks.partials.destination-form', ['destination' => null])
                </form>
            </div>
            <div class="backend-modal__footer transport-spk-detail-modal__footer">
                <button type="submit" form="addSpkDestination" class="backend-button backend-button-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    @lang('transport-management.detail.actions.add')
                </button>
            </div>
        </div>
    </div>
</div>

@foreach ($spk->destinations as $destination)
    <div class="modal fade backend-modal transport-spk-detail-modal" id="updateSpkDestination{{ $destination->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="backend-modal__header transport-spk-detail-modal__header">
                    <div>
                        <span class="transport-spk-detail-eyebrow">@lang('transport-management.detail.destinations.eyebrow')</span>
                        <h3>{{ $destination->destination_name ?? __('transport-management.detail.actions.edit_destination') }}</h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="backend-modal__body transport-spk-detail-modal__body">
                    <form id="updateSpkDestinationForm{{ $destination->id }}" class="transport-spk-detail-form" action="{{ route('admin.spk-destinations.update', $destination->id) }}" method="POST">
                        @csrf
                        @include('admin.transportmanagement.spks.partials.destination-form', ['destination' => $destination])
                    </form>
                </div>
                <div class="backend-modal__footer transport-spk-detail-modal__footer">
                    <button type="submit" form="updateSpkDestinationForm{{ $destination->id }}" class="backend-button backend-button-primary">
                        <i class="icon-copy dw dw-diskette1" aria-hidden="true"></i>
                        @lang('transport-management.detail.actions.save')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
