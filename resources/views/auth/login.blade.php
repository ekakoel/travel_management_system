
{{-- @extends('component.sysload') --}}
@php
	 $token = Str::random(64);
@endphp
@extends('layouts.master-login')
@section('title', 'Login Agent')
@section('content')
<section class="anim-feed-up">
	<div class="position-ref full-height bg-01">
		<div class="overlay"></div>
		<div class="container h-100">
			<div class="row justify-content-sm-center">
				<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6">
					@include('layouts.logo_front')
					<div class="card shadow-grey">
						<div class="card-body p-5">
							<h1 class="fs-4 fw-bold mb-4">@lang('messages.Login')</h1>
							<form method="POST" action="{{ route('login') }}">
								@csrf
								<div class="form-group">
									<label for="email">@lang('messages.E-Mail Address')</label>
									<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
									@error('email')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>
								<div class="form-group login-register" id="password">
									<label for="password">@lang('messages.Password')</label>
									<input id="password" type="password" class="input-icon form-control" name="password" required>
									<a href="" ><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
								</div>
								<div class="mb-3">
									<div class="form-group flex-right w-100 align-items-center">
										<div class="form-check">
											<input class="form-check-left" style="display: block !important" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
											<label for="remember" class="form-check-label">@lang('messages.Remember Me')</label>
										</div>
										<button type="submit" class="btn btn-primary ms-auto">@lang('messages.Login')</button>
										@if (Route::has('password.request'))
										@endif
									</div>
								</div>
							</form>
							<div class="row">
								<div class="col-md-12 text-center"><a href="{{ route('change.password.get') }}">Forgot Your Password?</a></div>
							</div>
						</div>
						@uiEnabled('btn-register')
							<div class="card-footer py-3 border-0">
								<div class="text-center">
									@lang('messages.Do not have an account?') <a href="/register" class="text-dark link-a">@lang('messages.Create One')</a>
								</div>
							</div>
						@endUiEnabled
					</div>
					@include('/layouts.footer')
				</div>
			</div>
		</div>
	</div>
	</section>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
	<script>
		$(document).ready(function() {
			$("#password a").on('click', function(event) {
				event.preventDefault();
				if($('#password input').attr("type") == "text"){
					$('#password input').attr('type', 'password');
					$('#password i').addClass( "fa-eye-slash" );
					$('#password i').removeClass( "fa-eye view" );
				}else if($('#password input').attr("type") == "password"){
					$('#password input').attr('type', 'text');
					$('#password i').removeClass( "fa-eye-slash" );
					$('#password i').addClass( "fa-eye view" );
				}
			});
			$("#password-confirmation a").on('click', function(event) {
				event.preventDefault();
				if($('#password-confirmation input').attr("type") == "text"){
					$('#password-confirmation input').attr('type', 'password');
					$('#password-confirmation i').addClass( "fa-eye-slash" );
					$('#password-confirmation i').removeClass( "fa-eye view" );
				}else if($('#password-confirmation input').attr("type") == "password"){
					$('#password-confirmation input').attr('type', 'text');
					$('#password-confirmation i').removeClass( "fa-eye-slash" );
					$('#password-confirmation i').addClass( "fa-eye view" );
				}
			});
		});
	</script>
@endsection
