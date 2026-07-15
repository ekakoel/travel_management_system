@extends('layouts.master-login')
@section('title', __('messages.Register'))
@section('content')
<main class="auth-shell">
	<section class="auth-panel" aria-label="@lang('messages.Create partner account')">
		<aside class="auth-panel__story">
			<a class="auth-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Bali Kami Tour') }}">
				<img src="{{ asset('storage/logo/' . config('app.logo_img_color')) }}" alt="{{ config('app.name', 'Bali Kami Tour') }}">
				<strong>{{ config('app.name', 'Bali Kami Tour') }}</strong>
			</a>
			<div class="auth-story-copy">
				<span class="auth-story-copy__eyebrow">@lang('messages.Create partner account')</span>
				<h1>@lang('messages.Register your team access')</h1>
				<p>@lang('messages.Register your account to request access to the Bali Kami Tour partner workspace.')</p>
			</div>
			<ul class="auth-trust-list" aria-label="@lang('messages.Security note')">
				<li><i class="fa fa-user-check" aria-hidden="true"></i>@lang('messages.Registration is reviewed before operational access is granted.')</li>
				<li><i class="fa fa-envelope-circle-check" aria-hidden="true"></i>@lang('messages.Email verification required')</li>
				<li><i class="fa fa-lock" aria-hidden="true"></i>@lang('messages.CSRF protected authentication')</li>
			</ul>
		</aside>
		<div class="auth-panel__form">
			<div class="auth-form-card">
				<span class="auth-form-card__eyebrow">@lang('messages.Register')</span>
				<h2>@lang('messages.Create secure account')</h2>
				<p class="auth-form-card__lead">@lang('messages.Use accurate account information so our team can validate the right access level.')</p>

				@if (count($errors) > 0)
					<div class="alert alert-danger auth-alert" role="alert">
						@foreach ($errors->all() as $error)
							<div>{{ $error }}</div>
						@endforeach
					</div>
				@endif

				<form method="POST" action="{{ route('register') }}" class="auth-form">
					@csrf
					<div class="auth-field">
						<label for="name">@lang('messages.Name')</label>
						<input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" required>
						@error('name')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-field">
						<label for="username">@lang('messages.Username')</label>
						<input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" autocomplete="username" required>
						@error('username')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<div class="auth-field">
						<label for="email">@lang('messages.E-Mail Address')</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required>
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
						<label for="password-confirmation">@lang('messages.Confirm Password')</label>
						<input id="password-confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" autocomplete="new-password" required>
						<button type="button" class="auth-password-toggle" data-password-toggle="password-confirmation" aria-label="@lang('messages.Show password')" aria-pressed="false">
							<i class="fa fa-eye-slash" aria-hidden="true"></i>
						</button>
						@error('password_confirmation')
							<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
						@enderror
					</div>
					<label class="auth-check" for="termsAndCondition">
						<input type="checkbox" id="termsAndCondition" name="terms" value="1" data-auth-terms="#btnRegister" {{ old('terms') ? 'checked' : '' }}>
						<span>
							@lang('messages.By registering you agree with our terms and condition.')
							<a class="auth-link" href="{{ route('terms-and-conditions') }}" target="_blank" rel="noopener">@lang('messages.Terms and Conditions')</a>
							/
							<a class="auth-link" href="{{ route('privacy-policy') }}" target="_blank" rel="noopener">@lang('messages.Privacy Policy')</a>
						</span>
					</label>
					<button id="btnRegister" type="submit" class="btn btn-primary auth-submit" disabled>@lang('messages.Register')</button>
				</form>

				<div class="auth-divider" aria-hidden="true"></div>
				<p class="auth-switch">@lang('messages.Already have an account?') <a class="auth-link" href="{{ route('login') }}">@lang('messages.Login')</a></p>
			</div>
		</div>
	</section>
</main>
@endsection
