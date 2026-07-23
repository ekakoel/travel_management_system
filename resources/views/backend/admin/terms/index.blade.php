@extends('layouts.head')

@section('title', __('messages.Term and Condition'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/terms/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/terms/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container terms-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero class="terms-admin-hero">
                    <x-slot name="kicker">
                        Policy Administration
                    </x-slot>
                    <x-slot name="heading">
                        Terms and Conditions
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            Manage public terms, privacy policy content, operational rules, pricing policy, promotion terms, and FAQ entries used across customer and partner pages.
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#add-new-policy">
                            <i class="fa fa-plus"></i>
                            Add Policy
                        </button>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar terms-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Terms and Conditions</li>
                        </ol>
                    </nav>
                    <a class="terms-admin-public-link" href="{{ route('terms-and-conditions') }}" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-external-link"></i>
                        Public Page
                    </a>
                </section>

                @if ($errors->any() || session('success'))
                    <section class="backend-feedback terms-admin-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger terms-admin-alert terms-admin-alert--danger">
                                <strong>Form needs attention.</strong>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="backend-alert backend-alert--success terms-admin-alert terms-admin-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Policy summary">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon"><i class="fa fa-file-text-o"></i></div>
                        <div>
                            <span>Total Policies</span>
                            <strong>{{ number_format($summary['total']) }}</strong>
                            <small>All managed policy records.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon"><i class="fa fa-check-circle"></i></div>
                        <div>
                            <span>Active</span>
                            <strong>{{ number_format($summary['active']) }}</strong>
                            <small>Visible on public pages.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon"><i class="fa fa-pencil-square-o"></i></div>
                        <div>
                            <span>Draft</span>
                            <strong>{{ number_format($summary['draft']) }}</strong>
                            <small>Saved but hidden from public pages.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="fa fa-question-circle"></i></div>
                        <div>
                            <span>FAQ</span>
                            <strong>{{ number_format($summary['faq']) }}</strong>
                            <small>Help center entries.</small>
                        </div>
                    </article>
                </section>

                <section class="terms-admin-layout">
                    <div class="terms-admin-main">
                        @foreach ($policySections as $section)
                            @php
                                $activeCount = $section['items']->where('status', 'Active')->count();
                                $draftCount = $section['items']->where('status', 'Draft')->count();
                            @endphp

                            <article class="backend-panel terms-admin-section">
                                <header class="backend-section-header terms-admin-section__heading">
                                    <div>
                                        <span class="backend-section-header__label">{{ $section['type'] }}</span>
                                        <h2>{{ $section['title'] }}</h2>
                                    </div>
                                    <div class="terms-admin-counts">
                                        <span class="backend-status-badge backend-status-badge--success terms-admin-badge is-success">{{ $activeCount }} Active</span>
                                        <span class="backend-status-badge backend-status-badge--muted terms-admin-badge is-muted">{{ $draftCount }} Draft</span>
                                    </div>
                                </header>

                                <div class="terms-admin-policy-list">
                                    @forelse ($section['items'] as $policy)
                                        <div class="terms-admin-policy-card {{ $policy->status === 'Draft' ? 'is-draft' : '' }}">
                                            <div class="terms-admin-policy-card__content">
                                                <div class="terms-admin-policy-card__title">
                                                    <span class="backend-status-badge {{ $policy->status === 'Active' ? 'backend-status-badge--success' : 'backend-status-badge--muted' }} terms-admin-badge {{ $policy->status === 'Active' ? 'is-success' : 'is-muted' }}">{{ $policy->status }}</span>
                                                    <h3>{{ $policy->name_en }}</h3>
                                                </div>
                                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($policy->policy_en), 210) }}</p>
                                                <dl class="terms-admin-policy-meta">
                                                    <div>
                                                        <dt>ID</dt>
                                                        <dd>{{ $policy->name_id }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>ZH</dt>
                                                        <dd>{{ $policy->name_zh }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Updated</dt>
                                                        <dd>{{ optional($policy->updated_at)->format('d M Y H:i') ?? '-' }}</dd>
                                                    </div>
                                                </dl>
                                            </div>
                                            <div class="terms-admin-policy-card__actions">
                                                <button type="button" class="terms-admin-icon-action" data-toggle="modal" data-target="#edit-policy-{{ $policy->id }}" aria-label="Edit {{ $policy->name_en }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <form action="{{ route('term-and-condition.policy.destroy', $policy->id) }}" method="POST" data-terms-delete-form>
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="terms-admin-icon-action is-danger" aria-label="Delete {{ $policy->name_en }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @include('backend.admin.terms.partials.policy-modal', [
                                            'modalId' => 'edit-policy-'.$policy->id,
                                            'formId' => 'edit-policy-form-'.$policy->id,
                                            'action' => route('term-and-condition.policy.update', $policy->id),
                                            'method' => 'put',
                                            'title' => 'Update '.$section['title'],
                                            'policy' => $policy,
                                            'policyTypes' => $policyTypes,
                                        ])
                                    @empty
                                        <div class="terms-admin-empty">
                                            <strong>No {{ $section['title'] }} content yet.</strong>
                                            <span>Add a policy and select type {{ $section['type'] }}.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="terms-admin-side">
                        <section class="backend-panel terms-admin-guide">
                            <div class="backend-section-header terms-admin-guide__heading">
                                <div>
                                    <span class="backend-section-header__label">Publishing Rules</span>
                                    <h2>Content Flow</h2>
                                </div>
                            </div>
                            <ul>
                                <li>Use <strong>Draft</strong> while writing or translating content.</li>
                                <li>Only <strong>Active</strong> content appears on public pages.</li>
                                <li>Keep ID, EN, and ZH content aligned before publishing.</li>
                                <li>Use type <strong>FAQ</strong> for Help Center questions.</li>
                            </ul>
                        </section>

                        <section class="backend-panel terms-admin-guide">
                            <div class="backend-section-header terms-admin-guide__heading">
                                <div>
                                    <span class="backend-section-header__label">Public Mapping</span>
                                    <h2>Where It Appears</h2>
                                </div>
                            </div>
                            <dl>
                                <div>
                                    <dt>Terms</dt>
                                    <dd>User, System, Administrator, Currency, Price, Promotion</dd>
                                </div>
                                <div>
                                    <dt>Privacy</dt>
                                    <dd>System policy</dd>
                                </div>
                                <div>
                                    <dt>FAQ</dt>
                                    <dd>FAQ policy type</dd>
                                </div>
                            </dl>
                        </section>
                    </aside>
                </section>

                @include('backend.admin.terms.partials.policy-modal', [
                    'modalId' => 'add-new-policy',
                    'formId' => 'add-term-and-condition',
                    'action' => route('term-and-condition.policy.store'),
                    'method' => 'put',
                    'title' => 'Add New Policy',
                    'policy' => null,
                    'policyTypes' => $policyTypes,
                ])

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
