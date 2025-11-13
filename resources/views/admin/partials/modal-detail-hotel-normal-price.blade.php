<div class="modal fade" id="detail-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="dw dw-eye"></i> Price {{ $room->rooms }}
                </div>
                <div class="card-box-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">
                                        Hotel Name
                                    </div>
                                    <div class="card-text">
                                        <p>{{ $hotel->name }}</p>
                                    </div>
                                    <div class="card-subtitle">
                                        Room
                                    </div>
                                    <div class="card-text">
                                        <p>{{ $room->rooms }}</p>
                                    </div>
                                    <div class="card-subtitle">
                                        Stay Period Start
                                    </div>
                                    <div class="card-text">
                                        <p>{{ dateFormat($price->start_date) }}</p>
                                    </div>
                                    <div class="card-subtitle">
                                        Stay Period End
                                    </div>
                                    <div class="card-text">
                                        <p>{{ dateFormat($price->end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">
                                        Contract Rate
                                    </div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($price->contractRate($usdrates)) !!}</p>
                                        <p>{{ currencyFormatIdr($price->contract_rate) }}</p>
                                    </div>
                                    <div class="card-subtitle">
                                        Markup
                                    </div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($price->markup) !!}</p>
                                        <p>{{ currencyFormatIdr($price->markup * $usdrates->rate) }}</p>
                                    </div>
                                    @if ($price->kick_back > 0)
                                        <div class="card-subtitle">
                                            Kick Back
                                        </div>
                                        <div class="card-text">
                                            <p class="rate-kick-back">{!! currencyFormatUsd($price->kick_back) !!}</p>
                                            <p>{{ currencyFormatIdr($price->kick_back * $usdrates->rate) }}</p>
                                        </div>
                                    @endif
                                    <div class="card-subtitle">
                                        Tax <span>({{ $tax->tax }}%)</span>
                                    </div>
                                    <div class="card-text">
                                        @php
                                            $price_tax = $price->taxRate($usdrates, $tax);
                                        @endphp
                                        <p class="rate-usd">{!! currencyFormatUsd($price->taxRate($usdrates, $tax)) !!}</p>
                                        <p>{{ currencyFormatIdr( $price_tax * $usdrates->rate) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($price->kick_back >0)
                            @php
                                $publish_rate = $price->calculatePrice($usdrates,$tax);
                            @endphp
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="card-subtitle">
                                            Published Rate /night
                                        </div>
                                        <div class="price-usd">
                                            <strike>{!! currencyFormatUsd($publish_rate) !!}</strike><br>
                                        </div>
                                        {{ currencyFormatIdr($publish_rate * $usdrates->rate) }}
                                    </div>
                                    <div class="col-6">
                                        <div class="card-subtitle">
                                            Published With Kick Back Rate /night
                                        </div>
                                        <div class="price-usd">
                                            {!! currencyFormatUsd($publish_rate - $price->kick_back) !!}<br>
                                        </div>
                                        {{ currencyFormatIdr(($publish_rate - $price->kick_back) * $usdrates->rate) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="card-subtitle">
                                    Published Rate
                                </div>
                                <div class="price-usd">
                                    {!! currencyFormatUsd($price->calculatePrice($usdrates,$tax)) !!}
                                </div>
                                <div class="price-idr">
                                    {{ currencyFormatIdr($price->calculatePrice($usdrates,$tax) * $usdrates->rate) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-box-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>