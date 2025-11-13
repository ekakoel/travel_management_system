@section('title','Reviews')
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-8">
                    <div class="print-area">
                        <div class="print-heading">
                            <div class="card print-hidden mb-4">
                                <div class="card-body">
                                    <h1>@lang('messages.Review Summary')</h1>
                                    <p><strong>@lang('messages.Wedding Organizer'):</strong><i> {{ $temporary_link['wedding_organizer']?? "-"; }}</i></p>
                                    <p><strong>@lang('messages.Booking Code'):</strong><i> {{ $bookingCode??"-"; }}</i></p>
                                    <p><strong>@lang('messages.Wedding Date'):</strong><i> {{ $temporary_link->wedding_date?? "-"; }}</i></p>
                                    <p><strong>@lang('messages.Total Reviews'):</strong><i> {{ count($reviews) }} {{ count($reviews)>1?__('messages.reviews'):__('messages.review'); }}</i></p>
                                </div>
                            </div>
                        </div>
                        @if($reviews->isEmpty())
                            <div class="alert alert-warning">@lang('messages.No reviews found for this booking code.')</div>
                        @else
                            <div class="card print-hidden mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <strong>@lang('messages.Services')</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="subheading-h4">@lang('messages.General Services')</h4>
                                            <ul class="list-unstyled">
                                                @if ($averageRatings['communication_efficiency'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Accommodation')</span>
                                                        <span>{!! renderStars($averageRatings['communication_efficiency']) !!} ({{ number_format($averageRatings['communication_efficiency'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['workflow_planning'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Workflow Planning')</span>
                                                        <span>{!! renderStars($averageRatings['workflow_planning']) !!} ({{ number_format($averageRatings['workflow_planning'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['material_preparation'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Materials Preparation')</span>
                                                        <span>{!! renderStars($averageRatings['material_preparation']) !!} ({{ number_format($averageRatings['material_preparation'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['service_attitude'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Service Attitude')</span>
                                                        <span>{!! renderStars($averageRatings['service_attitude']) !!} ({{ number_format($averageRatings['service_attitude'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['execution_of_workflow'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Execution of Workflow')</span>
                                                        <span>{!! renderStars($averageRatings['execution_of_workflow']) !!} ({{ number_format($averageRatings['execution_of_workflow'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['time_management'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Time Management')</span>
                                                        <span>{!! renderStars($averageRatings['time_management']) !!} ({{ number_format($averageRatings['time_management'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['guest_care'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Guest Care')</span>
                                                        <span>{!! renderStars($averageRatings['guest_care']) !!} ({{ number_format($averageRatings['guest_care'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['team_coordination'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Team Coordination')</span>
                                                        <span>{!! renderStars($averageRatings['team_coordination']) !!} ({{ number_format($averageRatings['team_coordination'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['third_party_coordination'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Third Party Coordination')</span>
                                                        <span>{!! renderStars($averageRatings['third_party_coordination']) !!} ({{ number_format($averageRatings['third_party_coordination'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['problem_solving_ability'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Problem Solving Ability')</span>
                                                        <span>{!! renderStars($averageRatings['problem_solving_ability']) !!} ({{ number_format($averageRatings['problem_solving_ability'], 1) }})</span>
                                                    </li>
                                                @endif
                                                @if ($averageRatings['wrap_up_and_item_check'] > 0)
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Wrap up and Item Check')</span>
                                                        <span>{!! renderStars($averageRatings['wrap_up_and_item_check']) !!} ({{ number_format($averageRatings['wrap_up_and_item_check'], 1) }})</span>
                                                    </li>
                                                @endif
                                            </ul>
                                            
                                        </div>
                                        
                                        <div class="col-md-6">
                                            @if ($averageMoodScore)
                                                <h4 class="subheading-h4">@lang('messages.Customer Mood')</h4>
                                                <ul class="list-unstyled">
                                                    <li class="d-flex justify-content-between">
                                                        <span>@lang('messages.Average Customer Mood')</span>
                                                        <span>
                                                            @if ($averageMoodScore >= 4)
                                                                <span data-toggle="tooltip" title="Very Satisfied" >😊</span>
                                                            @elseif ($averageMoodScore >= 3)
                                                                <span data-toggle="tooltip" title="Satisfied">🙂</span>
                                                            @elseif ($averageMoodScore >= 2)
                                                                <span data-toggle="tooltip" title="Neutral">😐</span>
                                                            @elseif ($averageMoodScore >= 1)
                                                                <span data-toggle="tooltip" title="Need Improvement">🙁</span>
                                                            @endif
                                                            <strong>{{ __('messages.'.$averageMoodLabel) }}</strong>
                                                        </span>
                                                    </li>
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($reviews->whereNotNull('customer_review')->count())
                                <div class="card print-hidden mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>@lang('messages.Customer Reviews')</strong>
                                    </div>
                                    <div class="card-body">
                                        @foreach($reviews as $review)
                                            @if($review->customer_review)
                                                <p>
                                                    @if ($review->travel_mood == "Very Satisfied")
                                                        <span data-toggle="tooltip" title="Very Satisfied" >😊</span>
                                                    @elseif ($review->travel_mood == "Satisfied")
                                                        <span data-toggle="tooltip" title="Satisfied">🙂</span>
                                                    @elseif ($review->travel_mood == "Neutral")
                                                        <span data-toggle="tooltip" title="Neutral">😐</span>
                                                    @elseif ($review->travel_mood == "Need Improvement")
                                                        <span data-toggle="tooltip" title="Need Improvement">🙁</span>
                                                    @endif
                                                    
                                                    <strong>
                                                        {{ $review->customer_name }}:
                                                    </strong> 
                                                    <em>
                                                        {{ $review->customer_review }}
                                                    </em>
                                                </p>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between float-right mb-4">
                                <div>
                                    <button onclick="window.print()" class="btn btn-primary me-2">
                                        <i class="bi bi-printer"></i> @lang('messages.Print')
                                    </button>
                                    <a href="{{ route('admin.wedding-reviews.index') }}">
                                        <button class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> @lang('messages.Cancel')
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>
@endsection
