<div class="modal fade" id="detail-promo-{{ $promo->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="dw dw-eye"></i> Promo
                    <div class="status-card m-t-8 right-18">
                        @include('partials.status-icon', ['status' => $promo->status])
                    </div>
                </div>
                <div class="card-box-body">
                    <div class="row">
                        @if ($promo->book_periode_end < $now)
                            <div class="col-12 col-sm-5">
                                <div class="expired-ico">
                                    <img src="{{ asset ('storage/icon/expired.png') }}" alt="{{ $promo->name }}" loading="lazy">
                                </div>
                            </div>
                        @endif
                        @if ($promo->booking_code != "")
                            <div class="col-6">
                                <div class="card-subtitle">Booking Code</div>
                                <div class="card-text">
                                    <p>
                                        {{ $promo->booking_code }}
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
                                        {{  currencyFormatIdr(ceil($usdrates->rate)) }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <hr class="form-hr">
                        </div>
                        <div class="col-6 col-sm-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">Name</div>
                                    <div class="card-text">
                                        <p>{{ $promo->name }}</p>
                                    </div>
                                    <div class="card-subtitle">Room</div>
                                    <div class="card-text">
                                        <p>{{ $promo->rooms->rooms }}</p>
                                    </div>
                                    <div class="card-subtitle">Minimum Stay</div>
                                    <div class="card-text">
                                        <p>{{ $promo->minimum_stay.' Nights' }}</p>
                                    </div>
                                    <div class="card-subtitle">Booking Period</div>
                                    <div class="card-text">
                                        <p>{{ dateFormat($promo->book_periode_start)." - ". dateFormat($promo->book_periode_end) }}</p>
                                    </div>
                                    <div class="card-subtitle">Stay Period</div>
                                    <div class="card-text">
                                        <p>{{ dateFormat($promo->periode_start)." - ". dateFormat($promo->periode_end) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-subtitle">Contract Rate</div>
                                    <div class="card-text">
                                        <p class="rate-usd m-l-18">{!! currencyFormatUsd(ceil($promo->contract_rate / $usdrates->rate)) !!}</p>
                                        <p>{{ currencyFormatIdr($promo->contract_rate) }}</p>
                                    </div>
                                    <div class="card-subtitle">Markup</div>
                                    <div class="card-text">
                                        <p class="rate-usd m-l-18">{!! currencyFormatUsd($promo->markup) !!}</p>
                                        <p>{{ currencyFormatIdr($promo->markup * $usdrates->rate) }}</p>
                                    </div>
                                    <div class="card-subtitle">Tax</div>
                                    <div class="card-text">
                                        <p class="rate-usd">{!! currencyFormatUsd($promo->calculateTax($usdrates,$tax)) !!}</p>
                                        <p>{{ currencyFormatIdr($promo->calculateTax($usdrates,$tax) * $usdrates->rate) }}</p>
                                    </div>
                                </div>
                            </div>       
                        </div>
                        @if (isset($promo->include) or isset($promo->benefits) or isset($promo->additional_info))
                            <div class="col-12">
                                <hr class="form-hr">
                            </div>
                            <div class="col-12 ">
                                @if (app()->getLocale() == 'zh')
                                    @if (isset($promo->include_traditional))
                                        <div class="card-subtitle">@lang('messages.Include')</div>
                                        <p>{!! $promo->include_traditional !!}</p>
                                    @endif
                                    @if (isset($promo->benefits_traditional))
                                        <div class="card-subtitle">@lang('messages.Benefits')</div>
                                        <p>{!! $promo->benefits_traditional !!}</p>
                                    @endif
                                    @if (isset($promo->additional_info_traditional))
                                        <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                        <p>{!! $promo->additional_info_traditional !!}</p>
                                    @endif
                                @elseif (app()->getLocale() == 'zh-CN')
                                    @if (isset($promo->include_simplified))
                                        <div class="card-subtitle">@lang('messages.Include')</div>
                                        <p>{!! $promo->include_simplified !!}</p>
                                    @endif
                                    @if (isset($promo->benefits_simplified))
                                        <div class="card-subtitle">@lang('messages.Benefits')</div>
                                        <p>{!! $promo->benefits_simplified !!}</p>
                                    @endif
                                    @if (isset($promo->additional_info_simplified))
                                        <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                        <p>{!! $promo->additional_info_simplified !!}</p>
                                    @endif
                                @else
                                    @if (isset($promo->include))
                                        <div class="card-subtitle">@lang('messages.Include')</div>
                                        <div class="card-text">
                                            <p>{!! $promo->include !!}</p>
                                        </div>
                                    @endif
                                    @if (isset($promo->benefits))
                                        <div class="card-subtitle">@lang('messages.Benefits')</div>
                                        <div class="card-text">
                                            <p>{!! $promo->benefits !!}</p>
                                        </div>
                                    @endif
                                    @if (isset($promo->additional_info))
                                        <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                        <div class="card-text">
                                            <p>{!! $promo->additional_info !!}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                        <div class="col-12">
                            <hr class="form-hr">
                        </div>
                        <div class="col-12">
                            <div class="card-subtitle">Published Rate</div>
                        </div>
                        <div class="col-12 price-usd m-b-8">
                            {!! currencyFormatUsd($promo->calculatePrice($usdrates,$tax)) !!}
                        </div>
                        <div class="col-12 m-b-8">
                            <p>{{ currencyFormatIdr($promo->calculatePrice($usdrates,$tax) * $usdrates->rate) }}</p>
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