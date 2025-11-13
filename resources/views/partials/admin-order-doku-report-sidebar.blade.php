@uiEnabled('doku-payment')
    @if ($order->reservations?->invoice)
        @if ($doku_payment || $doku_payment_paid)
            @if ($order->status == "Paid" && $doku_payment_paid)
                <div class="col-md-12 m-b-18">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="title">@lang('messages.DOKU Payment')</div>
                        </div>
                        <button id="checkout-button-{{ $device }}" class="w-100">
                            <div class="card-ptext-margin">
                                <div class="pmt-des m-b-8">
                                    <div class="card-ptext-content">
                                        <div class="ptext-title">@lang('messages.Invoice No').</div>
                                        <div class="ptext-value">{{ $doku_payment_paid->invoice_number }}</div>
                                        <div class="ptext-title">@lang('messages.Payment deadline')</div>
                                        <div class="ptext-value">{{ date('d F Y',strtotime($doku_payment_paid->expired_date)) }}</div>
                                        <div class="ptext-title">@lang('messages.Amount')</div>
                                        <div class="ptext-value">{{ currencyFormatIdr($doku_payment_paid->amount) }}</div>
                                    </div>
                                </div>
                            </div>
                        </button>
                        <script type="text/javascript">
                            var device = @json($device);
                            var checkoutButton = document.getElementById('checkout-button-'+ device);
                            var paymentUrl = @json($doku_payment_paid->checkout_url);
                            checkoutButton.addEventListener('click', function () {
                                loadJokulCheckout(paymentUrl);
                            });
                        </script>
                        <div class="paid-stamp">
                            <img src="{{ asset('storage/icon/paid_icon.png') }}" alt="Paid Icon">
                        </div>
                    </div>
                </div>
            @else
                @if ($doku_payment)
                    <div class="col-md-12 m-b-18">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">@lang('messages.DOKU Payment')</div>
                            </div>
                            <button id="checkout-button-{{ $device }}" class="w-100">
                                <div class="card-ptext-margin">
                                    <div class="pmt-des m-b-8">
                                        <div class="card-ptext-content">
                                            <div class="ptext-title">@lang('messages.Invoice No').</div>
                                            <div class="ptext-value">{{ $doku_payment->invoice_number }}</div>
                                            <div class="ptext-title">@lang('messages.Payment deadline')</div>
                                            <div class="ptext-value">{{ date('d F Y',strtotime($doku_payment->expired_date)) }}</div>
                                            <div class="ptext-title">@lang('messages.Amount')</div>
                                            <div class="ptext-value">{{ currencyFormatIdr($doku_payment->amount) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                            <script type="text/javascript">
                                var device = @json($device);
                                var checkoutButton = document.getElementById('checkout-button-'+ device);
                                var paymentUrl = @json($doku_payment->checkout_url);
                                checkoutButton.addEventListener('click', function () {
                                    loadJokulCheckout(paymentUrl);
                                });
                            </script>
                            @if ($doku_payment->status == "Paid")
                                <div class="paid-stamp">
                                    <img src="{{ asset('storage/icon/paid_icon.png') }}" alt="Paid Icon">
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        @else
            @if ($order->status == "Approved")
                @canany(['posDev','posRsv'])
                    <div class="col-md-12">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">@lang('messages.DOKU Payment')</div>
                            </div>
                            <div class="notification">
                                <p>DOKU Payment not available, please generate one! and the Agent can make payment using DOKU</p>
                            </div>
                        </div>
                    </div>
                @endcanany
            @endif
        @endif
    @endif
@endUiEnabled