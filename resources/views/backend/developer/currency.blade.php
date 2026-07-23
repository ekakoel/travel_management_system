@extends('layouts.head')
@section('title', __('messages.Currency'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/currency/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/currency/index.js') }}"></script>
@endpush

@section('content')
    @php
        $rateMeta = [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'icon' => 'fa-usd', 'route' => 'f-update-usd-rates'],
            'CNY' => ['name' => 'Chinese Yuan', 'symbol' => 'CNY', 'icon' => 'fa-jpy', 'route' => 'f-update-cny-rates'],
            'TWD' => ['name' => 'New Taiwan Dollar', 'symbol' => 'NT$', 'icon' => 'fa-money', 'route' => 'f-update-twd-rates'],
        ];
        $formatIdr = fn ($value) => is_numeric($value) ? currencyFormatIdr((float) $value) : '-';
        $formatNumber = fn ($value) => is_numeric($value) ? number_format((float) $value, 2, '.', ',') : '-';
        $externalStatus = $externalRates['status'] ?? 'unavailable';
        $externalUpdatedAt = $externalRates['retrieved_at'] ?? null;
    @endphp

    <div class="mobile-menu-overlay"></div>
    <main class="main-container currency-admin-page">
        <div class="pd-ltr-20">
            <x-backend.page-hero class="currency-admin-header">
                <x-slot name="kicker">
                    Finance Configuration
                </x-slot>
                <x-slot name="heading">
                    Currency
                </x-slot>
                <x-slot name="copy">
                    <p>
                        Manage selling rates, buying rates, tax configuration, and payment bank accounts used by backend order and invoice workflows.
                    </p>
                </x-slot>
                <x-slot name="action">
                    @canany(['posDev','posAuthor'])
                        <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#add-bank-account">
                        <i class="fa fa-plus"></i>
                        Add Bank Account
                    </button>
                        @endcanany
                </x-slot>
            </x-backend.page-hero>

            @if ($errors->any() || session()->has('success') || session()->has('error'))
                <section class="backend-feedback currency-admin-feedback">
                    @if ($errors->any())
                        <div class="backend-alert backend-alert--danger currency-admin-alert currency-admin-alert--danger">
                            <strong>Action needs attention.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="backend-alert backend-alert--success currency-admin-alert currency-admin-alert--success">
                            <strong>Saved.</strong>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="backend-alert backend-alert--danger currency-admin-alert currency-admin-alert--danger">
                            <strong>Unable to save.</strong>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </section>
            @endif

            <section class="backend-page-toolbar currency-admin-toolbar">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Currency</li>
                    </ol>
                </nav>
                <div class="currency-admin-source currency-admin-source--{{ $externalStatus === 'available' ? 'ok' : 'warning' }}">
                    <i class="fa {{ $externalStatus === 'available' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                    <span>
                        External rates {{ $externalStatus === 'available' ? 'available' : 'using local fallback' }}
                        @if ($externalUpdatedAt)
                            <small>{{ $externalUpdatedAt->format('d M Y H:i') }}</small>
                        @endif
                    </span>
                </div>
            </section>

            <section class="currency-admin-grid">
                <div class="currency-admin-rates">
                    @foreach ($currencyRates as $rate)
                        @php
                            $code = $rate['code'];
                            $meta = $rateMeta[$code];
                            $spread = is_numeric($rate['sell']) && is_numeric($rate['buy']) ? (float) $rate['sell'] - (float) $rate['buy'] : null;
                            $routeName = $meta['route'];
                        @endphp

                        <article class="currency-rate-card">
                            <div class="currency-rate-card__header">
                                <div>
                                    <span class="currency-rate-card__code">{{ $code }}</span>
                                    <h2>{{ $meta['name'] }}</h2>
                                </div>
                                <span class="currency-rate-card__icon"><i class="fa {{ $meta['icon'] }}"></i></span>
                            </div>

                            <div class="currency-rate-card__body">
                                <div class="currency-rate-card__metric currency-rate-card__metric--primary">
                                    <span>Market reference</span>
                                    <strong>{{ $formatIdr($rate['external_rate']) }}</strong>
                                    <small>{{ $formatNumber($rate['external_rate']) }}</small>
                                </div>
                                <div class="currency-rate-card__pair">
                                    <div>
                                        <span>Sell</span>
                                        <strong>{{ $formatIdr($rate['sell']) }}</strong>
                                    </div>
                                    <div>
                                        <span>Buy</span>
                                        <strong>{{ $formatIdr($rate['buy']) }}</strong>
                                    </div>
                                    <div>
                                        <span>Spread</span>
                                        <strong>{{ $formatIdr($spread) }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="currency-rate-card__footer">
                                <span>
                                    <i class="fa fa-clock-o"></i>
                                    {{ $rate['updated_at'] ? $rate['updated_at']->format('d M Y H:i') : 'Not configured' }}
                                </span>
                                @canany(['posDev','posAuthor'])
                                    @if ($rate['id'])
                                        <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#edit-rate-{{ $code }}">
                                            <i class="fa fa-pencil"></i>
                                            Update
                                        </button>
                                    @endif
                                @endcanany
                            </div>
                        </article>

                        @canany(['posDev','posAuthor'])
                            @if ($rate['id'])
                                <div class="modal fade backend-modal currency-admin-modal" id="edit-rate-{{ $code }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="backend-modal__header currency-admin-modal__header">
                                                <div>
                                                    <span>{{ $code }} Rate</span>
                                                    <h3>Update Currency Rate</h3>
                                                </div>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="edit-rate-form-{{ $code }}" action="{{ route($routeName, $rate['id']) }}" method="post">
                                                @csrf
                                                @method('put')
                                                <div class="backend-modal__body currency-admin-modal__body">
                                                    <div class="currency-admin-current">
                                                        <span>Current sell rate</span>
                                                        <strong>{{ $formatIdr($rate['sell']) }}</strong>
                                                    </div>
                                                    <div class="backend-form-grid currency-admin-form-grid">
                                                        <label>
                                                            <span>Sell Rate <b>*</b></span>
                                                            <input class="backend-form-control" name="sell" type="number" step="0.01" min="0" value="{{ old('sell', $rate['sell']) }}" required>
                                                        </label>
                                                        <label>
                                                            <span>Spread / Difference <b>*</b></span>
                                                            <input class="backend-form-control" name="difference" type="number" step="0.01" min="0" value="{{ old('difference', $rate['difference']) }}" required>
                                                        </label>
                                                    </div>
                                                    <p class="currency-admin-help">Buy rate is calculated automatically from sell rate minus spread.</p>
                                                </div>
                                                <div class="backend-modal__footer currency-admin-modal__footer">
                                                    <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="backend-button backend-button-primary">
                                                        <i class="fa fa-check"></i>
                                                        Save Rate
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endcanany
                    @endforeach
                </div>

                <aside class="currency-admin-side">
                    <article class="currency-tax-card">
                        <div>
                            <span class="currency-admin-eyebrow">Tax</span>
                            <h2>{{ $tax ? $formatNumber($tax->tax) : '-' }}%</h2>
                            <p>Applied to pricing calculation where tax configuration is required.</p>
                        </div>
                        @canany(['posDev','posAuthor'])
                            @if ($tax)
                                <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#edit-tax">
                                    <i class="fa fa-pencil"></i>
                                    Update Tax
                                </button>
                            @endif
                        @endcanany
                    </article>

                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="fa fa-university"></i></div>
                        <div>
                            <span>Payment Accounts</span>
                            <strong>{{ $bank_acc->count() }}</strong>
                            <small>Active bank account records grouped by currency for invoice payment instructions.</small>
                        </div>
                    </article>
                </aside>
            </section>

            <section class="backend-panel currency-bank-section">
                <div class="backend-section-header currency-admin-section-heading">
                    <div>
                        <span class="backend-section-header__label">Bank Accounts</span>
                        <h2>Payment Account Directory</h2>
                    </div>
                    @canany(['posDev','posAuthor'])
                        <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#add-bank-account">
                            <i class="fa fa-plus"></i>
                            Add Account
                        </button>
                    @endcanany
                </div>

                <div class="currency-bank-list">
                    @forelse ($bank_acc as $bank)
                        <article class="currency-bank-card">
                            <div class="currency-bank-card__header">
                                <div>
                                    <span>{{ $bank->currency }}</span>
                                    <h3>{{ $bank->bank }}</h3>
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="currency-bank-card__actions">
                                        <button type="button" data-toggle="modal" data-target="#edit-bank-account-{{ $bank->id }}" title="Edit bank account">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <form action="/delete-bank-account/{{ $bank->id }}" method="post" data-confirm-delete="Delete {{ $bank->bank }} account?">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" title="Delete bank account">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endcanany
                            </div>
                            <dl class="currency-bank-card__details">
                                <div>
                                    <dt>Account Name</dt>
                                    <dd>{{ $bank->account_name ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>Account Number</dt>
                                    <dd>{{ $bank->account_number ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>Location</dt>
                                    <dd>{{ $bank->location ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>Address</dt>
                                    <dd>{{ $bank->address ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>Telephone</dt>
                                    <dd>{{ $bank->telephone ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>SWIFT Code</dt>
                                    <dd>{{ $bank->swift_code ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>Bank Code</dt>
                                    <dd>{{ $bank->bank_code ?: '-' }}</dd>
                                </div>
                            </dl>
                        </article>

                        @canany(['posDev','posAuthor'])
                            @include('backend.developer.partials.currency-bank-modal', [
                                'modalId' => 'edit-bank-account-' . $bank->id,
                                'title' => 'Edit Bank Account',
                                'formId' => 'edit-bank-account-form-' . $bank->id,
                                'action' => '/fupdate-bank-account/' . $bank->id,
                                'method' => 'put',
                                'bank' => $bank,
                            ])
                        @endcanany
                    @empty
                        <div class="backend-empty-state currency-admin-empty">
                            <i class="fa fa-university"></i>
                            <strong>No bank accounts configured.</strong>
                            <span>Add the first payment account to show payment instructions on invoices.</span>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>

    @canany(['posDev','posAuthor'])
        @if ($tax)
            <div class="modal fade backend-modal currency-admin-modal" id="edit-tax" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header currency-admin-modal__header">
                            <div>
                                <span>Tax Rate</span>
                                <h3>Update Tax</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit-tax-form" action="{{ route('f-update-tax', $tax->id) }}" method="post">
                            @csrf
                            @method('put')
                            <div class="backend-modal__body currency-admin-modal__body">
                                <label class="currency-admin-field">
                                    <span>Tax Percentage <b>*</b></span>
                                    <input class="backend-form-control" name="tax" type="number" step="0.01" min="0" value="{{ old('tax', $tax->tax) }}" required>
                                </label>
                                <input name="author" value="{{ Auth::id() }}" type="hidden">
                            </div>
                            <div class="backend-modal__footer currency-admin-modal__footer">
                                <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="backend-button backend-button-primary">
                                    <i class="fa fa-check"></i>
                                    Save Tax
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @include('backend.developer.partials.currency-bank-modal', [
            'modalId' => 'add-bank-account',
            'title' => 'Add Bank Account',
            'formId' => 'add-bank-account-form',
            'action' => '/fadd-bank-account',
            'method' => 'put',
            'bank' => null,
        ])
    @endcanany
@endsection
