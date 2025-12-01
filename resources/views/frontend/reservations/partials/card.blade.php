<div class="card mb-3 p-3 shadow-sm">
    <div class="d-flex justify-content-between">
        <h5>Booking #{{ $res->code }}</h5>
        <span class="badge bg-{{ $res->is_active ? 'success' : 'secondary' }}">
            {{ $res->status }}
        </span>
    </div>

    <p class="mb-1"><strong>{{ $res->hotel_name }}</strong></p>
    <p class="text-muted">
        {{ $res->checkin_date }} - {{ $res->checkout_date }} ({{ $res->nights }} Nights, {{ $res->guests }} Guests)
    </p>

    <div class="d-flex justify-content-between align-items-center">
        <span class="fw-bold">${{ number_format($res->total_price) }}</span>
        <div>
            <a href="{{ route('user.reservations.show', $res->id) }}" class="btn btn-sm btn-primary">View</a>
            @if($res->is_cancelable)
                <button class="btn btn-sm btn-outline-danger">Cancel</button>
            @endif
        </div>
    </div>
</div>
