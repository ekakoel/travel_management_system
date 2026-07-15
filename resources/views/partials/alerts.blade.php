@if (session('success') || session('danger') || $errors->any())
    <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="@lang('messages.Close')">
            <span aria-hidden="true">&times;</span>
        </button>
        <ul>
            @if (session('success'))
                <li>{{ session('success') }}</li>
            @endif
            @if (session('danger'))
                <li>{{ session('danger') }}</li>
            @endif
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
