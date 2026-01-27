@if (count($transports) > 0)
    <div class="trans-box">
        <div class="trans-box-title"><i class="icon-copy ion-android-car"></i>@lang('messages.' . $titleTransport)</div>
        <div class="trans-box-content">
            @foreach ($transports as $transport)
                <div class="trans-card">
                    <a href="/transport-{{ $transport->code }}">
                        <div class="trans-img-container">
                            @php
                                $transportInclude = substr($transport->include, 0, 100);
                            @endphp
                            <img src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}"
                                class="img-fluid rounded thumbnail-image">
                            {{-- <div class="trans-card-title-b-0">{{ $transport->name }}</div> --}}
                            {{-- <div class="trans-card-overlay">
                                <i class="icon-copy dw dw-car"></i>
                                <p>{!! $transport->destinations !!}</p>
                                <div class="overlay-title">{{ $transport->duration }}</div>
                                <p>@lang('messages.Include') :</p>
                                <p>{!! $transportInclude !!}...</p>
                            </div> --}}
                            <div class="image-title-bottom">
                                <p>{{ $transport->name }}</p>
                            </div>
                        </div>
                    </a>
                    <div class="trans-lable-container">
                        <div class="trans-lable">
                            @if ($transport->type == 'Car')
                                <p><i class="icon-copy ion-ios-person"></i> {{ $transport->capacity . ' ' }}
                                @lang('messages.Seat')</p>
                            @elseif ($transport->type == 'Micro Bus')
                                <p><i class="icon-copy ion-ios-person"></i>
                                {{ $transport->capacity . ' ' }}@lang('messages.Seat')</p>
                            @elseif ($transport->type == 'Bus')
                                <p><i class="icon-copy ion-ios-person"></i>
                                {{ $transport->capacity . ' ' }}@lang('messages.Seat')</p>
                            @elseif ($transport->type == 'Luxuri')
                                <p><i class="icon-copy ion-ios-person"></i>
                                {{ $transport->capacity . ' ' }}@lang('messages.Seat')</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="trans-box-footer">
            <a href="/transports">
                <button class="btn btn-primary text-white"> @lang('messages.Transportation') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif
