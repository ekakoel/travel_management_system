
@if ($attentions)
    @if (count($attentions)>0)
        <div class="col-md-12 m-b-18">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="subtitle"><i class="icon-copy ion-alert-circled"></i> @lang('messages.Attention')</div>
                </div>
                <div class="banner-right">
                    <ul class="attention">
                        @if (config('app.locale') == "zh")
                            @foreach ($attentions as $attention)
                                <li><p><b>"@lang('messages.'.$attention->name)"</b> {{ $attention->attention_zh }}</p></li>
                            @endforeach
                        @else
                            @foreach ($attentions as $attention)
                                <li><p><b>"@lang('messages.'.$attention->name)"</b> {{ $attention->attention_en }}</p></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endif