@php
    use App\Models\UsdRates;
    $usdrates = UsdRates::where('name','USD')->first();
    $cnyrates = UsdRates::where('name','CNY')->first();
    $twdrates = UsdRates::where('name','TWD')->first();
@endphp
<div class="col-md-12 m-b-18">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-money" aria-hidden="true"></i> Currency</div>
        </div>
        <div class="grid-3-container">
            <div class="grid-box">
                <div class="grid-box-title">USD <span>($)</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($usdrates->sell) }} <span>S</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($usdrates->buy) }} <span>B</span></div>
            </div>
            <div class="grid-box">
                <div class="grid-box-title">CNY <span>(¥)</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($cnyrates->sell) }} <span>S</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($cnyrates->buy) }} <span>B</span></div>
            </div>
            <div class="grid-box">
                <div class="grid-box-title">TWD <span>(NT$)</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($twdrates->sell) }} <span>S</span></div>
                <div class="grid-box-content">{{ currencyFormatIdr($twdrates->buy) }} <span>B</span></div>
            </div>
        </div>
    </div>
</div>