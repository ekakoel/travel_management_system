@if (count($tours) > 0)
    <div class="trans-box">
        <div class="trans-box-title"><i class="fa fa-suitcase"></i> @lang('messages.'.$titleTour)</div>
        <div class="trans-box-content">
            @foreach ($tours as $tour)
                <div class="trans-card anim-feed-up">
                    <a href="/tour-{{ $tour->code }}">
                        <div class="trans-img-container">
                            @php
                                $tourInclude = substr($tour->include, 0, 100);
                            @endphp
                            <img src="{{ asset('storage/tours/tours-cover/' . $tour->cover) }}" class="img-fluid rounded thumbnail-image">
                            <div class="trans-card-title-b-0">{{ $tour->name }}</div>
                            <div class="trans-card-overlay">
                                <i class="icon-copy dw dw-map-5"></i>
                                <div class="overlay-title">{{ $tour->name }}</div>
                                <p>{!! $tour->destinations !!}</p>
                                <div class="overlay-title">{{ $tour->duration }}</div>
                                <p>@lang('messages.Include') :</p>
                                <p>{!! $tourInclude !!}...</p>
                            </div>
                        </div>
                        <div class="trans-lable-container">
                            <div class="trans-lable">
                                <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                <p>{{ $tour->location }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="trans-box-footer">
            <a href="/tours">
                <button class="btn btn-primary text-white"> @lang('messages.All Tour Packages') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif
