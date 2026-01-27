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
                                    <p><strong>@lang('messages.Agent'):</strong><i> {{ $temporary_link['agent']?? "-"; }}</i></p>
                                    <p><strong>@lang('messages.Booking Code'):</strong><i> {{ $bookingCode??"-"; }}</i></p>
                                    <p><strong>@lang('messages.Arrival Date'):</strong><i> {{ $temporary_link->arrival_date?? "-"; }}</i></p>
                                    <p><strong>@lang('messages.Departure Date'):</strong><i> {{ $temporary_link->departure_date??"-"; }}</i></p>
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
                                            @if ($averageRatings['accommodation'] > 0 || $averageRatings['meals'] > 0 || $averageRatings['tour_sites'] > 0)
                                                <h4 class="subheading-h4">@lang('messages.General Services')</h4>
                                                <ul class="list-unstyled">
                                                    @if ($averageRatings['accommodation'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Accommodation')</span>
                                                            <span>{!! renderStars($averageRatings['accommodation']) !!} ({{ number_format($averageRatings['accommodation'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['meals'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Meals')</span>
                                                            <span>{!! renderStars($averageRatings['meals']) !!} ({{ number_format($averageRatings['meals'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['tour_sites'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Tour Sites')</span>
                                                            <span>{!! renderStars($averageRatings['tour_sites']) !!} ({{ number_format($averageRatings['tour_sites'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                            @if ($averageRatings['transportation_cleanliness'] > 0 || $averageRatings['transportation_air_condition'] > 0 )
                                                <h4 class="subheading-h4">@lang('messages.Transportation')</h4>
                                                <ul class="list-unstyled">
                                                    @if ($averageRatings['transportation_cleanliness'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Cleanliness')</span>
                                                            <span>{!! renderStars($averageRatings['transportation_cleanliness']) !!} ({{ number_format($averageRatings['transportation_cleanliness'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['transportation_air_condition'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Air Conditioner')</span>
                                                            <span>{!! renderStars($averageRatings['transportation_air_condition']) !!} ({{ number_format($averageRatings['transportation_air_condition'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                            @if ($averageRatings['driver_punctuality'] > 0 || $averageRatings['driver_driving_skills'] > 0 || $averageRatings['driver_neatness'] > 0)
                                                <h4 class="subheading-h4">@lang('messages.Driver')</h4>
                                                <ul class="list-unstyled">
                                                    @if ($averageRatings['driver_punctuality'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Punctuality')</span><span>{!! renderStars($averageRatings['driver_punctuality']) !!} ({{ number_format($averageRatings['driver_punctuality'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['driver_driving_skills'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Driving Skills')</span><span>{!! renderStars($averageRatings['driver_driving_skills']) !!} ({{ number_format($averageRatings['driver_driving_skills'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['driver_neatness'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Neatness')</span><span>{!! renderStars($averageRatings['driver_neatness']) !!} ({{ number_format($averageRatings['driver_neatness'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        </div>
                                        @if ($averageRatings['attitude'] > 0 || $averageRatings['explanation'] > 0 || $averageRatings['knowledge'] > 0 || $averageRatings['time_control'] > 0 || $averageRatings['guide_neatness'] > 0)
                                            <div class="col-md-6">
                                                <h4 class="subheading-h4">@lang('messages.Tour Guide')</h4>
                                                <ul class="list-unstyled">
                                                    @if ($averageRatings['attitude'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Attitude')</span><span>{!! renderStars($averageRatings['attitude']) !!} ({{ number_format($averageRatings['attitude'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['explanation'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Explanation')</span><span>{!! renderStars($averageRatings['explanation']) !!} ({{ number_format($averageRatings['explanation'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['knowledge'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Knowledge')</span><span>{!! renderStars($averageRatings['knowledge']) !!} ({{ number_format($averageRatings['knowledge'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['time_control'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Time Control')</span><span>{!! renderStars($averageRatings['time_control']) !!} ({{ number_format($averageRatings['time_control'], 1) }})</span>
                                                        </li>
                                                    @endif
                                                    @if ($averageRatings['guide_neatness'] > 0)
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Neatness')</span><span>{!! renderStars($averageRatings['guide_neatness']) !!} ({{ number_format($averageRatings['guide_neatness'], 1) }})</span> 
                                                        </li>
                                                    @endif
                                                </ul>
                                                @if ($averageMoodScore)
                                                    <h4 class="subheading-h4">@lang('messages.Travel Mood')</h4>
                                                    <ul class="list-unstyled">
                                                        <li class="d-flex justify-content-between">
                                                            <span>@lang('messages.Average Travel Mood')</span>
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
                                        @endif
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
                                    <a href="{{ route('admin.reviews.index') }}">
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
