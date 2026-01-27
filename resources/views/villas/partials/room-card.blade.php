<div class="card">
    <a href="#" data-toggle="modal" data-target="#detail-room-{{ $room->id }}">
        <div class="card-image-container">
            <img 
                class="img-fluid rounded thumbnail-image" 
                src="{{ asset('storage/villas/rooms/' . $room->cover) }}" 
                alt="{{ $room->name }}">
            <div class="card-detail-title">{{ $room->name }}</div>
        </div>
    </a>
    @php
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
    @endphp
    <!-- Modal -->
    <div class="modal fade" id="detail-room-{{ $room->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle">
                            <i class="icon-copy fa fa-hotel" aria-hidden="true"></i> 
                            {{ $room->name }}
                        </div>
                    </div>
                    <div class="card-banner m-b-8">
                        <img 
                            class="rounded" 
                            src="{{ asset('storage/villas/rooms/' . $room->cover) }}" 
                            alt="{{ $room->name }}" 
                            loading="lazy">
                    </div>

                    <!-- Room Details -->
                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-text">
                                    @if (config('app.locale') == "zh")
                                        {!! trim($room->description_traditional) !!}
                                    @elseif (config('app.locale') == "zh-CN")
                                        {!! trim($room->description_simplified) !!}
                                    @else
                                        {!! trim($room->description) !!}
                                    @endif
                                </div>
                            </div>
                            @foreach ([
                                'messages.Size' => $room->size ? $room->size . ' m<sup>2</sup>' : null,
                                'messages.Capacity' => $room->guest_adult . " ".($room->guest_adult > 1 ? __('messages.Adults') : __('messages.Adult')) . ($room->guest_child > 0 ? ' + ' . $room->guest_child ." ". __('messages.Child') : ''),
                                'messages.Bed Type' => __('messages.'.$room->bed_type), 
                                'messages.View' => __('messages.'.$room->view),
                            ] as $label => $value)
                                @if ($value)
                                    <div class="col-md-6">
                                        <div class="card-text">
                                            <div class="card-subtitle">@lang($label)</div>
                                            <p>{!! $value !!}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-md-12">
                                <hr class="form-hr">
                            </div>
                            @if ($room->amenities)
                                <div class="col-md-12">
                                    <div class="card-subtitle">@lang('messages.Amenities')</div>
                                    <ul>
                                        @if (config('app.locale') == "zh")
                                            <p>{!! trim($room->amenities_traditional) !!}</p>
                                        @elseif (config('app.locale') == "zh-CN")
                                            <p>{!! trim($room->amenities_simplified) !!}</p>
                                        @else
                                            <p>{!! trim($room->amenities) !!}</p>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
