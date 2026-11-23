@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">{{ __('Order updates') }}</h1>
        <a href="{{ route('view.orders') }}" class="btn btn-outline-primary">{{ __('View orders') }}</a>
    </div>

    @forelse ($notifications as $notification)
        @php($data = $notification->data)
        <article class="border rounded p-3 mb-3 {{ $notification->read_at ? 'bg-light' : 'border-primary' }}">
            <div class="d-flex justify-content-between gap-3">
                <div>
                    <h2 class="h6 mb-1">{{ $data['title'] ?? __('Order update') }}</h2>
                    <p class="mb-1">{{ $data['message'] ?? '' }}</p>
                    <small class="text-muted">{{ $data['order_number'] ?? '' }} · {{ $notification->created_at->format('Y-m-d H:i') }}</small>
                </div>
                @unless ($notification->read_at)
                    <form method="POST" action="{{ route('order-notifications.read', $notification->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('Mark as read') }}</button>
                    </form>
                @endunless
            </div>
        </article>
    @empty
        <p class="text-muted">{{ __('You have no order updates yet.') }}</p>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection
