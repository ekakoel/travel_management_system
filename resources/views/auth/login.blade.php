@extends('layouts.master-login')
@section('title', __('messages.Login'))
@section('content')
<main class="auth-shell">
	<section class="auth-panel" aria-label="@lang('messages.Secure partner access')">
		<aside class="auth-panel__story">
			<a class="auth-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Bali Kami Tour') }}">
				<img src="{{ asset('storage/logo/' . config('app.logo_img_color')) }}" alt="{{ config('app.name', 'Bali Kami Tour') }}">
				<strong>{{ config('app.name', 'Bali Kami Tour') }}</strong>
			</a>
			<div class="auth-story-copy">
				<span class="auth-story-copy__eyebrow">@lang('messages.Secure partner access')</span>
				<h1>@lang('messages.Welcome back to Bali Kami Tour')</h1>
				<p>@lang('messages.Sign in to manage reservations, orders, rates, and operational workflows from one protected workspace.')</p>
			</div>
			<ul class="auth-trust-list" aria-label="@lang('messages.Security note')">
				<li><i class="fa fa-shield-halved" aria-hidden="true"></i>@lang('messages.Verified agent and operation access')</li>
				<li><i class="fa fa-route" aria-hidden="true"></i>@lang('messages.Role-aware dashboard routing')</li>
				<li><i class="fa fa-lock" aria-hidden="true"></i>@lang('messages.CSRF protected authentication')</li>
			</ul>
		</aside>
		<div class="auth-panel__form">
			<div class="auth-form-card">
				<span class="auth-form-card__eyebrow">@lang('messages.Login')</span>
				<h2>@lang('messages.Access your workspace')</h2>
				<p class="auth-form-card__lead">@lang('messages.Use your registered email and password to continue.')</p>

				@if (session('status'))
					<div class="alert alert-success auth-alert" role="alert">{{ session('status') }}</div>
				@endif
				@if (session('message'))
					<div class="alert alert-success auth-alert" role="alert">{{ session('message') }}</div>
				@endif

				<form method="POST" action="{{ route('login') }}" class="auth-form">
					@csrf
					<div class="auth-field">
						<label for="email">@lang('messages.E-Mail Address')</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
						@error('email')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-field auth-password-field">
						<label for="password">@lang('messages.Password')</label>
						<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password" required>
						<button type="button" class="auth-password-toggle" data-password-toggle="password" aria-label="@lang('messages.Show password')" aria-pressed="false">
							<i class="fa fa-eye-slash" aria-hidden="true"></i>
						</button>
						@error('password')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-form-row">
						<label class="auth-check" for="remember">
							<input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
							<span>@lang('messages.Remember Me')</span>
						</label>
						<a class="auth-link" href="{{ route('change.password.get') }}">@lang('messages.Forgot password?')</a>
					</div>
					<button type="submit" class="btn btn-primary auth-submit">@lang('messages.Login securely')</button>
				</form>

				<div class="auth-divider" aria-hidden="true"></div>
				<p class="auth-switch">@lang('messages.Do not have an account?') <a class="auth-link" href="{{ route('register') }}">@lang('messages.Create One')</a></p>
				<div class="auth-security-note">
					<i class="fa fa-circle-info" aria-hidden="true"></i>
					<span>@lang('messages.For your protection, authentication requests are CSRF protected and repeated attempts are throttled.')</span>
				</div>
			</div>
		</div>
	</section>
</main>
@endsection
