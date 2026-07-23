@php
    $bank = $bank ?? null;
    $method = $method ?? 'post';
    $currencies = [
        'IDR' => 'IDR (Rp)',
        'USD' => 'USD ($)',
        'CNY' => 'CNY (CNY)',
        'TWD' => 'TWD (NT$)',
    ];
@endphp

<div class="modal fade backend-modal currency-admin-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="backend-modal__header currency-admin-modal__header">
                <div>
                    <span>Payment Account</span>
                    <h3>{{ $title }}</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="{{ $formId }}" action="{{ $action }}" method="post">
                @csrf
                @if (strtolower($method) !== 'post')
                    @method($method)
                @endif
                <div class="backend-modal__body currency-admin-modal__body">
                    <div class="currency-admin-form-grid currency-admin-form-grid--wide">
                        <label>
                            <span>Bank <b>*</b></span>
                            <input name="bank" type="text" value="{{ old('bank', optional($bank)->bank) }}" required>
                        </label>
                        <label>
                            <span>Currency <b>*</b></span>
                            <select name="currency" required>
                                <option value="">Select currency</option>
                                @foreach ($currencies as $value => $label)
                                    <option value="{{ $value }}" {{ old('currency', optional($bank)->currency) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Account Name <b>*</b></span>
                            <input name="account_name" type="text" value="{{ old('account_name', optional($bank)->account_name) }}" required>
                        </label>
                        <label>
                            <span>Account Number <b>*</b></span>
                            <input name="account_number" type="text" value="{{ old('account_number', optional($bank)->account_number) }}" required>
                        </label>
                        <label>
                            <span>Location <b>*</b></span>
                            <input name="location" type="text" value="{{ old('location', optional($bank)->location) }}" required>
                        </label>
                        <label>
                            <span>Telephone</span>
                            <input name="telephone" type="text" value="{{ old('telephone', optional($bank)->telephone) }}">
                        </label>
                        <label class="currency-admin-field-span">
                            <span>Address</span>
                            <input name="address" type="text" value="{{ old('address', optional($bank)->address) }}">
                        </label>
                        <label>
                            <span>SWIFT Code</span>
                            <input name="swift_code" type="text" value="{{ old('swift_code', optional($bank)->swift_code) }}">
                        </label>
                        <label>
                            <span>Bank Code</span>
                            <input name="bank_code" type="text" value="{{ old('bank_code', optional($bank)->bank_code) }}">
                        </label>
                    </div>
                </div>
                <div class="backend-modal__footer currency-admin-modal__footer">
                    <button type="button" class="currency-admin-ghost-action" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="currency-admin-primary-action">
                        <i class="fa fa-check"></i>
                        Save Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
