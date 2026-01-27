@php
    use Carbon\Carbon;
    $years = Carbon::now();
@endphp
<div class="footer text-center d-print-none p-b-18">
    <div class="inline-link">
        <a href="{{ config('app.term') }}" target="_blank">@lang('messages.Terms and Conditions')</a> - 
        <a href="{{ config('app.privacy') }}" target="_blank">@lang('messages.Privacy Policy')</a>
        {{-- <a href="{{ config('app.CONTACT') }}" target="_blank">Contact</a> --}}
    </div>
    <a href="{{ config('app.app_url') }}" target="_blank">{{ config('app.vendor') }}</a>
    <p>@lang('messages.Copyright') ©{{ date('Y',strtotime($years))." ".config('app.business') }} | {{ config('app.app_status') }} @lang('messages.Version') {{ config('app.app_version') }}</p>
</div>