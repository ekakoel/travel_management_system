@if (count($hotels) > 0)
    <div class="card-box m-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="dw dw-hotel"></i> @lang("messages.Recent Hotel")</div>
        </div>
        <div class="card-box-content">
            @foreach ($hotels as $hotel)
                <div class="card">
                    <div class="image-container">
                        <div class="first">
                            <ul class="card-lable">
                                <li class="item">
                                    <div class="meta-box">
                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                        <a target="__blank" href="{{ $hotel->map }}">
                                            <p class="text">{{ $hotel->region }}</p>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <a href="hotel-{{ $hotel->code }}">
                        <img src="{{ asset('storage/hotels/hotels-cover/' . $hotel->cover) }}" class="img-fluid rounded thumbnail-image">
                            <div class="card-detail-title">{{ $hotel->name }}</div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-box-footer">
            <a href="/hotels">
                <button class="btn btn-primary text-white"> @lang('messages.All Hotels') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif