@extends('layouts.head')

@section('title', __('messages.User Manager'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/users/manager.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/users/manager.js') }}" defer></script>
@endpush

@section('content')
    @can('posDev')
        @php
            $selectedSearch = $filters['search'] ?? '';
            $selectedPosition = $filters['position'] ?? '';
            $selectedStatus = $filters['status'] ?? '';
            $selectedApproval = $filters['approval'] ?? '';

            $statusLabel = function ($status) {
                return $status === 'Block' ? 'Blocked' : ($status ?: 'Unset');
            };

            $activityLabel = function ($user) use ($now) {
                if (!$user->session_id) {
                    return ['label' => 'No recent session', 'state' => 'muted'];
                }

                $lastSeen = \Carbon\Carbon::parse($user->session_id);

                if ($lastSeen->greaterThanOrEqualTo($now->copy()->subMinutes(5))) {
                    return ['label' => 'Online now', 'state' => 'online'];
                }

                return ['label' => 'Seen '.$lastSeen->diffForHumans(), 'state' => 'away'];
            };

            $profileImage = function ($user) {
                return $user->profileimg
                    ? asset('storage/user/profile/'.$user->profileimg)
                    : asset('storage/user/profile/default_user_img.png');
            };
        @endphp

        <div class="main-container user-manager-page">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero class="user-manager-hero">
                        <x-slot name="kicker">
                            Access Administration
                        </x-slot>
                        <x-slot name="heading">
                            User Manager
                        </x-slot>
                        <x-slot name="copy">
                            <p>
                                Manage staff, partner access, verification state, approval status, and account availability from one operational view.
                            </p>
                        </x-slot>
                        <x-slot name="action">
                            <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#user-add">
                                <i class="fa fa-plus"></i>
                                Add User
                            </button>
                        </x-slot>
                    </x-backend.page-hero>

                    <section class="backend-page-toolbar user-manager-toolbar">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item active" aria-current="page">User Manager</li>
                            </ol>
                        </nav>
                    </section>

                    @if ($errors->any() || session('success') || session('invalid'))
                        <section class="backend-feedback user-manager-feedback">
                            @if ($errors->any())
                                <div class="backend-alert backend-alert--danger user-manager-alert user-manager-alert--danger">
                                    <strong>Form needs attention</strong>
                                    @foreach ($errors->all() as $error)
                                        <span>{{ $error }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="backend-alert backend-alert--success user-manager-alert user-manager-alert--success">
                                    <strong>{{ session('success') }}</strong>
                                </div>
                            @endif

                            @if (session('invalid'))
                                <div class="backend-alert backend-alert--danger user-manager-alert user-manager-alert--danger">
                                    <strong>{{ session('invalid') }}</strong>
                                </div>
                            @endif
                        </section>
                    @endif

                    <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="User summary">
                        <article class="backend-kpi-card backend-kpi-card--teal">
                            <div class="backend-kpi-card__icon">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span>Total Users</span>
                                <strong>{{ number_format($summary['total']) }}</strong>
                                <small>All registered backend and partner accounts</small>
                            </div>
                        </article>
                        <article class="backend-kpi-card backend-kpi-card--green">
                            <div class="backend-kpi-card__icon">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span>Active</span>
                                <strong>{{ number_format($summary['active']) }}</strong>
                                <small>Accounts currently allowed to sign in</small>
                            </div>
                        </article>
                        <article class="backend-kpi-card backend-kpi-card--amber">
                            <div class="backend-kpi-card__icon">
                                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span>Pending Approval</span>
                                <strong>{{ number_format($summary['pendingApproval']) }}</strong>
                                <small>Need developer review before full access</small>
                            </div>
                        </article>
                        <article class="backend-kpi-card backend-kpi-card--blue">
                            <div class="backend-kpi-card__icon">
                                <i class="fa fa-circle" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span>Online</span>
                                <strong>{{ number_format($summary['online']) }}</strong>
                                <small>Seen in the last 5 minutes</small>
                            </div>
                        </article>
                    </section>

                    <section class="user-manager-layout">
                        <div class="user-manager-main">
                            <div class="backend-panel user-manager-panel user-manager-directory-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Directory</span>
                                        <h2>User Access Directory</h2>
                                        <p>{{ $users->total() }} matching records. Use filters to review role, access, approval, and activity status.</p>
                                    </div>
                                </div>

                                <form class="backend-filter-panel backend-filter-grid user-manager-filter" method="GET" action="{{ route('user-manager') }}">
                                    <label class="backend-filter-field">
                                        <span>Search</span>
                                        <input class="backend-form-control" type="search" name="search" value="{{ $selectedSearch }}" placeholder="Name, username, email, code, office">
                                    </label>
                                    <label class="backend-filter-field">
                                        <span>Group</span>
                                        <select class="backend-form-control form-select" name="position">
                                            <option value="">All groups</option>
                                            @foreach ($positions as $value => $label)
                                                <option value="{{ $value }}" @selected($selectedPosition === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="backend-filter-field">
                                        <span>Status</span>
                                        <select class="backend-form-control form-select" name="status">
                                            <option value="">All statuses</option>
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="backend-filter-field">
                                        <span>Approval</span>
                                        <select class="backend-form-control form-select" name="approval">
                                            <option value="">All approvals</option>
                                            <option value="approved" @selected($selectedApproval === 'approved')>Approved</option>
                                            <option value="pending" @selected($selectedApproval === 'pending')>Pending</option>
                                        </select>
                                    </label>
                                    <div class="backend-filter-actions user-manager-filter__actions">
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-filter"></i>
                                            Apply
                                        </button>
                                        <a href="{{ route('user-manager') }}" class="user-manager-secondary-action">Reset</a>
                                    </div>
                                </form>

                                <div class="backend-table-wrap user-manager-table-wrap">
                                    <table class="backend-table user-manager-table">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Group</th>
                                                <th>Status</th>
                                                <th>Approval</th>
                                                <th>Activity</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                @php
                                                    $activity = $activityLabel($user);
                                                    $isBlocked = $user->status === 'Block';
                                                @endphp
                                                <tr class="{{ $isBlocked ? 'is-blocked' : '' }}">
                                                    <td data-label="User">
                                                        <div class="user-manager-person">
                                                            <img src="{{ $profileImage($user) }}" alt="{{ $user->name }}">
                                                            <div>
                                                                <strong>{{ $user->name }}</strong>
                                                                <span>{{ $user->email }}</span>
                                                                <small>{{ $user->username }}{{ $user->code ? ' / '.$user->code : '' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td data-label="Group">
                                                        <strong>{{ $positions[$user->position] ?? ucfirst((string) $user->position) }}</strong>
                                                        <span>{{ $types[$user->type] ?? ucfirst((string) $user->type) }}</span>
                                                    </td>
                                                    <td data-label="Status">
                                                        <span class="backend-status-badge {{ $isBlocked ? 'backend-status-badge--danger' : 'backend-status-badge--success' }} user-manager-badge {{ $isBlocked ? 'is-danger' : 'is-success' }}">{{ $statusLabel($user->status) }}</span>
                                                    </td>
                                                    <td data-label="Approval">
                                                        <span class="backend-status-badge {{ $user->is_approved ? 'backend-status-badge--success' : 'backend-status-badge--warning' }} user-manager-badge {{ $user->is_approved ? 'is-success' : 'is-warning' }}">
                                                            {{ $user->is_approved ? 'Approved' : 'Pending' }}
                                                        </span>
                                                        <small>{{ $user->email_verified_at ? 'Verified' : 'Email unverified' }}</small>
                                                    </td>
                                                    <td data-label="Activity">
                                                        <span class="user-manager-activity is-{{ $activity['state'] }}">{{ $activity['label'] }}</span>
                                                    </td>
                                                    <td data-label="Actions">
                                                        <div class="backend-table-actions user-manager-row-actions">
                                                            @if (!$user->email_verified_at)
                                                                <form action="{{ route('verified-user', $user->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('put')
                                                                    <input type="hidden" name="verified" value="{{ $now->toDateTimeString() }}">
                                                                    <button type="submit" class="user-manager-icon-action" title="Verify email">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <button type="button" class="user-manager-icon-action" data-toggle="modal" data-target="#user-view-{{ $user->id }}" title="View user">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="user-manager-icon-action" data-toggle="modal" data-target="#user-edit-{{ $user->id }}" title="Edit user">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('remove-user', $user->id) }}" method="POST" data-confirm-delete="Remove {{ $user->name }}? If related records exist, the account will be blocked instead.">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="user-manager-icon-action is-danger" title="Remove user">
                                                                    <i class="fa fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="backend-table-empty user-manager-empty">
                                                            <i class="fa fa-users"></i>
                                                            <strong>No users found</strong>
                                                            <span>Try adjusting the filters or add a new user.</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="backend-table-card-list user-manager-mobile-list" aria-label="User access directory mobile view">
                                    @forelse ($users as $user)
                                        @php
                                            $activity = $activityLabel($user);
                                            $isBlocked = $user->status === 'Block';
                                        @endphp
                                        <article class="backend-table-card user-manager-mobile-card {{ $isBlocked ? 'is-blocked' : '' }}">
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">User</span>
                                                <div class="user-manager-person">
                                                    <img src="{{ $profileImage($user) }}" alt="{{ $user->name }}">
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        <span>{{ $user->email }}</span>
                                                        <small>{{ $user->username }}{{ $user->code ? ' / '.$user->code : '' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">Group</span>
                                                <strong>{{ $positions[$user->position] ?? ucfirst((string) $user->position) }}</strong>
                                                <span>{{ $types[$user->type] ?? ucfirst((string) $user->type) }}</span>
                                            </div>
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">Status</span>
                                                <span class="backend-status-badge {{ $isBlocked ? 'backend-status-badge--danger' : 'backend-status-badge--success' }} user-manager-badge {{ $isBlocked ? 'is-danger' : 'is-success' }}">{{ $statusLabel($user->status) }}</span>
                                            </div>
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">Approval</span>
                                                <span class="backend-status-badge {{ $user->is_approved ? 'backend-status-badge--success' : 'backend-status-badge--warning' }} user-manager-badge {{ $user->is_approved ? 'is-success' : 'is-warning' }}">
                                                    {{ $user->is_approved ? 'Approved' : 'Pending' }}
                                                </span>
                                                <small>{{ $user->email_verified_at ? 'Verified' : 'Email unverified' }}</small>
                                            </div>
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">Activity</span>
                                                <span class="user-manager-activity is-{{ $activity['state'] }}">{{ $activity['label'] }}</span>
                                            </div>
                                            <div class="user-manager-mobile-card__section">
                                                <span class="backend-table-card__label user-manager-mobile-card__label">Actions</span>
                                                <div class="backend-table-actions user-manager-row-actions">
                                                    @if (!$user->email_verified_at)
                                                        <form action="{{ route('verified-user', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('put')
                                                            <input type="hidden" name="verified" value="{{ $now->toDateTimeString() }}">
                                                            <button type="submit" class="user-manager-icon-action" title="Verify email">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <button type="button" class="user-manager-icon-action" data-toggle="modal" data-target="#user-view-{{ $user->id }}" title="View user">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="user-manager-icon-action" data-toggle="modal" data-target="#user-edit-{{ $user->id }}" title="Edit user">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('remove-user', $user->id) }}" method="POST" data-confirm-delete="Remove {{ $user->name }}? If related records exist, the account will be blocked instead.">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="user-manager-icon-action is-danger" title="Remove user">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="backend-table-empty user-manager-empty">
                                            <i class="fa fa-users"></i>
                                            <strong>No users found</strong>
                                            <span>Try adjusting the filters or add a new user.</span>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="user-manager-pagination">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>

                        <aside class="user-manager-side">
                            <section class="backend-panel user-manager-panel user-manager-panel--compact">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Review Queue</span>
                                        <h2>Pending Work</h2>
                                    </div>
                                </div>
                                <div class="user-manager-review-list">
                                    <div>
                                        <strong>{{ number_format($summary['pendingApproval']) }}</strong>
                                        <span>Approval pending</span>
                                    </div>
                                    <div>
                                        <strong>{{ number_format($summary['blocked']) }}</strong>
                                        <span>Blocked accounts</span>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel user-manager-panel user-manager-panel--compact">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Activity</span>
                                        <h2>Recent Notifications</h2>
                                    </div>
                                </div>
                                <div class="user-manager-notes">
                                    @forelse ($notifications as $notification)
                                        <article>
                                            <strong>{{ $notification->data['title'] ?? 'New Agent' }}</strong>
                                            <p>{{ $notification->data['message'] ?? 'A new notification is available.' }}</p>
                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                        </article>
                                    @empty
                                        <article>
                                            <strong>No recent notifications</strong>
                                            <p>Registration and agent updates will appear here.</p>
                                        </article>
                                    @endforelse
                                </div>
                            </section>
                        </aside>
                    </section>

                    @foreach ($users as $user)
                        @php
                            $activity = $activityLabel($user);
                        @endphp
                        <div class="modal fade user-manager-modal" id="user-view-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="user-manager-modal__header">
                                        <div>
                                            <span>User Detail</span>
                                            <h3>{{ $user->name }}</h3>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="user-manager-modal__body">
                                        <div class="user-manager-profile">
                                            <img src="{{ $profileImage($user) }}" alt="{{ $user->name }}">
                                            <div>
                                                <span class="backend-status-badge {{ $user->status === 'Block' ? 'backend-status-badge--danger' : 'backend-status-badge--success' }} user-manager-badge {{ $user->status === 'Block' ? 'is-danger' : 'is-success' }}">{{ $statusLabel($user->status) }}</span>
                                                <h4>{{ $user->name }}</h4>
                                                <p>{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <dl class="user-manager-detail-grid">
                                            <div><dt>Username</dt><dd>{{ $user->username }}</dd></div>
                                            <div><dt>Code</dt><dd>{{ $user->code ?: '-' }}</dd></div>
                                            <div><dt>Type</dt><dd>{{ $types[$user->type] ?? ucfirst((string) $user->type) }}</dd></div>
                                            <div><dt>Group</dt><dd>{{ $positions[$user->position] ?? ucfirst((string) $user->position) }}</dd></div>
                                            <div><dt>Phone</dt><dd>{{ $user->phone ?: '-' }}</dd></div>
                                            <div><dt>Office</dt><dd>{{ $user->office ?: '-' }}</dd></div>
                                            <div><dt>Country</dt><dd>{{ $user->country ?: '-' }}</dd></div>
                                            <div><dt>Approval</dt><dd>{{ $user->is_approved ? 'Approved' : 'Pending' }}</dd></div>
                                            <div><dt>Email</dt><dd>{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</dd></div>
                                            <div><dt>Activity</dt><dd>{{ $activity['label'] }}</dd></div>
                                            <div class="is-wide"><dt>Address</dt><dd>{{ $user->address ?: '-' }}</dd></div>
                                            <div class="is-wide"><dt>Comment</dt><dd>{{ $user->comment ?: '-' }}</dd></div>
                                        </dl>
                                    </div>
                                    <div class="user-manager-modal__footer">
                                        @if ($user->status === 'Active' && !$user->is_approved)
                                            <form action="{{ route('approve-user', $user->id) }}" method="POST">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="backend-button backend-button-primary">
                                                    <i class="fa fa-check"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade user-manager-modal" id="user-edit-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="user-manager-modal__header">
                                        <div>
                                            <span>Edit Access</span>
                                            <h3>{{ $user->name }}</h3>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="update-user-{{ $user->id }}" action="{{ route('edit-user', $user->id) }}" method="POST">
                                        @csrf
                                        @method('put')
                                        <div class="user-manager-modal__body">
                                            @include('backend.admin.users.partials.manager-form', [
                                                'mode' => 'edit',
                                                'user' => $user,
                                                'positions' => $positions,
                                                'statuses' => $statuses,
                                                'types' => $types,
                                            ])
                                        </div>
                                        <div class="user-manager-modal__footer">
                                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="backend-button backend-button-primary">
                                                <i class="fa fa-check"></i>
                                                Save User
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="modal fade user-manager-modal" id="user-add" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="user-manager-modal__header">
                                    <div>
                                        <span>New Access</span>
                                        <h3>Add User</h3>
                                    </div>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="add-user" method="POST" action="{{ route('create-user') }}">
                                    @csrf
                                    <div class="user-manager-modal__body">
                                        @include('backend.admin.users.partials.manager-form', [
                                            'mode' => 'create',
                                            'user' => null,
                                            'positions' => $positions,
                                            'statuses' => $statuses,
                                            'types' => $types,
                                        ])
                                        <p class="user-manager-help">Default password is set to 1234567890. Ask the user to change it after first login.</p>
                                    </div>
                                    <div class="user-manager-modal__footer">
                                        <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-check"></i>
                                            Add User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
