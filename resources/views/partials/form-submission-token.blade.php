@php
    $submissionTokenName = $name ?? 'submission_token';
    $submissionTokenValue = $value ?? old($submissionTokenName) ?: (string) \Illuminate\Support\Str::uuid();
@endphp
<input type="hidden" name="{{ $submissionTokenName }}" value="{{ $submissionTokenValue }}" data-form-submission-token>
