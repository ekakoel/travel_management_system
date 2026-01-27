    <div class="counter-container">
        @if (count($allactivehotels) > 0)
            <a href="hotels">
                <div class="widget">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="chart-icon">
                            <i class="icon-copy dw dw-hotel" aria-hidden="true"></i>
                        </div>
                        <div class="widget-data">
                            <div class="widget-data-title">{{ $allactivehotels->count() }} @lang("messages.Hotels")</div>
                            <div class="widget-data-subtitle">@lang("messages.Available")</div>
                        </div>
                    </div>
                </div>
            </a>
        @endif
        @if (count($ctpackage) > 0)
            <a href="tours">
                <div class="widget">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="chart-icon">
                            <i class="icon-copy fa fa-suitcase" aria-hidden="true"></i>
                        </div>
                        <div class="widget-data">
                            <div class="widget-data-title">{{ $ctpackage->count() }} @lang("messages.Tours")
                            </a></div>
                            <div class="widget-data-subtitle">@lang("messages.Available")</div>
                        </div>
                    </div>
                </div>
            </a>
        @endif
        @if (count($cactivities) > 0)
            <a href="activities">
                <div class="widget">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="chart-icon">
                            <i class="icon-copy fa fa-child" aria-hidden="true"></i>
                        </div>
                        <div class="widget-data">
                            <div class="widget-data-title">{{ $cactivities->count() }} @lang("messages.Activities")</div>
                            <div class="widget-data-subtitle">@lang("messages.Available")</div>
                        </div>
                    </div>
                </div>
            </a>
        @endif
        @if (count($ctransports) > 0)
            <a href="transports">
                <div class="widget">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="chart-icon">
                            <i class="icon-copy fa fa-bus" aria-hidden="true"></i>
                        </div>
                        <div class="widget-data">
                            <div class="widget-data-title">{{ $ctransports->count() }} @lang("messages.Transports")</div>
                            <div class="widget-data-subtitle">@lang("messages.Available")</div>
                        </div>
                    </div>
                </div>
            </a>
        @endif
    </div>

