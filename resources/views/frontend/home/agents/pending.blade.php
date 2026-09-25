@extends('layouts.app')

@section('title', __('agent-registration.pending.title'))

@section('content')
    <main class="container py-5">
        <h1>@lang('agent-registration.pending.title')</h1>
        <p>@lang('agent-registration.pending.message')</p>
        <a class="btn btn-primary" href="{{ route('home') }}">@lang('agent-registration.pending.return_home')</a>
    </main>
@endsection
