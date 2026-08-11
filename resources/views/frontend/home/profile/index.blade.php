@extends('frontend.layouts.app')

@section('title', __('messages.Profile'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/profile-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/profile.js') }}" defer></script>
@endpush

@section('content')
    @php
        $user = $profileUser ?? Auth::user();
        $profileUpdateBag = $errors->getBag('profileUpdate');
        $profilePhotoBag = $errors->getBag('profilePhoto');
        $profilePasswordBag = $errors->getBag('profilePassword');
        $allProfileErrors = collect($profileUpdateBag->all())
            ->merge($profilePhotoBag->all())
            ->merge($profilePasswordBag->all())
            ->unique()
            ->values();
        $autoOpenModal = $profileUpdateBag->isNotEmpty()
            ? 'profileEditModal'
            : ($profilePhotoBag->isNotEmpty()
                ? 'profilePictureModal'
                : ($profilePasswordBag->isNotEmpty() ? 'profilePasswordModal' : ''));
        $contactPlatformMap = collect($contactChannelPlatformOptions)->keyBy('value');
        $formContactChannels = collect(old('contact_channels', $user->normalized_contact_channels))
            ->filter(fn ($channel) => is_array($channel))
            ->values();
    @endphp

    <svg class="profile-icon-sprite" aria-hidden="true" focusable="false">
        <symbol id="profile-icon-whatsapp" viewBox="0 0 24 24"><path fill="currentColor" d="M19.05 4.91A9.82 9.82 0 0 0 12.03 2C6.61 2 2.2 6.41 2.2 11.83c0 1.74.45 3.44 1.31 4.95L2 22l5.38-1.42a9.78 9.78 0 0 0 4.65 1.18h.01c5.42 0 9.83-4.41 9.83-9.83c0-2.63-1.02-5.1-2.82-7.02ZM12.04 20.1h-.01a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-3.19.84.85-3.11-.2-.32a8.11 8.11 0 0 1-1.25-4.37c0-4.49 3.66-8.15 8.16-8.15c2.18 0 4.22.85 5.75 2.39a8.08 8.08 0 0 1 2.37 5.76c0 4.5-3.66 8.16-8.15 8.16Zm4.47-6.12c-.24-.12-1.42-.7-1.64-.78c-.22-.08-.38-.12-.54.12c-.16.24-.62.77-.76.93c-.14.16-.28.18-.52.06c-.24-.12-1.01-.37-1.92-1.19c-.71-.63-1.19-1.41-1.33-1.65c-.14-.24-.01-.37.11-.49c.11-.11.24-.28.36-.42c.12-.14.16-.24.24-.4c.08-.16.04-.3-.02-.42c-.06-.12-.54-1.31-.74-1.8c-.2-.47-.4-.41-.54-.42l-.46-.01c-.16 0-.42.06-.64.3c-.22.24-.84.82-.84 2.01c0 1.18.86 2.32.98 2.48c.12.16 1.68 2.56 4.07 3.59c.57.25 1.02.4 1.37.51c.58.18 1.11.15 1.53.09c.47-.07 1.42-.58 1.62-1.15c.2-.57.2-1.06.14-1.16c-.06-.1-.22-.16-.46-.28Z"/></symbol>
        <symbol id="profile-icon-wechat" viewBox="0 0 24 24"><path fill="currentColor" d="M9.33 3.5c-3.94 0-7.13 2.57-7.13 5.74c0 1.82 1.05 3.44 2.68 4.49l-.63 2.34l2.64-1.31c.79.18 1.6.28 2.44.28l.5-.01a5.86 5.86 0 0 1-.5-2.34c0-3.17 3.19-5.74 7.13-5.74c.17 0 .34.01.51.02c-.93-2.01-3.57-3.47-6.64-3.47Zm-2.4 4.31a.78.78 0 1 1 0 1.56a.78.78 0 0 1 0-1.56Zm4.81 0a.78.78 0 1 1 0 1.56a.78.78 0 0 1 0-1.56Zm4.39.78c-3.13 0-5.67 2.03-5.67 4.54s2.54 4.54 5.67 4.54c.67 0 1.32-.09 1.93-.27l2.09 1.04l-.49-1.81c1.29-.83 2.13-2.09 2.13-3.5c0-2.51-2.54-4.54-5.66-4.54Zm-2.13 3.19a.62.62 0 1 1 0 1.24a.62.62 0 0 1 0-1.24Zm4.26 0a.62.62 0 1 1 0 1.24a.62.62 0 0 1 0-1.24Z"/></symbol>
        <symbol id="profile-icon-line" viewBox="0 0 24 24"><path fill="currentColor" d="M20.64 10.1c0-4.26-3.86-7.72-8.6-7.72s-8.6 3.46-8.6 7.72c0 3.82 3.06 7.01 7.2 7.62c.28.06.66.18.76.4c.09.2.06.51.03.72l-.12.74c-.03.22-.16.86.75.47c.9-.38 4.84-2.85 6.6-4.88h-.01c1.22-1.33 1.99-3 1.99-5.07Zm-11.8 1.95H7.12a.25.25 0 0 1-.25-.25V8.27c0-.14.11-.25.25-.25h.42c.14 0 .25.11.25.25v3.11h1.05c.14 0 .25.11.25.25v.42c0 .14-.11.25-.25.25Zm1.84-.25a.25.25 0 0 1-.25.25H10a.25.25 0 0 1-.25-.25V8.27c0-.14.11-.25.25-.25h.43c.14 0 .25.11.25.25v3.53Zm4.1 0a.25.25 0 0 1-.25.25h-.42a.25.25 0 0 1-.2-.1l-1.68-2.28v2.13c0 .14-.11.25-.25.25h-.43a.25.25 0 0 1-.25-.25V8.27c0-.14.11-.25.25-.25h.42c.08 0 .15.04.2.1l1.68 2.28V8.27c0-.14.11-.25.25-.25h.43c.14 0 .25.11.25.25v3.53Zm2.85-3.11h-1.05v.6h1.05c.14 0 .25.11.25.25v.42c0 .14-.11.25-.25.25h-1.05v.6h1.05c.14 0 .25.11.25.25v.42c0 .14-.11.25-.25.25h-1.73a.25.25 0 0 1-.25-.25V8.27c0-.14.11-.25.25-.25h1.73c.14 0 .25.11.25.25v.42c0 .14-.11.25-.25.25Z"/></symbol>
        <symbol id="profile-icon-telegram" viewBox="0 0 24 24"><path fill="currentColor" d="M21.49 4.48a1.5 1.5 0 0 0-1.63-.22L3.84 11.02c-.69.29-.65 1.29.07 1.52l3.95 1.25l1.45 4.45c.19.59.94.78 1.4.35l2.2-2.07l4.32 3.18c.54.4 1.31.11 1.45-.53l2.89-13.26c.12-.54-.16-1.08-.68-1.43ZM9.9 13.27l-.56 3.63l-1.03-3.15l8.8-6.3l-7.21 5.82Zm1.18 2.37l.27-1.75l1.26.92l-1.53.83Z"/></symbol>
        <symbol id="profile-icon-skype" viewBox="0 0 24 24"><path fill="currentColor" d="M18.87 13.41c.06-.45.09-.92.09-1.41c0-4.95-4.01-8.96-8.96-8.96c-.49 0-.96.04-1.42.11A4.66 4.66 0 0 0 6.2 2.5A4.7 4.7 0 0 0 1.5 7.2c0 .85.23 1.65.62 2.34A8.93 8.93 0 0 0 2 11c0 4.95 4.01 8.96 8.96 8.96c.5 0 .99-.04 1.47-.12c.69.39 1.49.61 2.34.61a4.7 4.7 0 0 0 4.69-4.69c0-.86-.24-1.67-.66-2.35Zm-8.06 3.1c-2.12 0-3.53-1.04-3.64-2.72c-.02-.19.13-.36.33-.36h1.22c.17 0 .31.12.35.28c.17.75.78 1.13 1.81 1.13c1 0 1.61-.4 1.61-1.04c0-.52-.33-.83-1.22-1.05l-1.27-.32c-1.79-.45-2.62-1.31-2.62-2.72c0-1.67 1.47-2.86 3.54-2.86c1.99 0 3.36 1.05 3.47 2.66c.01.19-.13.35-.32.35h-1.16c-.16 0-.3-.11-.35-.26c-.18-.59-.7-.93-1.55-.93c-.88 0-1.46.42-1.46 1c0 .48.36.77 1.3 1l1.18.29c1.9.46 2.65 1.26 2.65 2.73c0 1.83-1.49 2.82-3.87 2.82Z"/></symbol>
        <symbol id="profile-icon-facebook" viewBox="0 0 24 24"><path fill="currentColor" d="M13.5 21v-7.03h2.36l.35-2.74H13.5v-1.75c0-.79.22-1.33 1.36-1.33h1.45V5.7c-.25-.03-1.11-.1-2.11-.1c-2.09 0-3.53 1.27-3.53 3.61v2.02H8.3v2.74h2.37V21h2.83Z"/></symbol>
        <symbol id="profile-icon-instagram" viewBox="0 0 24 24"><path fill="currentColor" d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.8A3.95 3.95 0 0 0 3.8 7.75v8.5a3.95 3.95 0 0 0 3.95 3.95h8.5a3.95 3.95 0 0 0 3.95-3.95v-8.5a3.95 3.95 0 0 0-3.95-3.95h-8.5Zm8.97 1.35a1.08 1.08 0 1 1 0 2.16a1.08 1.08 0 0 1 0-2.16ZM12 6.86A5.14 5.14 0 1 1 6.86 12A5.15 5.15 0 0 1 12 6.86Zm0 1.8A3.34 3.34 0 1 0 15.34 12A3.34 3.34 0 0 0 12 8.66Z"/></symbol>
        <symbol id="profile-icon-linkedin" viewBox="0 0 24 24"><path fill="currentColor" d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A1.97 1.97 0 1 0 5.3 6.94A1.97 1.97 0 0 0 5.25 3ZM20.44 12.62c0-3.46-1.85-5.07-4.31-5.07c-1.99 0-2.88 1.09-3.38 1.86V8.5H9.38c.04.6 0 11.5 0 11.5h3.37v-6.42c0-.34.02-.68.13-.93c.27-.68.88-1.38 1.91-1.38c1.35 0 1.89 1.03 1.89 2.54V20h3.37v-7.38Z"/></symbol>
        <symbol id="profile-icon-chat" viewBox="0 0 24 24"><path fill="currentColor" d="M12 3c-5.25 0-9.5 3.72-9.5 8.3c0 2.39 1.17 4.53 3.05 6.04L4.5 21l4.01-2.2c1.08.34 2.25.5 3.49.5c5.25 0 9.5-3.72 9.5-8.3S17.25 3 12 3Zm-4.2 8.94a1.14 1.14 0 1 1 0-2.28a1.14 1.14 0 0 1 0 2.28Zm4.2 0a1.14 1.14 0 1 1 0-2.28a1.14 1.14 0 0 1 0 2.28Zm4.2 0a1.14 1.14 0 1 1 0-2.28a1.14 1.14 0 0 1 0 2.28Z"/></symbol>
        <symbol id="profile-icon-remove" viewBox="0 0 24 24"><path fill="currentColor" d="M6.7 5.3a1 1 0 0 0-1.4 1.4L10.59 12l-5.3 5.3a1 1 0 1 0 1.42 1.4L12 13.41l5.3 5.3a1 1 0 0 0 1.4-1.42L13.41 12l5.3-5.3a1 1 0 0 0-1.42-1.4L12 10.59L6.7 5.3Z"/></symbol>
    </svg>

    <main
        class="frontend-page-shell profile-page"
        data-profile-page
        data-contact-platforms='@json($contactChannelPlatformOptions)'
        data-contact-default-placeholder="@lang('messages.Example: profile link, username, or direct number')"
        @if($autoOpenModal) data-profile-open-modal="{{ $autoOpenModal }}" @endif
    >
        <section class="container-fluid frontend-page-topband profile-topband py-5">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['label' => __('messages.Profile')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="frontend-page-intro profile-hero">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('messages.Manage your international partner profile')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Keep your business identity, contact channels, language preference, and location data accurate so our reservation and operations teams can coordinate your bookings professionally.')
                        </p>
                    </div>

                    <div class="frontend-page-summary profile-hero__summary">
                        @foreach ($heroStats as $stat)
                            <div class="frontend-page-summary__item">
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ $stat['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="profile-section profile-section--alerts">
            <div class="container">
                @if ($allProfileErrors->isNotEmpty() || session('success') || session('status') || session('error'))
                    <div class="profile-alerts">
                        @if ($allProfileErrors->isNotEmpty())
                            <div class="profile-alert profile-alert--danger">
                                <strong>@lang('messages.Please review the highlighted fields.')</strong>
                                <ul>
                                    @foreach ($allProfileErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('success') || session('status'))
                            <div class="profile-alert profile-alert--success">
                                {{ session('success') ?? session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="profile-alert profile-alert--danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($user->status === 'Block')
                    <div class="profile-alert profile-alert--danger">
                        @lang('messages.Your account has been disabled because it does not comply with the established terms.')
                    </div>
                @elseif ($missingFields->isNotEmpty())
                    <div class="profile-alert profile-alert--warning">
                        @lang('messages.Please make sure your email is available before continuing to protected booking workflows.')
                    </div>
                @elseif ((int) $user->is_approved === 0)
                    <div class="profile-alert profile-alert--warning">
                        @lang('messages.Your account is in the approval process, please wait for 2 x 24 hours for approval! Thank you.')
                    </div>
                @endif
            </div>
        </section>

        <section class="profile-section profile-section--main">
            <div class="container">
                @if ($user->status !== 'Block')
                    <div class="profile-action-bar">
                        <button type="button" class="btn btn-primary profile-action-bar__primary" data-bs-toggle="modal" data-bs-target="#profileEditModal">
                            @lang('messages.Edit Profile')
                        </button>
                        <button type="button" class="profile-action-bar__secondary" data-bs-toggle="modal" data-bs-target="#profilePasswordModal">
                            @lang('messages.Change Password')
                        </button>
                    </div>
                @endif

                <article class="profile-single-card">
                    <div class="profile-single-card__identity">
                        <div class="profile-avatar">
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name ?: $user->username }}">
                            @if ($user->status !== 'Block')
                                <button type="button" class="profile-avatar__edit" data-bs-toggle="modal" data-bs-target="#profilePictureModal" aria-label="@lang('messages.Change Profile Picture')">
                                    <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                </button>
                            @endif
                        </div>

                        <div class="profile-identity">
                            <h2>{{ $user->name ?: $user->username }}</h2>
                            <p>{{ $user->job_title ?: __('messages.Pending Update') }}</p>
                            <span class="profile-status {{ $accountStatusClass }}">{{ $accountStatus }}</span>
                        </div>

                        <div class="profile-progress-inline">
                            <div class="profile-progress__head">
                                <span>@lang('messages.Booking access email')</span>
                                <strong>{{ $completionRate }}%</strong>
                            </div>
                            <div class="profile-progress__bar" aria-hidden="true">
                                <span style="width: {{ $completionRate }}%"></span>
                            </div>
                            @if ($missingFields->isNotEmpty())
                                <p>@lang('messages.Missing information'): {{ $missingFields->join(', ') }}</p>
                            @else
                                <p>@lang('messages.Your email requirement is complete and your account can continue through reservation workflows once approval is active.')</p>
                            @endif
                        </div>
                    </div>

                    <div class="profile-single-card__content">
                        <div class="profile-data-grid">
                            <div class="profile-data-row profile-data-row--wide">
                                <span>@lang('messages.Primary Contact')</span>
                                <div class="profile-contact-stack">
                                    <div class="profile-contact-item">
                                        <small>@lang('messages.Email')</small>
                                        <strong>{{ $user->email ?: '-' }}</strong>
                                    </div>
                                    <div class="profile-contact-item">
                                        <small>@lang('messages.Phone')</small>
                                        <strong>{{ $user->phone ?: '-' }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if (count($contactChannels) > 0)
                                <div class="profile-data-row profile-data-row--wide">
                                    <span>@lang('messages.Social & Chat Channels')</span>
                                    <div class="profile-social-display">
                                        @foreach ($contactChannels as $channel)
                                            <div class="profile-social-display__item">
                                                <span class="profile-social-display__icon">
                                                    <svg aria-hidden="true"><use href="#profile-icon-{{ $channel['icon'] }}"></use></svg>
                                                </span>
                                                <div>
                                                    <small>{{ $channel['label'] }}</small>
                                                    @if ($channel['href'])
                                                        <a href="{{ $channel['href'] }}" target="_blank" rel="noopener noreferrer">{{ $channel['value'] }}</a>
                                                    @else
                                                        <strong>{{ $channel['value'] }}</strong>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="profile-data-row">
                                <span>@lang('messages.Company / Office')</span>
                                <strong>{{ $user->office ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Legal Company Name')</span>
                                <strong>{{ $user->company_legal_name ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Job Title')</span>
                                <strong>{{ $user->job_title ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Website')</span>
                                @if ($user->website)
                                    <a href="{{ str_starts_with($user->website, 'http') ? $user->website : 'https://' . $user->website }}" target="_blank" rel="noopener noreferrer">{{ $user->website }}</a>
                                @else
                                    <strong>-</strong>
                                @endif
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Preferred Language')</span>
                                <strong>{{ $languageOptions[$user->preferred_language ?? 'en'] ?? strtoupper((string) $user->preferred_language) }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Time Zone')</span>
                                <strong>{{ $user->timezone ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Country')</span>
                                <strong>{{ $user->country ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.City')</span>
                                <strong>{{ $user->city ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.State / Region')</span>
                                <strong>{{ $user->state_region ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Postal Code')</span>
                                <strong>{{ $user->postal_code ?: '-' }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Partner Code')</span>
                                <strong>{{ $user->code ?: __('messages.Not Assigned') }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Account Approval')</span>
                                <strong>{{ $accountStatus }}</strong>
                            </div>
                            <div class="profile-data-row">
                                <span>@lang('messages.Email Verification')</span>
                                <strong>
                                    {{ $user->email_verified_at ? __('messages.Verified on :date', ['date' => $user->email_verified_at->format('Y-m-d')]) : __('messages.Pending Verification') }}
                                </strong>
                            </div>
                            <div class="profile-data-row profile-data-row--wide">
                                <span>@lang('messages.Address')</span>
                                <strong>{{ $user->address ?: '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </main>

    @if ($user->status !== 'Block')
        <div class="modal fade profile-modal" id="profileEditModal" tabindex="-1" role="dialog" aria-labelledby="profileEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="profile-edit-form" action="/fupdate-profile/{{ $user->id }}" method="post" data-profile-submit-form>
                        @csrf
                        @method('put')
                        <div class="profile-modal__header">
                            <div>
                                <span>@lang('messages.Partner Account Center')</span>
                                <h3 id="profileEditModalLabel">@lang('messages.Update Profile')</h3>
                            </div>
                            <button type="button" class="profile-modal__close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">x</button>
                        </div>

                        <div class="profile-modal__body">
                            <div class="profile-form-note">
                                @lang('messages.Use complete business information so our team can verify your account, localize communication, and process bookings smoothly.')
                            </div>

                            <div class="profile-form-section">
                                <span class="profile-form-section__eyebrow">@lang('messages.Account Identity')</span>
                                <div class="profile-form-grid">
                                    <label>
                                        <span>@lang('messages.Username')</span>
                                        <input type="text" value="{{ $user->username }}" disabled>
                                    </label>
                                    <label>
                                        <span>@lang('messages.Email')</span>
                                        <input type="email" value="{{ $user->email }}" disabled>
                                    </label>
                                    <label>
                                        <span>@lang('messages.Name') <i>*</i></span>
                                        <input name="name" type="text" value="{{ old('name', $user->name) }}" class="@error('name', 'profileUpdate') is-invalid @enderror" required>
                                        @error('name', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Job Title') <i>*</i></span>
                                        <input name="job_title" type="text" value="{{ old('job_title', $user->job_title) }}" class="@error('job_title', 'profileUpdate') is-invalid @enderror" required>
                                        @error('job_title', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Preferred Language') <i>*</i></span>
                                        <select name="preferred_language" class="@error('preferred_language', 'profileUpdate') is-invalid @enderror" required>
                                            @foreach ($languageOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('preferred_language', $user->preferred_language ?: 'en') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('preferred_language', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Time Zone')</span>
                                        <select name="timezone" class="@error('timezone', 'profileUpdate') is-invalid @enderror">
                                            <option value="">@lang('messages.Select Time Zone')</option>
                                            @foreach ($timezoneOptions as $timezone)
                                                <option value="{{ $timezone }}" @selected(old('timezone', $user->timezone) === $timezone)>{{ $timezone }}</option>
                                            @endforeach
                                        </select>
                                        @error('timezone', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                </div>
                            </div>

                            <div class="profile-form-section">
                                <span class="profile-form-section__eyebrow">@lang('messages.Contact Details')</span>
                                <div class="profile-form-grid">
                                    <label>
                                        <span>@lang('messages.Phone') <i>*</i></span>
                                        <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="@error('phone', 'profileUpdate') is-invalid @enderror" required>
                                        @error('phone', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Website')</span>
                                        <input name="website" type="url" value="{{ old('website', $user->website) }}" class="@error('website', 'profileUpdate') is-invalid @enderror" placeholder="https://example.com">
                                        @error('website', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                </div>

                                <div class="profile-social-manager" data-social-manager>
                                    <div class="profile-social-manager__header">
                                        <div>
                                            <strong>@lang('messages.Social & Chat Channels')</strong>
                                            <p>@lang('messages.Add optional chat or social channels so our reservation team can reach you through your preferred platform.')</p>
                                        </div>
                                        <button type="button" class="profile-btn profile-btn--ghost profile-btn--small" data-add-social-channel>
                                            @lang('messages.Add Social Media')
                                        </button>
                                    </div>

                                    <div class="profile-social-manager__list" data-social-list>
                                        @foreach ($formContactChannels as $index => $channel)
                                            @php
                                                $selectedPlatform = $channel['platform'] ?? '';
                                                $platformMeta = $contactPlatformMap->get($selectedPlatform);
                                            @endphp
                                            <div class="profile-social-row" data-social-row>
                                                <div class="profile-social-row__icon">
                                                    <svg data-social-icon aria-hidden="true"><use href="#profile-icon-{{ $platformMeta['icon'] ?? 'chat' }}"></use></svg>
                                                </div>

                                                <label>
                                                    <select name="contact_channels[{{ $index }}][platform]" class="@error("contact_channels.$index.platform", 'profileUpdate') is-invalid @enderror" data-social-platform>
                                                        <option value="">@lang('messages.Select Social / Chat Platform')</option>
                                                        @foreach ($contactChannelPlatformOptions as $platform)
                                                            <option value="{{ $platform['value'] }}" data-icon="{{ $platform['icon'] }}" data-placeholder="{{ $platform['placeholder'] }}" @selected($selectedPlatform === $platform['value'])>
                                                                {{ $platform['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("contact_channels.$index.platform", 'profileUpdate')
                                                        <span class="profile-field-error">{{ $message }}</span>
                                                    @enderror
                                                </label>

                                                <label>
                                                    <input
                                                        name="contact_channels[{{ $index }}][value]"
                                                        type="text"
                                                        value="{{ $channel['value'] ?? '' }}"
                                                        placeholder="{{ $platformMeta['placeholder'] ?? __('messages.Example: profile link, username, or direct number') }}"
                                                        class="@error("contact_channels.$index.value", 'profileUpdate') is-invalid @enderror"
                                                        data-social-value
                                                    >
                                                    @error("contact_channels.$index.value", 'profileUpdate')
                                                        <span class="profile-field-error">{{ $message }}</span>
                                                    @enderror
                                                </label>

                                                <button type="button" class="profile-social-row__remove" data-remove-social-channel aria-label="@lang('messages.Remove')">
                                                    <svg aria-hidden="true"><use href="#profile-icon-remove"></use></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="profile-social-manager__empty @if ($formContactChannels->isNotEmpty()) is-hidden @endif" data-social-empty>
                                        @lang('messages.No additional social or chat channels added yet.')
                                    </div>

                                    <template data-social-template>
                                        <div class="profile-social-row" data-social-row>
                                            <div class="profile-social-row__icon">
                                                <svg data-social-icon aria-hidden="true"><use href="#profile-icon-chat"></use></svg>
                                            </div>

                                            <label>
                                                <select name="contact_channels[__INDEX__][platform]" data-social-platform>
                                                    <option value="">@lang('messages.Select Social / Chat Platform')</option>
                                                    @foreach ($contactChannelPlatformOptions as $platform)
                                                        <option value="{{ $platform['value'] }}" data-icon="{{ $platform['icon'] }}" data-placeholder="{{ $platform['placeholder'] }}">
                                                            {{ $platform['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>

                                            <label>
                                                <input
                                                    name="contact_channels[__INDEX__][value]"
                                                    type="text"
                                                    placeholder="@lang('messages.Example: profile link, username, or direct number')"
                                                    data-social-value
                                                >
                                            </label>

                                            <button type="button" class="profile-social-row__remove" data-remove-social-channel aria-label="@lang('messages.Remove')">
                                                <svg aria-hidden="true"><use href="#profile-icon-remove"></use></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="profile-form-section">
                                <span class="profile-form-section__eyebrow">@lang('messages.Business Identity')</span>
                                <div class="profile-form-grid">
                                    <label>
                                        <span>@lang('messages.Company / Office') <i>*</i></span>
                                        <input name="office" type="text" value="{{ old('office', $user->office) }}" class="@error('office', 'profileUpdate') is-invalid @enderror" required>
                                        @error('office', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Legal Company Name')</span>
                                        <input name="company_legal_name" type="text" value="{{ old('company_legal_name', $user->company_legal_name) }}" class="@error('company_legal_name', 'profileUpdate') is-invalid @enderror">
                                        @error('company_legal_name', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label class="is-wide">
                                        <span>@lang('messages.Company Registration Number')</span>
                                        <input name="company_registration_number" type="text" value="{{ old('company_registration_number', $user->company_registration_number) }}" class="@error('company_registration_number', 'profileUpdate') is-invalid @enderror">
                                        @error('company_registration_number', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                </div>
                            </div>

                            <div class="profile-form-section">
                                <span class="profile-form-section__eyebrow">@lang('messages.Location')</span>
                                <div class="profile-form-grid">
                                    <label class="is-wide">
                                        <span>@lang('messages.Address') <i>*</i></span>
                                        <textarea name="address" rows="3" class="@error('address', 'profileUpdate') is-invalid @enderror" required>{{ old('address', $user->address) }}</textarea>
                                        @error('address', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.City') <i>*</i></span>
                                        <input name="city" type="text" value="{{ old('city', $user->city) }}" class="@error('city', 'profileUpdate') is-invalid @enderror" required>
                                        @error('city', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.State / Region')</span>
                                        <input name="state_region" type="text" value="{{ old('state_region', $user->state_region) }}" class="@error('state_region', 'profileUpdate') is-invalid @enderror">
                                        @error('state_region', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Postal Code')</span>
                                        <input name="postal_code" type="text" value="{{ old('postal_code', $user->postal_code) }}" class="@error('postal_code', 'profileUpdate') is-invalid @enderror">
                                        @error('postal_code', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label>
                                        <span>@lang('messages.Country') <i>*</i></span>
                                        <select name="country" class="@error('country', 'profileUpdate') is-invalid @enderror" required>
                                            <option value="">@lang('messages.Select Country')</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country }}" @selected(old('country', $user->country) === $country)>{{ $country }}</option>
                                            @endforeach
                                        </select>
                                        @error('country', 'profileUpdate')
                                            <span class="profile-field-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="profile-modal__footer">
                            <button type="button" class="profile-btn profile-btn--ghost" data-bs-dismiss="modal">@lang('messages.Cancel')</button>
                            <button type="submit" class="profile-btn profile-btn--primary" data-submit-button data-default-label="@lang('messages.Update')" data-loading-label="@lang('messages.Processing...')">@lang('messages.Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade profile-modal" id="profilePictureModal" tabindex="-1" role="dialog" aria-labelledby="profilePictureModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="profile-picture-form" action="/fupdate-profileimg/{{ $user->id }}" method="post" enctype="multipart/form-data" data-profile-submit-form>
                        @csrf
                        @method('put')
                        <div class="profile-modal__header">
                            <div>
                                <span>@lang('messages.Profile Image')</span>
                                <h3 id="profilePictureModalLabel">@lang('messages.Change Profile Picture')</h3>
                            </div>
                            <button type="button" class="profile-modal__close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">x</button>
                        </div>
                        <div class="profile-modal__body">
                            <div class="profile-picture-upload">
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" data-profile-preview-image>
                                <div>
                                    <p>@lang('messages.Upload a clear square image for better recognition by our reservation team.')</p>
                                    <input type="file" name="profileimg" id="profileimg" accept="image/png,image/jpeg,image/webp" class="@error('profileimg', 'profilePhoto') is-invalid @enderror" data-profile-preview-input required>
                                    @error('profileimg', 'profilePhoto')
                                        <span class="profile-field-error">{{ $message }}</span>
                                    @enderror
                                    <small>@lang('messages.Accepted formats: JPG, PNG, or WEBP. Maximum size: 2 MB.')</small>
                                </div>
                            </div>
                        </div>
                        <div class="profile-modal__footer">
                            <button type="button" class="profile-btn profile-btn--ghost" data-bs-dismiss="modal">@lang('messages.Cancel')</button>
                            <button type="submit" class="profile-btn profile-btn--primary" data-submit-button data-default-label="@lang('messages.Change')" data-loading-label="@lang('messages.Uploading...')">@lang('messages.Change')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade profile-modal" id="profilePasswordModal" tabindex="-1" role="dialog" aria-labelledby="profilePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="profile-password-form" action="{{ route('update-password') }}" method="post" data-profile-submit-form>
                        @csrf
                        @method('put')
                        <div class="profile-modal__header">
                            <div>
                                <span>@lang('messages.Account Security')</span>
                                <h3 id="profilePasswordModalLabel">@lang('messages.Change Password')</h3>
                            </div>
                            <button type="button" class="profile-modal__close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">x</button>
                        </div>
                        <div class="profile-modal__body">
                            <div class="profile-form-grid">
                                <label class="is-wide">
                                    <span>@lang('messages.Old Password') <i>*</i></span>
                                    <input name="old_password" type="password" class="@error('old_password', 'profilePassword') is-invalid @enderror" required>
                                    @error('old_password', 'profilePassword')
                                        <span class="profile-field-error">{{ $message }}</span>
                                    @enderror
                                </label>
                                <label>
                                    <span>@lang('messages.New Password') <i>*</i></span>
                                    <input name="new_password" type="password" class="@error('new_password', 'profilePassword') is-invalid @enderror" minlength="8" required>
                                    @error('new_password', 'profilePassword')
                                        <span class="profile-field-error">{{ $message }}</span>
                                    @enderror
                                </label>
                                <label>
                                    <span>@lang('messages.Confirm New Password') <i>*</i></span>
                                    <input name="new_password_confirmation" type="password" class="@error('new_password_confirmation', 'profilePassword') is-invalid @enderror" minlength="8" required>
                                    @error('new_password_confirmation', 'profilePassword')
                                        <span class="profile-field-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                        <div class="profile-modal__footer">
                            <button type="button" class="profile-btn profile-btn--ghost" data-bs-dismiss="modal">@lang('messages.Cancel')</button>
                            <button type="submit" class="profile-btn profile-btn--primary" data-submit-button data-default-label="@lang('messages.Change')" data-loading-label="@lang('messages.Processing...')">@lang('messages.Change')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
