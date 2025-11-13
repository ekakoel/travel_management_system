@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 m-t-56">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="title">{{ __('Reset Password') }}</div> 
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form id="forget-password" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right flex-end">{{ __('E-Mail Address') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </form>
                <div class="card-box-footer">
                    <button type="submit" form="forget-password" class="btn btn-primary">{{ __('Send Password Reset Link') }}</button>
                    <a href="/login" type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
