@php
    $isEdit = $mode === 'edit';
    $fieldValue = function ($field, $default = '') use ($isEdit, $user) {
        return old($field, $isEdit && $user ? $user->{$field} : $default);
    };
@endphp

<div class="backend-form-grid user-manager-form-grid">
    @if ($isEdit && $user)
        <input type="hidden" name="managed_user_id" value="{{ $user->id }}">
    @endif

    <label>
        <span>Name <b>*</b></span>
        <input class="backend-form-control" type="text" name="name" value="{{ $fieldValue('name') }}" placeholder="Full name" autocomplete="name" required>
    </label>

    <label>
        <span>Username <b>*</b></span>
        <input class="backend-form-control" type="text" name="username" value="{{ $fieldValue('username') }}" placeholder="Username" autocomplete="username" required>
    </label>

    <label>
        <span>Email <b>*</b></span>
        <input class="backend-form-control" type="email" name="email" value="{{ $fieldValue('email') }}" placeholder="Email address" autocomplete="email" required>
    </label>

    <label>
        <span>Code</span>
        <input class="backend-form-control" type="text" name="code" value="{{ $fieldValue('code') }}" placeholder="Internal code" autocomplete="off">
    </label>

    <label>
        <span>Type <b>*</b></span>
        <select class="backend-form-control" name="type" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected($fieldValue('type', 'user') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span>Group <b>*</b></span>
        <select class="backend-form-control" name="position" required>
            <option value="">Select group</option>
            @foreach ($positions as $value => $label)
                <option value="{{ $value }}" @selected($fieldValue('position') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    @if ($isEdit)
        <label>
            <span>Status <b>*</b></span>
            <select class="backend-form-control" name="status" required>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($fieldValue('status', 'Active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="user-manager-check-field user-manager-toggle-field">
            <input type="hidden" name="is_approved" value="0">
            <input type="checkbox" name="is_approved" value="1" @checked((bool) $fieldValue('is_approved', false))>
            <span>Approved</span>
        </label>
    @endif
</div>
