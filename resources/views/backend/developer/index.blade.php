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
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container admin-panel-page" data-admin-panel>
            <div class="pd-ltr-20">
                <section class="admin-panel-hero">
                    <div>
                        <span class="admin-panel-eyebrow">Developer Workspace</span>
                        <h1>Admin Panel</h1>
                        <p>Review platform configuration, service registry health, integration readiness, and developer notes from one focused backend workspace.</p>
                    </div>
                    <button type="button" class="admin-panel-primary-action" data-toggle="modal" data-target="#add-service">
                        <i class="fa fa-plus"></i>
                        Add Service
                    </button>
                </section>

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

                <section class="admin-panel-stat-grid" aria-label="Admin panel summary">
                    @foreach ($dashboardStats as $stat)
                        <article class="admin-panel-stat admin-panel-stat--{{ $stat['tone'] }}">
                            <div class="admin-panel-stat__icon">
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

                <section class="admin-panel-grid">
                    <div class="admin-panel-section admin-panel-section--wide">
                        <div class="admin-panel-section__header">
                            <div>
                                <span class="admin-panel-section__label">Service Registry</span>
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

                    <aside class="admin-panel-section">
                        <div class="admin-panel-section__header">
                            <div>
                                <span class="admin-panel-section__label">Integration</span>
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
                    <div class="admin-panel-section">
                        <div class="admin-panel-section__header">
                            <div>
                                <span class="admin-panel-section__label">Developer Focus</span>
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

                @if ($attentions->isNotEmpty())
                    <section class="admin-panel-section admin-panel-attention">
                        <div class="admin-panel-section__header">
                            <div>
                                <span class="admin-panel-section__label">Attention</span>
                                <h2>Admin Notes</h2>
                            </div>
                        </div>
                        <div class="admin-attention-list">
                            @foreach ($attentions as $attention)
                                <article>
                                    <strong>{{ $attention->name }}</strong>
                                    <p>{{ $attention->attention_en ?: $attention->attention_zh }}</p>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

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
