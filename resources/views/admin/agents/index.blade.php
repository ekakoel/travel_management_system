@section('title', __('messages.Agent Request Registration'))
@section('content')
    @extends('layouts.head')
    <div class="container">
        <h1 class="mb-4">Agent Detail</h1>
    
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $agent->company_name?$agent->company_name:"-" }}</h5>
                <p><strong>Contact Person:</strong> {{ $agent->contact_name }}</p>
                <p><strong>Email:</strong> {{ $agent->email }}</p>
                <p><strong>Phone:</strong> {{ $agent->phone }}</p>
                <p><strong>Country:</strong> {{ $agent->country }}</p>
                <p><strong>Status:</strong> 
                    @if($agent->is_verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </p>
    
                {{-- Add more fields as needed --}}
            </div>
        </div>
    
        <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
@endsection
