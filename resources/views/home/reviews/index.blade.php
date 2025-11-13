@section('title','Reviews')
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="card-box">

                <div class="card-box-title">Customer Reviews</div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Error:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">
                        @foreach((array) session('success') as $msg)
                            <p class="mb-1">{{ $msg }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h5>
                            <span class="badge bg-secondary">{{ count($pendingReviews) }} Pending</span>
                            <span class="badge bg-success">{{ count($approvedReviews) }} Approved</span>
                            <span class="badge bg-danger">{{ count($rejectedReviews) }} Rejected</span>
                        </h5>
                        <hr class="my-1">
                    </div>
                    <div class="col-md-12">
                        <h4>📊 Review Statistics</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">🏨 General Service</div>
                            <div class="card-body">
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Accommodation</span>
                                    <span>{!! renderStars($serviceStats->accommodation) !!} ({{ number_format($serviceStats->accommodation, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Meals</span>
                                    <span>{!! renderStars($serviceStats->meals) !!} ({{ number_format($serviceStats->meals, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Tour Sites</span>
                                    <span>{!! renderStars($serviceStats->tour_sites) !!} ({{ number_format($serviceStats->tour_sites, 1) }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">🚘 Transport Service</div>
                            <div class="card-body">
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Cleanliness</span>
                                    <span>{!! renderStars($transportStats->transportation_cleanliness) !!} ({{ number_format($transportStats->transportation_cleanliness, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Air Conditioner</span>
                                    <span>{!! renderStars($transportStats->transportation_air_condition) !!} ({{ number_format($transportStats->transportation_air_condition, 1) }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">🙋🏻‍♂️ Guide Service</div>
                            <div class="card-body">
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Attitude</span>
                                    <span>{!! renderStars($guideStats->attitude) !!} ({{ number_format($guideStats->attitude, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Explanation</span>
                                    <span>{!! renderStars($guideStats->explanation) !!} ({{ number_format($guideStats->explanation, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Knowledge</span>
                                    <span>{!! renderStars($guideStats->knowledge) !!} ({{ number_format($guideStats->knowledge, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Time Control</span>
                                    <span>{!! renderStars($guideStats->time_control) !!} ({{ number_format($guideStats->time_control, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Neatness</span>
                                    <span>{!! renderStars($guideStats->guide_neatness) !!} ({{ number_format($guideStats->guide_neatness, 1) }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">👨‍✈️ Driver Service</div>
                            <div class="card-body">
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Punctuality</span>
                                    <span>{!! renderStars($driverStats->driver_punctuality) !!} ({{ number_format($driverStats->driver_punctuality, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Driving Skills</span>
                                    <span>{!! renderStars($driverStats->driver_driving_skills) !!} ({{ number_format($driverStats->driver_driving_skills, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Neatness</span>
                                    <span>{!! renderStars($driverStats->driver_neatness) !!} ({{ number_format($driverStats->driver_neatness, 1) }})</span>
                                </p>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-1">
                @if($pendingReviews->count() || $approvedReviews->count() || $rejectedReviews->count())
                    <div class="nav-container">
                        <ul class="nav nav-tabs" role="tablist">
                            @if($pendingReviews->count())
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#pendingReviews" role="tab">🕒 Pending</a>
                                </li>
                            @endif
                            @if($approvedReviews->count())
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#approvedReviews" role="tab">✔️ Approved</a>
                                </li>
                            @endif
                            @if($rejectedReviews->count())
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#rejectedReviews" role="tab">❌ Rejected</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    
                @endif
                <div class="tab-content">
                    @if($pendingReviews->count())
                        <div id="pendingReviews" class="row tab-pane active">
                            @foreach($pendingReviews as $review)
                                <div class="col-md-12">
                                    <div class="card m-b-18">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0 text-primary">
                                                    ✍️ {{ $review->customer_name ?? 'Anonymous' }} 
                                                    <span>({{ dateFormat($review->created_at) }})</span>
                                                </h5>
                                                <span class="badge bg-warning text-dark text-right">Pending</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <strong>Booking Code</strong><br>
                                                    <p class="ph-1">{{ $review->booking_code }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <strong>Arrival Date</strong><br>
                                                    <p class="ph-1">{{ dateFormat($review->arrival_date) }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <strong>Departure Date</strong><br>
                                                    <p class="ph-1">{{ dateFormat($review->departure_date) }}</p>
                                                </div>
                                            </div>
                                            <div class="content-title my-2">
                                                Agent: {{ $review->travel_agent }}
                                            </div>
                                            @if ($review->accommodation || $review->meal || $review->tour_sites)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <hr class="my-1">
                                                        <strong>General Services</strong>
                                                    </div>
                                                    @if ($review->accommodation)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Accommodation
                                                            <div class="ph-1">
                                                                {!! renderStars($review->accommodation) !!} 
                                                                <span class="text-muted">({{ number_format($review->accommodation, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->meals)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Meal
                                                            <div class="ph-1">
                                                                {!! renderStars($review->meals) !!} 
                                                                <span class="text-muted">({{ number_format($review->meals, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->tour_sites)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Tour Site
                                                            <div class="ph-1">
                                                                {!! renderStars($review->tour_sites) !!} 
                                                                <span class="text-muted">({{ number_format($review->tour_sites, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <hr class="my-1">
                                            @endif
                                            @if ($review->transportation_cleanliness || $review->transportation_air_condition)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <strong>Transport</strong>
                                                    </div>
                                                    @if ($review->transportation_cleanliness)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Cleanliness
                                                            <div class="ph-1">
                                                                {!! renderStars($review->transportation_cleanliness) !!} 
                                                                <span class="text-muted"> ({{ number_format($review->transportation_cleanliness, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->transportation_air_condition)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Air Conditioner
                                                            <div class="ph-1">
                                                                {!! renderStars($review->transportation_air_condition) !!} 
                                                                <span class="text-muted">({{ number_format($review->transportation_air_condition, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <hr class="my-1">
                                            @endif
                                            @if ($review->attitude || $review->knowledge || $review->explanation || $review->time_control)
                                                <div class="row">
                                                    <div class="col-md-12 inline-flex-space-between">
                                                        <strong>Guide:</strong>
                                                        <strong>{{ $review->guide ? $review->guide->name:$review->guide_name; }}</strong>
                                                    </div>
                                                    @if ($review->attitude)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Attitude
                                                            <div class="ph-1">
                                                                {!! renderStars($review->attitude) !!} 
                                                                <span class="text-muted">({{ number_format($review->attitude, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->knowledge)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Knowledge
                                                            <div class="ph-1">
                                                                {!! renderStars($review->knowledge) !!} 
                                                                <span class="text-muted">({{ number_format($review->knowledge, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->explanation)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Explanation
                                                            <div class="ph-1">
                                                                {!! renderStars($review->explanation) !!} 
                                                                <span class="text-muted">({{ number_format($review->explanation, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->time_control)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Time Control
                                                            <div class="ph-1">
                                                                {!! renderStars($review->time_control) !!} 
                                                                <span class="text-muted">({{ number_format($review->time_control, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->guide_neatness)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Neatness
                                                            <div class="ph-1">
                                                                {!! renderStars($review->guide_neatness) !!} 
                                                                <span class="text-muted">({{ number_format($review->guide_neatness, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <hr class="my-1">
                                            @if ($review->driver_name || $review->driver_punctuality || $review->driver_driving_skills || $review->driver_neatness)
                                                <div class="row">
                                                    <div class="col-md-12 inline-flex-space-between">
                                                        <strong>Driver:</strong>
                                                        <strong>{{ $review->driver_name ? $review->driver_name:"-"; }}</strong>
                                                    </div>
                                                    @if ($review->driver_punctuality)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Punctuality
                                                            <div class="ph-1">
                                                                {!! renderStars($review->driver_punctuality) !!} 
                                                                <span class="text-muted">({{ number_format($review->driver_punctuality, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->driver_driving_skills)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Driving Skills
                                                            <div class="ph-1">
                                                                {!! renderStars($review->driver_driving_skills) !!} 
                                                                <span class="text-muted">({{ number_format($review->driver_driving_skills, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($review->driver_neatness)
                                                        <div class="col-md-2 inline-flex-space-between">
                                                            Neatness
                                                            <div class="ph-1">
                                                                {!! renderStars($review->driver_neatness) !!} 
                                                                <span class="text-muted">({{ number_format($review->driver_neatness, 1) }})</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <hr class="my-1">
                                            @endif
                                            <div class="row">
                                                <div class="col-md-2 inline-flex-space-between">
                                                    Travel Mood
                                                    <div class="ph-1">
                                                        @if ($review->travel_mood == 'Very Satisfied')
                                                            <span data-toggle="tooltip" title="Very Satisfied" >😊</span>
                                                        @elseif ($review->travel_mood == 'Satisfied')
                                                            <span data-toggle="tooltip" title="Satisfied">🙂</span>
                                                        @elseif ($review->travel_mood == 'Neutral')
                                                            <span data-toggle="tooltip" title="Neutral">😐</span>
                                                        @elseif ($review->travel_mood == 'Need Improvement')
                                                            <span data-toggle="tooltip" title="Need Improvement">🙁</span>
                                                        @endif
                                                        {{ $review->travel_mood }}
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    @if ($review->customer_review)
                                                        <strong>Review</strong>
                                                        <div class="ph-1">
                                                            {!! $review->customer_review !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <form id="formApprove{{ $review->id }}" method="POST" action="{{ route('admin.reviews.updateStatus', $review->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                            </form>
                                            <form id="formReject{{ $review->id }}" method="POST" action="{{ route('admin.reviews.updateStatus', $review->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                            </form>
                                            <button type="submit" form="formApprove{{ $review->id }}" class="btn btn-success" onclick="return confirm('Approve this review?')">
                                                <i class="fa fa-check-circle" aria-hidden="true"></i> Approve
                                            </button>
                                            <button type="submit" form="formReject{{ $review->id }}" class="btn btn-danger" onclick="return confirm('Reject this review?')">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                    
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($approvedReviews->count())
                        <div id="approvedReviews" class="row tab-pane fade" role="tabpanel">
                            <div class="col-md-12">
                                @php
                                    $groupedApproved = $approvedReviews->groupBy('booking_code');
                                @endphp
                                @foreach ($groupedApproved as $agent => $approve_reviews)
                                    <div class="content-title justify-content-between mb-2">
                                        <strong>{{ $agent ?? 'Unknown Agent' }}</strong>
                                        <div class="d-flex float-right">
                                            <a href="{{ route('reviews.print', ['bookingCode' => $approve_reviews->first()->booking_code]) }}" class="btn-print">
                                                🖨️ Print
                                            </a>
                                        </div>
                                    </div>
                                    @foreach($approve_reviews as $approve_review)
                                        <div class="border-bottom ml-2 pb-2 mb-2 row review-printable" id="review-{{ $approve_review->id }}">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p><strong>{{ $approve_review->customer_name ?? 'Anonymous' }} ({{ $approve_review->booking_code }})</strong> <small class="text-muted">({{ $approve_review->created_at->format('d M Y') }})</small></p>
                                                        @if ($approve_review->customer_review)
                                                            <p><i>"{!! $approve_review->customer_review !!}"</i></p>
                                                        @endif
                                                    </div>
                                                    @if ($approve_review->driver_punctuality || $approve_review->driver_driving_skills || $approve_review->driver_neatness)
                                                        <div class="col-md-6">
                                                            <p><b>Driver</b> ({{ $approve_review->driver_name }})</p>
                                                            <p>
                                                                @if ($approve_review->driver_punctuality)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Punctuality">▸ P({{ number_format($approve_review->driver_punctuality, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->driver_driving_skills)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Driving Skill">▸ S({{ number_format($approve_review->driver_driving_skills, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->driver_neatness)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Neatness">▸ N({{ number_format($approve_review->driver_neatness, 1) }})</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($approve_review->attitude || $approve_review->knowledge || $approve_review->explanation || $approve_review->time_control || $approve_review->guide_neatness)
                                                        <div class="col-md-6">
                                                            <p><b>Guide</b> ({{ $approve_review->guide_name }})</p>
                                                            <p>
                                                                @if ($approve_review->attitude)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Attitude">▸ A({{ number_format($approve_review->attitude, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->knowledge)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Knowledge">▸ K({{ number_format($approve_review->knowledge, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->explanation)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Explanation">▸ E({{ number_format($approve_review->explanation, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->time_control)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Time Control">▸ T({{ number_format($approve_review->time_control, 1) }})</span>
                                                                @endif
                                                                @if ($approve_review->guide_neatness)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Neatness">▸ N({{ number_format($approve_review->guide_neatness, 1) }})</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-3 text-right align-self-end">
                                                <form id="formApproveReject{{ $approve_review->id }}" method="POST" action="{{ route('admin.reviews.updateStatus', $approve_review->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                </form>
                                                <button type="submit" form="formApproveReject{{ $approve_review->id }}" class="btn btn-danger" onclick="return confirm('Reject this review?')">
                                                    <i class="fa fa-times-circle" aria-hidden="true"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach

                                <div class="mt-3">
                                    {{ $approvedReviews->appends(['tab' => 'approvedReviews'])->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($rejectedReviews->count())
                        <div id="rejectedReviews" class="row tab-pane fade" role="tabpanel">
                            <div class="col-md-12">
                                @php
                                    $groupedRejected = $rejectedReviews->groupBy('booking_code');
                                @endphp
                                @foreach ($groupedRejected as $agent => $rejected_reviews)
                                    <div class="content-title mb-2"><strong>{{ $agent ?? 'Unknown Agent' }}</strong></div>
                                    @foreach ($rejected_reviews as $rejected_review)
                                        <div class="border-bottom ml-2 pb-2 mb-2 row">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p><strong>{{ $rejected_review->customer_name ?? 'Anonymous' }} ({{ $rejected_review->booking_code }})</strong> <small class="text-muted">({{ $rejected_review->created_at->format('d M Y') }})</small></p>
                                                        @if ($rejected_review->customer_review)
                                                            <p><i>"{!! $rejected_review->customer_review !!}"</i></p>
                                                        @endif
                                                    </div>
                                                    @if ($rejected_review->driver_punctuality || $rejected_review->driver_driving_skills || $rejected_review->driver_neatness)
                                                        <div class="col-md-6">
                                                            <p><b>Driver</b> ({{ $rejected_review->driver_name }})</p>
                                                            <p>
                                                                @if ($rejected_review->driver_punctuality)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Punctuality">▸ P({{ number_format($rejected_review->driver_punctuality, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->driver_driving_skills)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Driving Skills">▸ S({{ number_format($rejected_review->driver_driving_skills, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->driver_neatness)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Neatness">▸ N({{ number_format($rejected_review->driver_neatness, 1) }})</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($rejected_review->attitude || $rejected_review->knowledge || $rejected_review->explanation || $rejected_review->time_control || $rejected_review->guide_neatness)
                                                        <div class="col-md-6">
                                                            <p><b>Guide</b> ({{ $rejected_review->guide_name }})</p>
                                                            <p>
                                                                @if ($rejected_review->attitude)
                                                                    <span class="text-muted "data-toggle="tooltip" title="Attitude">▸ A({{ number_format($rejected_review->attitude, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->knowledge)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Knowledge">▸ K({{ number_format($rejected_review->knowledge, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->explanation)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Explanation">▸ E({{ number_format($rejected_review->explanation, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->time_control)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Time Control">▸ T({{ number_format($rejected_review->time_control, 1) }})</span>
                                                                @endif
                                                                @if ($rejected_review->guide_neatness)
                                                                    <span class="text-muted" data-toggle="tooltip" title="Neatness">▸ N({{ number_format($rejected_review->guide_neatness, 1) }})</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-3 text-right align-self-end">
                                                <form id="formRejectedApprove{{ $rejected_review->id }}" method="POST" action="{{ route('admin.reviews.updateStatus', $rejected_review->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                </form>
                                                <button type="submit" form="formRejectedApprove{{ $rejected_review->id }}" class="btn btn-success" onclick="return confirm('Approve this review?')">
                                                    <i class="fa fa-check-circle" aria-hidden="true"></i> Approve
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach

                                <div class="mt-3">
                                    {{ $rejectedReviews->appends(['tab' => 'rejectedReviews'])->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script>
        $(document).ready(function(){
            $(".nav-tabs a").click(function(){
                $(this).tab('show');
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Ambil nilai query string
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');

            if (tab) {
                $('.nav-tabs a[href="#' + tab + '"]').tab('show');
            }

            // Tambahkan handler agar tab yang diklik tetap update URL-nya
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                const newTab = $(e.target).attr('href').substring(1);
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', newTab);
                history.replaceState(null, '', newUrl);
            });
        });
    </script>
@endsection
