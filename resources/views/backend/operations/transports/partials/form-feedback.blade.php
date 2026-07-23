@if ($errors->any() || session()->has('success') || session()->has('error'))
    <section class="backend-feedback transport-form-feedback">
        @if ($errors->any())
            <div class="backend-alert backend-alert--danger">
                <strong>Action needs attention.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session()->has('success'))
            <div class="backend-alert backend-alert--success">
                <strong>{{ session('success') }}</strong>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="backend-alert backend-alert--danger">
                <strong>{{ session('error') }}</strong>
            </div>
        @endif
    </section>
@endif
