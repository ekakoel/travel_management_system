{{-- @livewire('register-passwords') --}}
@extends('layouts.master-login')
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
							<h1 class="fs-4 fw-bold">@lang('messages.Register')</h1>
							@if (count($errors) > 0)
								<div class="alert-danger alert-danger-form">
									<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
									@foreach ($errors->all() as $error)
									<div class="error">{{ $error }}</div>
									@endforeach
								</div>
							@endif
							@uiEnabled('form-register')
								<form method="POST" action="{{ route('register') }}">
									@csrf
									<div class="form-group">
										<label for="name">@lang('messages.Name')</label>
										<input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
										@error('name')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
									<div class="form-group">
										<label for="username">@lang('messages.Username')</label>
										<input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required>
										@error('username')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
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
									
									<div class="form-group login-register" id="password-confirm" >
										<label for="password-confirmation">@lang('messages.Confirm Password')</label>
										<input id="password-confirmation" type="password" class="input-icon form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
										<a href="" ><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
									</div>

									<div class="form-group">
										{{-- <input class="form-check-left" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> --}}
										<input class="form-check-left" style="display: block !important" type="checkbox" id="termsAndCondition" name="terms" value="1" onchange="checkTerms" {{ !old('terms') ?: 'checked' }}>
										<span>@lang('messages.By registering you agree with our terms and condition.')</span>
									</div>
									
									
									<div class="text-right">
										<button id="btnRegister" type="submit" disabled  class="btn btn-primary">@lang('messages.Register')</button>
									</div>
								</form>
							@elseUiEnabled('btn-register')
							@endUiEnabled
						</div>
						
						<div class="card-footer py-3 border-0">
							<div class="text-center">
								@lang('messages.Already have an account?') <a href="login" class="text-dark link-a">@lang('messages.Login')</a>
							</div>
						</div>
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
			$("#password-confirm a").on('click', function(event) {
				event.preventDefault();
				if($('#password-confirm input').attr("type") == "text"){
					$('#password-confirm input').attr('type', 'password');
					$('#password-confirm i').addClass( "fa-eye-slash" );
					$('#password-confirm i').removeClass( "fa-eye view" );
				}else if($('#password-confirm input').attr("type") == "password"){
					$('#password-confirm input').attr('type', 'text');
					$('#password-confirm i').removeClass( "fa-eye-slash" );
					$('#password-confirm i').addClass( "fa-eye view" );
				}
			});
		});

		var  chk = document.getElementById('termsAndCondition');
		chk.addEventListener('click', checked, false);
		function checked(){
			if(chk.checked) {
				document.getElementById('btnRegister').removeAttribute('disabled');
			} else {
				document.getElementById('btnRegister').setAttribute('disabled','disabled');
			}
		}
	</script>
@endsection