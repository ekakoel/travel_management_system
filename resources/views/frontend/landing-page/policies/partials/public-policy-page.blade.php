@php
    $policyGroups = $policyGroups ?? collect();
    $summaryItems = $summaryItems ?? [];
    $opening = $opening ?? null;
    $emptyMessage = $emptyMessage ?? __('messages.No active policy content is available yet.');
    $policyLogo = config('app.logo_img_color');
    $policyLogoAlt = config('app.alt_logo', 'Logo Bali Kami Tour');
    $policyItemCount = $policyGroups->sum(fn ($group) => $group['items']->count());
    $navItems = [
        [
            'key' => 'terms',
            'label' => __('messages.Terms and Conditions'),
            'route' => route('terms-and-conditions'),
        ],
        [
            'key' => 'privacy',
            'label' => __('messages.Privacy Policy'),
            'route' => route('privacy-policy'),
        ],
        [
            'key' => 'faq',
            'label' => __('messages.FAQs'),
            'route' => route('faq'),
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }} | {{ config('app.name', 'Bali Kami Tour') }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/balikami/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/balikami/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/balikami/favicon-16x16.png') }}">
    @include('frontend.layouts.frontend-head-assets')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/public-policy-entry.css') }}">
</head>
<body class="public-policy-body">
    <main class="frontend-page-shell public-policy-page public-policy-page--{{ $pageKey }}">
        <section class="frontend-page-topband public-policy-hero">
            <div class="container">
                <nav class="public-policy-nav" aria-label="@lang('messages.Public policy navigation')">
                    <a class="public-policy-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Bali Kami Tour') }}">
                        <img src="{{ asset('storage/logo/' . $policyLogo) }}" alt="{{ $policyLogoAlt }}">
                    </a>
                    <div class="public-policy-nav__links">
                        <a href="{{ url('/') }}">@lang('messages.Home')</a>
                        @foreach ($navItems as $item)
                            <a class="{{ $pageKey === $item['key'] ? 'is-active' : '' }}" href="{{ $item['route'] }}">{{ $item['label'] }}</a>
                        @endforeach
                    </div>
                </nav>

                <div class="public-policy-breadcrumb">
                    @include('partials.breadcrumbs', [
                        'breadcrumbs' => [
                            ['label' => __('messages.Home'), 'url' => url('/')],
                            ['label' => $pageTitle],
                        ],
                    ])
                </div>

                <div class="public-policy-hero__grid">
                    <div class="public-policy-hero__copy">
                        <span class="public-policy-kicker">{{ $pageEyebrow }}</span>
                        <h1>{{ $pageTitle }}</h1>
                        <p>{{ $pageDescription }}</p>
                        <div class="public-policy-meta-strip" aria-label="@lang('messages.Policy page summary')">
                            <span>{{ trans_choice('messages.:count active policy sections', $policyGroups->count(), ['count' => $policyGroups->count()]) }}</span>
                            <span>{{ trans_choice('messages.:count published policy items', $policyItemCount, ['count' => $policyItemCount]) }}</span>
                            <span>@lang('messages.Public access')</span>
                        </div>
                    </div>
                    <aside class="public-policy-hero__card" aria-label="@lang('messages.Policy highlights')">
                        <span>@lang('messages.Policy highlights')</span>
                        @foreach ($summaryItems as $index => $item)
                            <strong><i>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</i>{{ $item }}</strong>
                        @endforeach
                    </aside>
                </div>
            </div>
        </section>

        <section class="public-policy-content">
            <div class="container">
                <div class="public-policy-section-heading">
                    <span>@lang('messages.Active policy directory')</span>
                    <h2>@lang('messages.Review the latest published policy content')</h2>
                    <p>@lang('messages.This page is generated from backend-managed policy records, so active updates can be published without editing frontend templates.')</p>
                </div>
                <div class="public-policy-layout">
                    <aside class="public-policy-sidebar">
                        <div class="public-policy-sidebar__panel">
                            <span>@lang('messages.On this page')</span>
                            @forelse ($policyGroups as $group)
                                <a href="#policy-{{ \Illuminate\Support\Str::slug($group['type']) }}">{{ $group['title'] }}</a>
                            @empty
                                <em>{{ $emptyMessage }}</em>
                            @endforelse
                        </div>
                    </aside>

                    <div class="public-policy-main">
                        @if ($opening)
                            <article class="public-policy-card public-policy-card--opening">
                                <p>{{ $opening }}</p>
                            </article>
                        @endif

                        @forelse ($policyGroups as $group)
                            <section class="public-policy-group" id="policy-{{ \Illuminate\Support\Str::slug($group['type']) }}">
                                <div class="public-policy-group__header">
                                    <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h2>{{ $group['title'] }}</h2>
                                </div>
                                <div class="public-policy-stack">
                                    @foreach ($group['items'] as $policy)
                                        <article class="public-policy-card">
                                            @if ($policy['title'])
                                                <h3>{{ $policy['title'] }}</h3>
                                            @endif
                                            @if ($policy['content'])
                                                <div class="public-policy-richtext">{!! $policy['content'] !!}</div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @empty
                            <article class="public-policy-card public-policy-empty">
                                <h2>{{ $emptyMessage }}</h2>
                                <p>@lang('messages.Please check back later or contact our team for assistance.')</p>
                            </article>
                        @endforelse

                        <div class="public-policy-signoff">
                            <span>Denpasar, 01 January 2023</span>
                            <strong>{{ config('app.business') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('frontend.layouts.footer-modern')
    </main>
    <script src="{{ mix('build/frontend/js/app.js') }}" defer></script>
</body>
</html>
