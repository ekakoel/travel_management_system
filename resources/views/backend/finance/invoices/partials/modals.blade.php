@if ($canManageAdjustments)
    <div class="modal fade backend-modal" id="invoice-adjustment-create-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content backend-modal">
            <header class="backend-modal__header"><div><span>{{ __('invoices.items_eyebrow') }}</span><h2>{{ __('invoices.add_adjustment_title') }}</h2></div><x-backend.modal-close label="{{ __('invoices.close') }}" /></header>
            <form action="{{ route('admin.invoices.adjustments.store', $invoice) }}" method="post" class="backend-form">
                @csrf @method('put')
                <div class="backend-modal__body">
                    <p class="invoice-detail-form-help">{{ __('invoices.adjustment_form_help') }}</p>
                    <div class="backend-form-grid backend-form-grid--2">
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-create-date">{{ __('invoices.date') }}</label><input id="adjustment-create-date" name="date" type="text" class="backend-form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd"></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-create-description">{{ __('invoices.description_label') }}</label><input id="adjustment-create-description" name="description" type="text" maxlength="255" class="backend-form-control" value="{{ old('description') }}" required></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-create-rate">{{ __('invoices.rate') }}</label><input id="adjustment-create-rate" name="rate" type="text" inputmode="decimal" class="backend-form-control" value="{{ old('rate') }}" required data-backend-money-unit="USD"></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-create-unit">{{ __('invoices.unit') }}</label><input id="adjustment-create-unit" name="unit" type="number" min="0.01" step="0.01" class="backend-form-control" value="{{ old('unit', 1) }}" required></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-create-times">{{ __('invoices.times') }}</label><input id="adjustment-create-times" name="times" type="number" min="0.01" step="0.01" class="backend-form-control" value="{{ old('times', 1) }}" required></div>
                    </div>
                </div>
                <footer class="backend-modal__footer"><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-save" aria-hidden="true"></i>{{ __('invoices.save_adjustment') }}</button></footer>
            </form>
        </div></div>
    </div>

    @foreach ($invoiceRows->where('kind', 'adjustment') as $row)
        <div class="modal fade backend-modal" id="invoice-adjustment-edit-{{ $row['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content backend-modal">
                <header class="backend-modal__header"><div><span>{{ __('invoices.adjustment') }}</span><h2>{{ __('invoices.edit_adjustment_title') }}</h2></div><x-backend.modal-close label="{{ __('invoices.close') }}" /></header>
                <form action="{{ route('admin.invoices.adjustments.update', $row['model']) }}" method="post" class="backend-form">
                    @csrf @method('put')
                    <div class="backend-modal__body"><div class="backend-form-grid backend-form-grid--2">
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-date-{{ $row['id'] }}">{{ __('invoices.date') }}</label><input id="adjustment-date-{{ $row['id'] }}" name="date" type="text" class="backend-form-control" value="{{ $row['model']->date }}" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd"></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-description-{{ $row['id'] }}">{{ __('invoices.description_label') }}</label><input id="adjustment-description-{{ $row['id'] }}" name="description" type="text" maxlength="255" class="backend-form-control" value="{{ $row['model']->description }}" required></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-rate-{{ $row['id'] }}">{{ __('invoices.rate') }}</label><input id="adjustment-rate-{{ $row['id'] }}" name="rate" type="text" inputmode="decimal" class="backend-form-control" value="{{ $row['model']->rate }}" required data-backend-money-unit="USD"></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-unit-{{ $row['id'] }}">{{ __('invoices.unit') }}</label><input id="adjustment-unit-{{ $row['id'] }}" name="unit" type="number" min="0.01" step="0.01" class="backend-form-control" value="{{ $row['model']->unit }}" required></div>
                        <div class="backend-form-field"><label class="backend-form-label" for="adjustment-times-{{ $row['id'] }}">{{ __('invoices.times') }}</label><input id="adjustment-times-{{ $row['id'] }}" name="times" type="number" min="0.01" step="0.01" class="backend-form-control" value="{{ $row['model']->times }}" required></div>
                    </div></div>
                    <footer class="backend-modal__footer"><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-save" aria-hidden="true"></i>{{ __('invoices.update_adjustment') }}</button></footer>
                </form>
            </div></div>
        </div>
    @endforeach
@endif

@if ($canChangeBank)
    <div class="modal fade backend-modal" id="invoice-bank-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content backend-modal">
            <header class="backend-modal__header"><div><span>{{ __('invoices.context_eyebrow') }}</span><h2>{{ __('invoices.change_bank_title') }}</h2></div><x-backend.modal-close label="{{ __('invoices.close') }}" /></header>
            <form action="{{ route('admin.invoices.bank.update', $invoice) }}" method="post" class="backend-form">
                @csrf @method('put')
                <div class="backend-modal__body"><div class="backend-form-field"><label class="backend-form-label" for="invoice-bank-id">{{ __('invoices.bank') }}</label><select id="invoice-bank-id" name="bank_id" class="backend-form-control" required><option value="">{{ __('invoices.select_bank') }}</option>@foreach ($invoiceBankOptions as $bank)<option value="{{ $bank->id }}" @selected((int) $invoice->bank_id === (int) $bank->id)>{{ $bank->bank }} · {{ $bank->currency }} · {{ $bank->account_number }}</option>@endforeach</select></div></div>
                <footer class="backend-modal__footer"><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-save" aria-hidden="true"></i>{{ __('invoices.save_bank') }}</button></footer>
            </form>
        </div></div>
    </div>
@endif
