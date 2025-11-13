@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center my-5">
                <img src="{{ url('storage/logo/bali-kami-tour-logo.png') }}" alt="logo" width="300">
            </div>
            <div class="card-verify">
                <div class="title-verify">
                    <div class="card-header">{{ __('Verify Your Email Address') }}</div>
                </div>
                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email use the button below to request another verification link') }}.
                    <div class="row  btn-request-another">
                        <div class="col-md-6">
                            <div class="countdown">
                                <div id="countdown" class="countdown"></div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <form id="formVerification" class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <div>
                                    <button id="verificationButton" type="submit" class="btn btn-primary  m-0 align-baseline">{{ __('Click here to request another') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var formVerification = document.getElementById('formVerification');
var verificationButton = document.getElementById('verificationButton');
var countdownElement = document.getElementById('countdown');
var canSubmit = true;
function handleSubmit(event) {
    event.preventDefault();
    if (!canSubmit) return;
    formVerification.submit();
    canSubmit = false;
    verificationButton.disabled = true;
    var remainingTime = 30;
    var countdownInterval = setInterval(function() {
        countdownElement.textContent = remainingTime;
        remainingTime--;
        if (remainingTime < 0) {
            clearInterval(countdownInterval);
            countdownElement.textContent = ''; 
            verificationButton.disabled = false; 
            canSubmit = true; 
        }
    }, 1000);
}
formVerification.addEventListener('submit', handleSubmit);
</script>
@endsection
