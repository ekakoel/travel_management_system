@extends('layouts.master-login')
@section('title', __('messages.Password recovery'))
@section('content')
<main class="auth-shell">
	<section class="auth-panel" aria-label="@lang('messages.Password recovery')">
		<aside class="auth-panel__story">
			<a class="auth-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Bali Kami Tour') }}">
				<img src="{{ asset('storage/logo/' . config('app.logo_img_color')) }}" alt="{{ config('app.name', 'Bali Kami Tour') }}">
				<strong>{{ config('app.name', 'Bali Kami Tour') }}</strong>
			</a>
			<div class="auth-story-copy">
				<span class="auth-story-copy__eyebrow">@lang('messages.Password recovery')</span>
				<h1>@lang('messages.Reset your access')</h1>
				<p>@lang('messages.Enter your registered email and we will send instructions to reset your password.')</p>
			</div>
			<ul class="auth-trust-list" aria-label="@lang('messages.Security note')">
				<li><i class="fa fa-envelope-circle-check" aria-hidden="true"></i>@lang('messages.Reset links are sent only to registered emails')</li>
				<li><i class="fa fa-clock" aria-hidden="true"></i>@lang('messages.Repeated requests are rate limited')</li>
				<li><i class="fa fa-lock" aria-hidden="true"></i>@lang('messages.CSRF protected authentication')</li>
			</ul>
		</aside>
		<div class="auth-panel__form">
			<div class="auth-form-card">
				<span class="auth-form-card__eyebrow">@lang('messages.Forgot password?')</span>
				<h2>@lang('messages.Password recovery')</h2>
				<p class="auth-form-card__lead">@lang('messages.Enter your registered email and we will send instructions to reset your password.')</p>

				@if (session('status'))
					<div class="alert alert-success auth-alert" role="alert">{{ session('status') }}</div>
				@endif
				@if (session('message'))
					<div class="alert alert-success auth-alert" role="alert">{{ session('message') }}</div>
				@endif
				@if (session('error'))
					<div class="alert alert-danger auth-alert" role="alert">{{ session('error') }}</div>
				@endif

				<form id="forget-password" method="POST" action="{{ route('password.email') }}" class="auth-form">
					@csrf
					<div class="auth-field">
						<label for="email">@lang('messages.E-Mail Address')</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
						@error('email')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<button type="submit" class="btn btn-primary auth-submit">@lang('messages.Send reset link')</button>
				</form>

				<div class="auth-footer-link">
					<a href="{{ route('login') }}" class="auth-link">@lang('messages.Back to login')</a>
				</div>
				<div class="auth-security-note">
					<i class="fa fa-circle-info" aria-hidden="true"></i>
					<span>@lang('messages.For your protection, authentication requests are CSRF protected and repeated attempts are throttled.')</span>
				</div>
			</div>
		</div>
	</section>
</main>
@endsection
