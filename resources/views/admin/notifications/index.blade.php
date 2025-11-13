@extends('layouts.head')

@section('title', __('messages.Notifications'))

@section('content')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                
                @if(session('success'))
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle">
                                @lang('messages.Preregistrations Agent')
                            </div>
                        </div>
                        @forelse ($notifications as $notification)
                            <div class="card mb-2 {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">{{ $notification->data['title'] ?? 'Notification' }}</h5>
                                    <p class="card-text">{{ $notification->data['message'] ?? '' }}</p>
                                    
                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary">View Details</a>
                                    @endif
                                    
                                    <small class="text-muted float-end">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">No pending agent notifications found.</div>
                        @endforelse

                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
@endsection
