{{-- MODAL DETAIL PACKAGE =========================================================================================--}}
<div class="modal fade" id="detail-package-{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="dw dw-eye"></i> Package
                    <div class="status-card m-t-8">
                        @include('partials.status-icon', ['status' => $package->status])
                    </div>
                </div>
                <div class="card-box-body">
                    <div class="row">
                        @if ($package->booking_code != "")
                            <div class="col-6">
                                <div class="card-subtitle">Booking Code</div>
                                <div class="card-text">
                                    <p>
                                        {{ $package->booking_code }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6 text-left flex-end">
                                <div class="card-subtitle">USD Rate</div>
                                <div class="card-text">
                                    <p class="rate-usd">
                                        {{  currencyFormatIdr(ceil($usdrates->rate)) }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="card-subtitle">USD Rate</div>
                                <div class="card-text">
                                    <p class="rate-usd">
                                        {!! currencyFormatUsd(ceil($usdrates->rate)) !!}
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <hr class="form-hr">
                        </div>
                        <div class="col-12 col-sm-6 m-b-8">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">Name</div>
                                    <div class="card-text">
                                        <p class="card-text">
                                            {{ $package->name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">Room</div>
                                    <div class="card-text">
                                        <p class="card-text">
                                            {{ $room->rooms }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">Stay Period Start</div>
                                    <div class="card-text">
                                        <p class="card-text">
                                            {{ dateFormat($package->stay_period_start) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">Stay Period End</div>
                                    <div class="card-text">
                                        <p class="card-text">
                                            {{ dateFormat($package->stay_period_end) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">Minimum Stay</div>
                                    <div class="card-text">
                                        <p class="card-text">
                                            {{ $package->duration." Night" }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">Contract Rate</div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($package->calculatePrice($usdrates,$tax)) !!}</p>
                                        <p>{{ currencyFormatIdr($package->contract_rate) }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">Markup</div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($package->markup) !!}</p>
                                        <p>{{ currencyFormatIdr(($package->markup * $usdrates->rate)) }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-subtitle">TAX</div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($package->calculateTax($usdrates,$tax)) !!}</p>
                                        <p>{{ currencyFormatIdr($package->calculateTax($usdrates,$tax) * $usdrates->rate) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if ($package->include != "" or $package->benefits != "" or $package->additional_info != "")
                            <div class="col-12">
                                <hr class="form-hr">
                            </div>
                            <div class="col-12 ">
                                @if ($package->include != "")
                                    <div class="card-subtitle">Include</div>
                                    <div class="card-text">
                                        <p>{!! $package->include !!}</p>
                                    </div>
                                @endif
                                @if ($package->benefits != "")
                                    <div class="card-subtitle">Benefits</div>
                                    <div class="card-text">
                                        <p>{!! $package->benefits !!}</p>
                                    </div>
                                @endif
                                @if ($package->additional_info != "")
                                    <div class="card-subtitle">Additional Information</div>
                                    <div class="card-text">
                                        <p>{!! $package->additional_info !!}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="col-12">
                            <hr class="form-hr">
                        </div>
                        <div class="col-12">
                            <div class="card-subtitle">Published Rate</div>
                        </div>
                        <div class="col-12 price-usd">
                            {!! currencyFormatUsd($package->calculatePrice($usdrates,$tax)) !!}
                        </div>
                        <div class="col-12 m-b-8">
                            {{ currencyFormatIdr($package->calculatePrice($usdrates,$tax) * $usdrates->rate) }}
                        </div>
                    </div>
                </div>
                <div class="card-box-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>