@extends('layouts.master-login')
@section('title', __('messages.Reset Password'))

@section('content')
<main class="auth-shell">
	<section class="auth-panel" aria-label="@lang('messages.Reset Password')">
		<aside class="auth-panel__story">
			<a class="auth-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Bali Kami Tour') }}">
				<img src="{{ asset('storage/logo/' . config('app.logo_img_color')) }}" alt="{{ config('app.name', 'Bali Kami Tour') }}">
				<strong>{{ config('app.name', 'Bali Kami Tour') }}</strong>
			</a>
			<div class="auth-story-copy">
				<span class="auth-story-copy__eyebrow">@lang('messages.Reset Password')</span>
				<h1>@lang('messages.Create a new secure password')</h1>
				<p>@lang('messages.Use at least 8 characters and keep this password private to protect account access.')</p>
			</div>
			<ul class="auth-trust-list" aria-label="@lang('messages.Security note')">
				<li><i class="fa fa-key" aria-hidden="true"></i>@lang('messages.Reset token validation')</li>
				<li><i class="fa fa-lock" aria-hidden="true"></i>@lang('messages.Password is stored securely')</li>
				<li><i class="fa fa-shield-halved" aria-hidden="true"></i>@lang('messages.Repeated requests are rate limited')</li>
			</ul>
		</aside>
		<div class="auth-panel__form">
			<div class="auth-form-card">
				<span class="auth-form-card__eyebrow">@lang('messages.Reset Password')</span>
				<h2>@lang('messages.Create a new secure password')</h2>
				<p class="auth-form-card__lead">@lang('messages.Confirm your registered email and set a new password for your account.')</p>

				<form id="reset-password" method="POST" action="{{ route('password.update') }}" class="auth-form">
					@csrf
					<input type="hidden" name="token" value="{{ $token }}">
					<div class="auth-field">
						<label for="email">@lang('messages.E-Mail Address')</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" autocomplete="email" required autofocus>
						@error('email')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-field auth-password-field">
						<label for="password">@lang('messages.Password')</label>
						<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" required>
						<button type="button" class="auth-password-toggle" data-password-toggle="password" aria-label="@lang('messages.Show password')" aria-pressed="false">
							<i class="fa fa-eye-slash" aria-hidden="true"></i>
						</button>
						@error('password')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-field auth-password-field">
						<label for="password-confirm">@lang('messages.Confirm Password')</label>
						<input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" required>
						<button type="button" class="auth-password-toggle" data-password-toggle="password-confirm" aria-label="@lang('messages.Show password')" aria-pressed="false">
							<i class="fa fa-eye-slash" aria-hidden="true"></i>
						</button>
					</div>
					<button type="submit" class="btn btn-primary auth-submit">@lang('messages.Reset Password')</button>
				</form>
			</div>
		</div>
	</section>
</main>
@endsection
