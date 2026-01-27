<div class="col-md-12">
    <div class="card-box">
        <div class="pages-subtitle">
            @lang('messages.Private Villas Around') {{ $villa->region }}
            <span>@lang('messages.Availability') {{ count($nearvillas) }}</span>
        </div>
        <div class="card-box-content">
            @foreach ($nearvillas as $villa)
                <div class="card">
                    <div class="image-container">
                        <div class="first">
                            <ul class="card-lable">
                                <li class="item">
                                    <div class="meta-box">
                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                        <p class="text">{{ $villa->region }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <a href="/villas/{{ $villa->code }}">
                            <img src="{{ asset('storage/villas/covers/' . $villa->cover) }}"  class="img-fluid rounded thumbnail-image">
                            <div class="card-detail-title">{{ $villa->name }}</div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>