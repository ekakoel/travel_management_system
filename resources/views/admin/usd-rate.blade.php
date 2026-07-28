@php
    use App\Models\UsdRates;
    $usdrates = UsdRates::where('name','USD')->first();
    $cnyrates = UsdRates::where('name','CNY')->first();
    $twdrates = UsdRates::where('name','TWD')->first();
@endphp
<section class="backend-panel hotel-form-panel">
    <div class="backend-section-header hotel-form-panel__heading">
        <div>
            <span class="backend-section-header__label">Currency</span>
        </div>
    </div>
    <div class="hotel-form-panel__body">
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
</section>
