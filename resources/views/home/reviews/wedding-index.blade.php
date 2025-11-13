@section('title','Reviews')
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="card-box">

                <div class="card-box-title">Wedding Reviews</div>
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
                    <div class="col-md-6">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">🏨 General Service</div>
                            <div class="card-body">
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Communication Efficiency</span>
                                    <span>{!! renderStars($weddingStats->communication_efficiency) !!} ({{ number_format($weddingStats->communication_efficiency, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Workflow Planning	</span>
                                    <span>{!! renderStars($weddingStats->workflow_planning	) !!} ({{ number_format($weddingStats->workflow_planning	, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Material Preparation</span>
                                    <span>{!! renderStars($weddingStats->material_preparation) !!} ({{ number_format($weddingStats->material_preparation, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Service Attitude</span>
                                    <span>{!! renderStars($weddingStats->service_attitude) !!} ({{ number_format($weddingStats->service_attitude, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Execution of Workflow</span>
                                    <span>{!! renderStars($weddingStats->execution_of_workflow) !!} ({{ number_format($weddingStats->execution_of_workflow, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Time Management</span>
                                    <span>{!! renderStars($weddingStats->time_management) !!} ({{ number_format($weddingStats->time_management, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Guest Care</span>
                                    <span>{!! renderStars($weddingStats->guest_care) !!} ({{ number_format($weddingStats->guest_care, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Team Coordination</span>
                                    <span>{!! renderStars($weddingStats->team_coordination) !!} ({{ number_format($weddingStats->team_coordination, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Third Party Coordination</span>
                                    <span>{!! renderStars($weddingStats->third_party_coordination) !!} ({{ number_format($weddingStats->third_party_coordination, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Problem Solving Ability</span>
                                    <span>{!! renderStars($weddingStats->problem_solving_ability) !!} ({{ number_format($weddingStats->problem_solving_ability, 1) }})</span>
                                </p>
                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Wrap up and Item Check</span>
                                    <span>{!! renderStars($weddingStats->wrap_up_and_item_check) !!} ({{ number_format($weddingStats->wrap_up_and_item_check, 1) }})</span>
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
                                                    <strong>Wedding Date</strong><br>
                                                    <p class="ph-1">{{ dateFormat($review->arrival_date) }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <strong>Bride</strong><br>
                                                    <p class="ph-1">{{ $review->groom." ❤️ ".$review->bride }}</p>
                                                </div>
                                            </div>
                                            <div class="content-title my-2">
                                                Wedding Organizer: {{ $review->wedding_organizer }}
                                            </div>
                                            
                                            <div class="row">
                                                @if ($review->communication_efficiency)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Comunication Efficiency
                                                        <div class="ph-1">
                                                            {!! renderStars($review->communication_efficiency) !!} 
                                                            <span class="text-muted">({{ number_format($review->communication_efficiency, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->workflow_planning)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Workflow Planning
                                                        <div class="ph-1">
                                                            {!! renderStars($review->workflow_planning) !!} 
                                                            <span class="text-muted">({{ number_format($review->workflow_planning, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->material_preparation)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Material Preparation
                                                        <div class="ph-1">
                                                            {!! renderStars($review->material_preparation) !!} 
                                                            <span class="text-muted">({{ number_format($review->material_preparation, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->service_attitude)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Service Attitude
                                                        <div class="ph-1">
                                                            {!! renderStars($review->service_attitude) !!} 
                                                            <span class="text-muted">({{ number_format($review->service_attitude, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->execution_of_workflow)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Execution of Workflow
                                                        <div class="ph-1">
                                                            {!! renderStars($review->execution_of_workflow) !!} 
                                                            <span class="text-muted">({{ number_format($review->execution_of_workflow, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->time_management)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Time Management
                                                        <div class="ph-1">
                                                            {!! renderStars($review->time_management) !!} 
                                                            <span class="text-muted">({{ number_format($review->time_management, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->guest_care)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Guest Care
                                                        <div class="ph-1">
                                                            {!! renderStars($review->guest_care) !!} 
                                                            <span class="text-muted">({{ number_format($review->guest_care, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->team_coordination)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Team Coordination
                                                        <div class="ph-1">
                                                            {!! renderStars($review->team_coordination) !!} 
                                                            <span class="text-muted">({{ number_format($review->team_coordination, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->third_party_coordination)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Third Party Coordination
                                                        <div class="ph-1">
                                                            {!! renderStars($review->third_party_coordination) !!} 
                                                            <span class="text-muted">({{ number_format($review->third_party_coordination, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->problem_solving_ability)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Problem Solving Ability
                                                        <div class="ph-1">
                                                            {!! renderStars($review->problem_solving_ability) !!} 
                                                            <span class="text-muted">({{ number_format($review->problem_solving_ability, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($review->wrap_up_and_item_check)
                                                    <div class="col-md-2 inline-flex-space-between m-b-18">
                                                        Wrap up and Item Check
                                                        <div class="ph-1">
                                                            {!! renderStars($review->wrap_up_and_item_check) !!} 
                                                            <span class="text-muted">({{ number_format($review->wrap_up_and_item_check, 1) }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <hr class="my-1">
                                            <div class="row">
                                                <div class="col-md-2 inline-flex-space-between">
                                                    Customer Mood
                                                    <div class="ph-1">
                                                        @if ($review->couple_mood == 'Very Satisfied')
                                                            <span data-toggle="tooltip" title="Very Satisfied" >😊</span>
                                                        @elseif ($review->couple_mood == 'Satisfied')
                                                            <span data-toggle="tooltip" title="Satisfied">🙂</span>
                                                        @elseif ($review->couple_mood == 'Neutral')
                                                            <span data-toggle="tooltip" title="Neutral">😐</span>
                                                        @elseif ($review->couple_mood == 'Need Improvement')
                                                            <span data-toggle="tooltip" title="Need Improvement">🙁</span>
                                                        @endif
                                                        {{ $review->couple_mood }}
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
                                            <form id="formApprove{{ $review->id }}" method="POST" action="{{ route('admin.wedding-reviews.updateStatus', $review->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                            </form>
                                            <form id="formReject{{ $review->id }}" method="POST" action="{{ route('admin.wedding-reviews.updateStatus', $review->id) }}">
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
                                @foreach ($groupedApproved as $booking_code => $approve_reviews)
                                    <div class="content-title justify-content-between mb-2">
                                        <strong>{{ $booking_code ?? 'Unknown Booking Code' }}</strong>
                                        <div class="d-flex float-right">
                                            <a href="{{ route('wedding-reviews.print', ['bookingCode' => $approve_reviews->first()->booking_code]) }}" class="btn-print">
                                                🖨️ Print
                                            </a>
                                        </div>
                                    </div>
                                    @foreach($approve_reviews as $approve_review)
                                        <div class="border-bottom ml-2 pb-2 mb-2 row review-printable" id="review-{{ $approve_review->id }}">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p><strong>{{ $approve_review->customer_name ?? 'Anonymous' }}</strong> <small class="text-muted">at wedding of {{ $approve_review->groom." & ".$approve_review->bride }} ({{ $approve_review->created_at->format('d M Y') }})</small></p>
                                                        @if ($approve_review->customer_review)
                                                            <p><i>"{!! $approve_review->customer_review !!}"</i></p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12">
                                                        <p>
                                                            @if ($approve_review->communication_efficiency)
                                                                <span class="text-muted" data-toggle="tooltip" title="Communication Efficiency">▸CE({{ number_format($approve_review->communication_efficiency, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->workflow_planning)
                                                                <span class="text-muted" data-toggle="tooltip" title="Workflow Planning">▸WP({{ number_format($approve_review->workflow_planning, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->material_preparation)
                                                                <span class="text-muted" data-toggle="tooltip" title="Material Preparation">▸MP({{ number_format($approve_review->material_preparation, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->service_attitude)
                                                                <span class="text-muted" data-toggle="tooltip" title="Service Attitude">▸SA({{ number_format($approve_review->service_attitude, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->execution_of_workflow)
                                                                <span class="text-muted" data-toggle="tooltip" title="Execution of Work Flow">▸EWF({{ number_format($approve_review->execution_of_workflow, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->time_management)
                                                                <span class="text-muted" data-toggle="tooltip" title="Time Management">▸TM({{ number_format($approve_review->time_management, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->guest_care)
                                                                <span class="text-muted" data-toggle="tooltip" title="Guest Care">▸GC({{ number_format($approve_review->guest_care, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->team_coordination)
                                                                <span class="text-muted" data-toggle="tooltip" title="Team Coordination">▸TC({{ number_format($approve_review->team_coordination, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->third_party_coordination)
                                                                <span class="text-muted" data-toggle="tooltip" title="Third Party Coordinationt">▸TPC({{ number_format($approve_review->third_party_coordination, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->problem_solving_ability)
                                                                <span class="text-muted" data-toggle="tooltip" title="Problem Solving Ability">▸PSA({{ number_format($approve_review->problem_solving_ability, 1) }})</span>
                                                            @endif
                                                            @if ($approve_review->wrap_up_and_item_check)
                                                                <span class="text-muted" data-toggle="tooltip" title="Wrap up and Item Check">▸WIC({{ number_format($approve_review->wrap_up_and_item_check, 1) }})</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-3 text-right align-self-end">
                                                <form id="formApproveReject{{ $approve_review->id }}" method="POST" action="{{ route('admin.wedding-reviews.updateStatus', $approve_review->id) }}">
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
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p><strong>{{ $rejected_review->customer_name ?? 'Anonymous' }}</strong> <small class="text-muted">at wedding of {{ $rejected_review->groom." & ".$rejected_review->bride }} ({{ $rejected_review->created_at->format('d M Y') }})</small></p>
                                                        @if ($rejected_review->customer_review)
                                                            <p><i>"{!! $rejected_review->customer_review !!}"</i></p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12">
                                                        <p>
                                                            @if ($rejected_review->communication_efficiency)
                                                                <span class="text-muted" data-toggle="tooltip" title="Communication Efficiency">▸CE({{ number_format($rejected_review->communication_efficiency, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->workflow_planning)
                                                                <span class="text-muted" data-toggle="tooltip" title="Workflow Planning">▸WP({{ number_format($rejected_review->workflow_planning, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->material_preparation)
                                                                <span class="text-muted" data-toggle="tooltip" title="Material Preparation">▸MP({{ number_format($rejected_review->material_preparation, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->service_attitude)
                                                                <span class="text-muted" data-toggle="tooltip" title="Service Attitude">▸SA({{ number_format($rejected_review->service_attitude, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->execution_of_workflow)
                                                                <span class="text-muted" data-toggle="tooltip" title="Execution of Work Flow">▸EWF({{ number_format($rejected_review->execution_of_workflow, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->time_management)
                                                                <span class="text-muted" data-toggle="tooltip" title="Time Management">▸TM({{ number_format($rejected_review->time_management, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->guest_care)
                                                                <span class="text-muted" data-toggle="tooltip" title="Guest Care">▸GC({{ number_format($rejected_review->guest_care, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->team_coordination)
                                                                <span class="text-muted" data-toggle="tooltip" title="Team Coordination">▸TC({{ number_format($rejected_review->team_coordination, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->third_party_coordination)
                                                                <span class="text-muted" data-toggle="tooltip" title="Third Party Coordinationt">▸TPC({{ number_format($rejected_review->third_party_coordination, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->problem_solving_ability)
                                                                <span class="text-muted" data-toggle="tooltip" title="Problem Solving Ability">▸PSA({{ number_format($rejected_review->problem_solving_ability, 1) }})</span>
                                                            @endif
                                                            @if ($rejected_review->wrap_up_and_item_check)
                                                                <span class="text-muted" data-toggle="tooltip" title="Wrap up and Item Check">▸WIC({{ number_format($rejected_review->wrap_up_and_item_check, 1) }})</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-2 text-right align-self-end">
                                                <form id="formRejectedApprove{{ $rejected_review->id }}" method="POST" action="{{ route('admin.wedding-reviews.updateStatus', $rejected_review->id) }}">
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
    <script>
        $(document).ready(function(){
            $(".nav-tabs a").click(function(){
                $(this).tab('show');
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                $('.nav-tabs a[href="#' + tab + '"]').tab('show');
            }
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                const newTab = $(e.target).attr('href').substring(1);
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', newTab);
                history.replaceState(null, '', newUrl);
            });
        });
    </script>
@endsection
