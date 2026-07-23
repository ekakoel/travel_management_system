@extends('layouts.head')
@section('title', __('messages.Admin Panel'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/panel/index.css') }}">
@endpush

@section('content')
    @can('isAdmin')
        @php
            $userId = Auth::id();
            $currencyNames = ['USD', 'CNY', 'TWD'];
            $trafficSeries = $trafficAnalytics['series'];
            $trafficPeriods = $trafficAnalytics['periods'];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container admin-panel-page" data-admin-panel>
            <div class="pd-ltr-20">
                <x-backend.page-hero class="admin-panel-hero">
                    <x-slot name="kicker">
                        Developer Workspace
                    </x-slot>
                    <x-slot name="heading">
                        Admin Panel
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            Review platform configuration, service registry health, integration readiness, and developer notes from one focused backend workspace.
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#add-service">
                            <i class="fa fa-plus"></i>
                            Add Service
                        </button>
                    </x-slot>
                </x-backend.page-hero>

                @if ($errors->any() || session()->has('success'))
                    <section class="admin-panel-feedback">
                        @if ($errors->any())
                            <div class="admin-panel-alert admin-panel-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="admin-panel-alert admin-panel-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Admin panel summary">
                    @foreach ($dashboardStats as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon">
                                <i class="{{ $stat['icon'] }}"></i>
                            </div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ $stat['value'] }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="backend-panel admin-registration-access">
                    <div class="backend-section-header">
                        <div>
                            <span class="backend-section-header__label">Access Control</span>
                            <h2>Registration Access</h2>
                        </div>
                        <p>Control public registration availability. When disabled, registration pages and direct submit requests are blocked by backend middleware.</p>
                    </div>

                    <div class="admin-registration-access__body">
                        <div class="admin-registration-access__state">
                            <span class="admin-registration-access__indicator {{ $registrationAccess->status ? 'is-enabled' : 'is-disabled' }}"></span>
                            <div>
                                <strong>{{ $registrationAccess->status ? 'Registration Enabled' : 'Registration Disabled' }}</strong>
                                <small>{{ $registrationAccess->status ? 'Visitors can open registration pages and submit requests.' : 'Visitors cannot open or submit registration from any public endpoint.' }}</small>
                            </div>
                        </div>

                        <form action="{{ route('admin-panel.registration-access.update') }}" method="post" class="admin-registration-access__form">
                            @csrf
                            @method('PUT')
                            <button
                                type="submit"
                                name="enabled"
                                value="1"
                                class="admin-registration-access__button {{ $registrationAccess->status ? 'is-active' : '' }}"
                                {{ $registrationAccess->status ? 'disabled' : '' }}
                            >
                                Enable
                            </button>
                            <button
                                type="submit"
                                name="enabled"
                                value="0"
                                class="admin-registration-access__button {{ ! $registrationAccess->status ? 'is-active' : '' }}"
                                {{ ! $registrationAccess->status ? 'disabled' : '' }}
                                data-confirm="Disable public registration? Existing users can still log in, but new registration GET and POST requests will be blocked."
                            >
                                Disable
                            </button>
                        </form>
                    </div>
                </section>

                <section class="backend-panel admin-analytics-section" data-traffic-analytics='@json($trafficPeriods)'>
                    <div class="backend-section-header">
                        <div>
                            <span class="backend-section-header__label">Website Analytics</span>
                            <h2>Traffic Overview</h2>
                        </div>
                        <p>Monitor website access trends, visitor origin, and the pages receiving the most attention.</p>
                    </div>

                    {{-- <div class="admin-analytics-summary">
                        @foreach ($trafficAnalytics['summary'] as $item)
                            <div class="admin-analytics-summary__item">
                                <span>{{ $item['label'] }}</span>
                                <strong>{{ number_format($item['value']) }}</strong>
                                <small>{{ $item['meta'] }}</small>
                            </div>
                        @endforeach
                    </div> --}}

                    <div class="admin-analytics-toolbar" aria-label="Traffic period selector">
                        @foreach (['day' => 'Daily', 'week' => 'Weekly', 'month' => 'Monthly', 'year' => 'Yearly'] as $period => $label)
                            <button
                                type="button"
                                class="admin-analytics-period {{ $period === 'day' ? 'is-active' : '' }}"
                                data-analytics-period="{{ $period }}"
                                aria-pressed="{{ $period === 'day' ? 'true' : 'false' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="admin-analytics-period-summary" data-analytics-summary></div>

                    <div class="admin-analytics-chart-shell">
                        <article class="admin-analytics-chart">
                            <div class="admin-analytics-chart__header">
                                <div>
                                    <span data-analytics-chart-label>Daily</span>
                                    <strong data-analytics-chart-total>0 visits</strong>
                                </div>
                                <small data-analytics-chart-range>-</small>
                            </div>
                            <div class="admin-analytics-chart__canvas">
                                <div class="admin-analytics-chart__grid" aria-hidden="true">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="admin-analytics-chart__bars" data-analytics-chart-bars aria-label="Traffic chart"></div>
                            </div>
                            <div class="admin-analytics-chart__labels" data-analytics-chart-labels></div>
                        </article>

                        <aside class="admin-analytics-insight">
                            <span class="backend-section-header__label">Period Insight</span>
                            <strong data-analytics-insight-title>Daily traffic</strong>
                            <p data-analytics-insight-copy>Loading tracked website traffic.</p>
                        </aside>
                    </div>

                    <div class="admin-analytics-breakdown">
                        <div data-analytics-breakdown="countries">
                            <h3>Top Countries</h3>
                        </div>
                        <div data-analytics-breakdown="pages">
                            <h3>Top Pages</h3>
                        </div>
                        <div data-analytics-breakdown="devices">
                            <h3>Device Split</h3>
                        </div>
                        <div data-analytics-breakdown="referrers">
                            <h3>Referrers</h3>
                        </div>
                        <div data-analytics-breakdown="areas">
                            <h3>Site Areas</h3>
                        </div>
                    </div>
                </section>

                <section class="admin-panel-grid">
                    <div class="backend-panel admin-panel-wide">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Service Registry</span>
                                <h2>Registered Services</h2>
                            </div>
                            <p>Maintain service metadata, status, icon classes, and content health from the developer registry.</p>
                        </div>

                        <div class="admin-service-list">
                            @forelse ($services as $service)
                                <article class="admin-service-row">
                                    <div class="admin-service-row__identity">
                                        <span class="admin-service-row__icon"><i class="{{ $service['icon'] }}"></i></span>
                                        <div>
                                            <h3>{{ $service['name'] }}</h3>
                                            <p>{{ $service['nicname'] ?: 'No slug configured' }}</p>
                                        </div>
                                    </div>
                                    <div class="admin-service-row__counts">
                                        <span><strong>{{ $service['active_count'] }}</strong> Active</span>
                                        <span><strong>{{ $service['draft_count'] }}</strong> Draft</span>
                                        <span><strong>{{ $service['total_count'] }}</strong> Total</span>
                                    </div>
                                    <span class="admin-status-pill admin-status-pill--{{ strtolower($service['status']) }}">{{ $service['status'] }}</span>
                                    <div class="admin-service-row__actions">
                                        <button type="button" data-toggle="modal" data-target="#edit-service-{{ $service['id'] }}">Edit</button>

                                        @if ($service['status'] === 'Active')
                                            <form action="{{ route('f-disable-service', $service['id']) }}" method="post" data-confirm="Disable this service?">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="author" value="{{ $userId }}">
                                                <input type="hidden" name="status" value="Draft">
                                                <button type="submit">Disable</button>
                                            </form>
                                        @else
                                            <form action="{{ route('f-enable-service', $service['id']) }}" method="post" data-confirm="Enable this service?">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="author" value="{{ $userId }}">
                                                <input type="hidden" name="status" value="Active">
                                                <button type="submit">Enable</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('f-remove-service', $service['id']) }}" method="post" data-confirm="Remove this service permanently?">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="author" value="{{ $userId }}">
                                            <input type="hidden" name="service" value="{{ $service['name'] }}">
                                            <button type="submit" class="admin-action-danger">Remove</button>
                                        </form>
                                    </div>
                                </article>

                                <div class="modal fade admin-panel-modal" id="edit-service-{{ $service['id'] }}" tabindex="-1" role="dialog" aria-labelledby="edit-service-title-{{ $service['id'] }}">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form class="modal-content" action="{{ route('f-edit-service', $service['id']) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                                                <span class="admin-panel-eyebrow">Service Management</span>
                                                <h4 class="modal-title" id="edit-service-title-{{ $service['id'] }}">Edit {{ $service['name'] }}</h4>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="author" value="{{ $userId }}">
                                                <div class="admin-panel-form-grid">
                                                    <label>
                                                        Service Name
                                                        <input type="text" name="name" value="{{ $service['name'] }}" required>
                                                    </label>
                                                    <label>
                                                        Service Slug
                                                        <input type="text" name="nicname" value="{{ $service['nicname'] }}" required>
                                                    </label>
                                                    <label>
                                                        Icon Class
                                                        <input type="text" name="icon" value="{{ $service['icon'] }}" required>
                                                    </label>
                                                    <label>
                                                        Status
                                                        <select name="status" required>
                                                            <option value="Active" {{ $service['status'] === 'Active' ? 'selected' : '' }}>Active</option>
                                                            <option value="Draft" {{ $service['status'] !== 'Active' ? 'selected' : '' }}>Draft</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="admin-panel-secondary-action" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="admin-panel-primary-action">Save Service</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="admin-panel-empty">No services have been configured yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <aside class="backend-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Integration</span>
                                <h2>Currency Readiness</h2>
                            </div>
                        </div>
                        <div class="admin-currency-list">
                            @foreach ($currencyNames as $currencyName)
                                @php($rate = $currencyRates->get($currencyName))
                                <div class="admin-currency-row">
                                    <span>{{ $currencyName }}</span>
                                    <strong>{{ $rate ? currencyFormatIdr($rate->sell) : '-' }}</strong>
                                    <small>Sell rate</small>
                                </div>
                            @endforeach
                        </div>
                    </aside>
                </section>

                <section class="admin-panel-grid admin-panel-grid--single">
                    <div class="backend-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Developer Focus</span>
                                <h2>Platform Health Checks</h2>
                            </div>
                            <p>High-signal technical checks that developer users should review before changing backend configuration.</p>
                        </div>
                        <div class="admin-health-grid">
                            @foreach ($developerHealthChecks as $check)
                                <article class="admin-health-item admin-health-item--{{ $check['tone'] }}">
                                    <span>{{ $check['label'] }}</span>
                                    <strong>{{ $check['status'] }}</strong>
                                    <p>{{ $check['meta'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                @include('layouts.footer')
            </div>
        </main>

        <div class="modal fade admin-panel-modal" id="add-service" tabindex="-1" role="dialog" aria-labelledby="add-service-title">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content" action="{{ route('f-add-service') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        <span class="admin-panel-eyebrow">Service Management</span>
                        <h4 class="modal-title" id="add-service-title">Add Service</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="author" value="{{ $userId }}">
                        <div class="admin-panel-form-grid">
                            <label>
                                Service Name
                                <input type="text" name="name" placeholder="Hotels" required>
                            </label>
                            <label>
                                Service Slug
                                <input type="text" name="nicname" placeholder="hotels" required>
                            </label>
                            <label class="admin-panel-form-grid__wide">
                                Icon Class
                                <input type="text" name="icon" placeholder="fa fa-hotel" required>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="admin-panel-secondary-action" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="admin-panel-primary-action">Create Service</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/panel/index.js') }}" defer></script>
@endpush
