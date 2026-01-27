@if (session('success') || session('danger') || $errors->any())
    <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }}">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
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
