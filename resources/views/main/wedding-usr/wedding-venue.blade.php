{{-- WEDDING VENUE --}}
<div id="vendor" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="dw dw-hotel" aria-hidden="true"></i> {{ $hotel->name }}</div>
    </div>
    <div class="page-card">
        <div class="card-banner">
            <img src="{{ asset ('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->name }}" loading="lazy">
        </div>
        <div class="card-content">
            <div class="card-text">
                <div class="row">
                    <div class="col-6">
                        <div class="card-subtitle">@lang('messages.Address')</div>
                        <p>{{ $hotel->address }}</p>
                    </div>
                    <div class="col-6">
                        <div class="card-subtitle">@lang('messages.Region')</div>
                        <a target="__blank" href="{{ $hotel->map }}">
                            <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>{{ " ". $hotel->region }}</p>
                        </a>
                    </div>
                </div>
                @if (isset($hotel->check_in_time) and isset($hotel->check_out_time))
                    <div class="row">
                        @if (isset($hotel->check_in_time))
                            <div class="col-6">
                                <div class="card-subtitle">@lang('messages.Check-in')</div>
                                <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_in_time)) }}</p>
                            </div>
                        @endif
                        @if (isset($hotel->check_out_time))
                            <div class="col-6">
                                <div class="card-subtitle">@lang('messages.Check-out')</div>
                                <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_out_time)) }}</p>
                            </div>
                        @endif
                    </div>
                @endif
                @if (isset($hotel->airport_distance) and isset($hotel->airport_duration))
                    <div class="row">
                        @if (isset($hotel->airport_distance))
                            <div class="col-6">
                                <div class="card-subtitle">@lang('messages.Airport Distance')</div>
                                <p><i class="fa fa-map" aria-hidden="true"></i> {{ $hotel->airport_distance." Km" }}</p>
                            </div>
                        @endif
                        @if (isset($hotel->airport_duration))
                            <div class="col-6">
                                <div class="card-subtitle">@lang('messages.Airport Duration')</div>
                                <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $hotel->airport_duration." Hours" }}</p>
                            </div>
                        @endif
                        {{-- <div class="col-12">
                            <div class="card-subtitle"> @lang('messages.Wedding Brochure')
                                <span data-toggle="tooltip" data-placement="top" title="@lang('messages.You can easily view or download the available wedding package brochure by simply clicking the file link below')">
                                    <i class="icon-copy fi-info"></i>
                                </span>
                            </div>
                            @foreach ($brochures as $brochure)
                                <a href="#" data-toggle="modal" data-target="#wedding-brochure-{{ $brochure->id }}">
                                    ❖ {{ $brochure->name }} (Period: {{ dateFormat($brochure->period_start)." - ".dateFormat($brochure->period_end) }}) <i class="icon-copy fa fa-download" aria-hidden="true"></i>
                                </a><br>
                                <div class="modal fade" id="wedding-brochure-{{ $brochure->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                            <div class="modal-body pd-5">
                                                <embed src="storage/hotels/wedding-contract/{{ $brochure->file_name }}" frameborder="10" width="100%" height="850px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div> --}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>