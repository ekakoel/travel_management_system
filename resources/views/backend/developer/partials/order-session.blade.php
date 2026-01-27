<div class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"> <i class="icon-copy fa fa-tag" aria-hidden="true"></i>Orders</div>
    </div>
    <div class="card-box-content">
        
        @if (count($validorders) > 0)
            <div class="widget-panel">
                <div class="subtitle m-b-8">
                    Valid Order {{ "(".count($validorders).")" }}
                    <hr class="form-hr">
                </div>
                <div class="widget-data">
                    <div class="rate-usd-panel m-0">{{ currencyFormatUsd($total_price_valid_order) }}</div>
                    <hr class="form-hr">
                    <p>{{ dateFormat($mindate)." - ".dateFormat($maxdate) }}</p>
                </div>
            </div>
        @endif
       
        @if (count($activeorders) > 0)
            <div class="widget-panel">
                <div class="chart-icon-active">
                    <i class="icon-copy fa fa-tag" aria-hidden="true"></i>
                </div>
                <div class="widget-data">
                    <div class="widget-data-title">
                    Confirmed Order
                    </div>
                    <div class="widget-data-subtitle">
                    <p>{{ count($activeorders)." Orders" }}<br>
                    {{ "Total (".currencyFormatUsd($total_price_active_order).")" }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (count($pendingorders) > 0)
            <div class="widget-panel">
                <div class="chart-icon-pending">
                    <i class="icon-copy fa fa-tag" aria-hidden="true"></i>
                </div>
                <div class="widget-data">
                    <div class="widget-data-title">
                    Pending Order
                    </div>
                    <div class="widget-data-subtitle">
                    <p>{{ count($pendingorders)." Orders" }}<br>
                        {{ "Total (".currencyFormatUsd($total_price_pending_order).")" }}
                    </p>
                    </div>
                </div>
            </div>
        @endif
        @if (count($invalidorders) > 0)
            <div class="widget-panel">
                <div class="chart-icon-invalid">
                    <i class="icon-copy fa fa-tag" aria-hidden="true"></i>
                </div>
                <div class="widget-data">
                    <div class="widget-data-title">
                    Invalid Order
                    </div>
                    <div class="widget-data-subtitle">
                    <p>{{ count($invalidorders)." Orders" }}<br>
                    {{ "Total (".currencyFormatUsd($total_price_invalid_order).")" }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (count($rejectedorders) > 0)
            <div class="widget-panel">
                <div class="chart-icon-rejected">
                    <i class="icon-copy fa fa-tag" aria-hidden="true"></i>
                </div>
                <div class="widget-data">
                    <div class="widget-data-title">
                    Rejected Order
                    </div>
                    <div class="widget-data-subtitle">
                    <p>{{ count($rejectedorders)." Orders" }}<br>
                    {{ "Total (".currencyFormatUsd($total_price_rejected_order).")" }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>