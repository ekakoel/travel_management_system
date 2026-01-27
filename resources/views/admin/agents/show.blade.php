@extends('layouts.head')

@section('title', __('messages.Validate Agent'))

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
                                <i class="dw dw-hotel"></i> @lang('messages.Hotel')
                            </div>
                        </div>
                        <h1 class="mb-4">Agent Detail</h1>
    
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $agent->company_name }}</h5>
                                <p><strong>PIC Name:</strong> {{ $agent->pic_name }}</p>
                                <p><strong>Email:</strong> {{ $agent->email }}</p>
                                <p><strong>Phone:</strong> {{ $agent->phone }}</p>
                                <p><strong>Country:</strong> {{ $agent->country }}</p>
                                <p><strong>Address:</strong> {{ $agent->company_address }}</p>
                                <p><strong>Website:</strong> {{ $agent->website }}</p>
        
                                <p><strong>Business License:</strong>
                                    @if($agent->business_license)
                                        <a href="{{ asset('storage/' . $agent->business_license) }}" target="_blank">View</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </p>
        
                                <p><strong>Tax Document:</strong>
                                    @if($agent->tax_document)
                                        <a href="{{ asset('storage/' . $agent->tax_document) }}" target="_blank">View</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </p>
        
                                <p><strong>Company Letter:</strong>
                                    @if($agent->company_letter)
                                        <a href="{{ asset('storage/' . $agent->company_letter) }}" target="_blank">View</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </p>
        
                                <p><strong>Translation Documents:</strong>
                                    @if($agent->translation_documents)
                                        <a href="{{ asset('storage/' . $agent->translation_documents) }}" target="_blank">View</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </p>
        
                                <p><strong>Status:</strong> 
                                    @if($agent->status == 'verified')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($agent->status == 'pending')
                                        <span class="badge bg-info">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </p>
        
                                @if($agent->status != 'verified')
                                    <form action="{{ route('admin.agents.verify', $agent->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">Verify Agent</button>
                                    </form>
                                @else
                                    <div class="alert alert-success mt-3">
                                        This agent is already verified.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary mt-3">Back to Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
