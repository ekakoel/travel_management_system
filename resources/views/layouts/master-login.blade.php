<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bali Kami Tour') }} | @yield('title')</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/balikami/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/balikami/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/balikami/favicon-16x16.png') }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ mix('build/frontend/css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/auth-entry.css') }}">
    @stack('styles')
</head>
<body class="auth-body">
    @php
        $authLocaleOptions = [
            'en' => 'English',
            'zh' => '繁體中文',
            'zh-CN' => '简体中文',
        ];
        $currentAuthLocale = app()->getLocale();
    @endphp
    <nav class="auth-language-switch" aria-label="Language switcher">
        <span class="auth-language-switch__label">
            <i class="fa fa-language" aria-hidden="true"></i>
            Language
        </span>
        <div class="auth-language-switch__options">
            @foreach ($authLocaleOptions as $locale => $label)
                <a href="{{ language_switch_url($locale) }}" class="{{ $currentAuthLocale === $locale ? 'is-active' : '' }}"
                    hreflang="{{ $locale }}" lang="{{ str_replace('_', '-', $locale) }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>
    <div id="app" class="auth-app">
        @yield('content')
    </div>
    @include('sweetalert::alert')
    <script src="{{ mix('build/frontend/js/app.js') }}" defer></script>
    <script src="{{ mix('build/frontend/js/pages/auth.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
