@if (count($activities) > 0)
    <div class="trans-box">
        <div class="trans-box-title"><i class="icon-copy fa fa-child"></i>@lang('messages.' . $titleActivity)</div>
        <div class="trans-box-content">
            @foreach ($activities as $activity)
                <div class="trans-card anim-feed-up">
                    <a href="/activity-{{ $activity->code }}">
                        <div class="trans-img-container">
                            @php
                                $activityInclude = substr($activity->include, 0, 100);
                            @endphp
                            <img src="{{ asset('storage/activities/activities-cover/' . $activity->cover) }}"
                                class="img-fluid rounded thumbnail-image">
                            <div class="trans-card-title-b-0">{{ $activity->name }}</div>
                            <div class="trans-card-overlay">
                                <i class="icon-copy dw dw-position"></i>
                                <p>{!! $activity->destinations !!}</p>
                                <div class="overlay-title">{{ $activity->location }} - {{ $activity->name }}</div>
                                <p>@lang('messages.Include') :</p>
                                <p>{!! $activityInclude !!}...</p>
                            </div>
                        </div>
                        <div class="trans-lable-container">
                            <div class="trans-lable">
                                <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                <p>{{ $activity->location }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="trans-box-footer">
            <a href="/activities">
                <button class="btn btn-primary text-white"> @lang('messages.All Activities') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif
