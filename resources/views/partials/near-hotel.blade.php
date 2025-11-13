<div class="col-md-12">
    <div class="card-box">
        <div class="pages-subtitle">
            @lang('messages.Hotels Around') {{ $hotel->region }}
            <span>@lang('messages.Availability') {{ count($nearhotels) }}</span>
        </div>
        <div class="card-box-content">
            @foreach ($nearhotels as $hotel)
                <div class="card">
                    @if ($hotel->promos->isNotEmpty())
                        <div class="persen-promo" data-toggle="tooltip" data-placement="top" title="Promotion" aria-hidden="true">
                            <img src="{{ asset('storage/icon/persen.png') }}" alt="Promo discount" loading="lazy">
                        </div>
                    @endif
                    <div class="image-container">
                        <div class="first">
                            <ul class="card-lable">
                                <li class="item">
                                    <div class="meta-box">
                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                        <p class="text">{{ $hotel->region }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('view.hotel-detail',$hotel->code) }}">
                            <img src="{{ getThumbnail('/hotels/hotels-cover/' . $hotel->cover,380,200) }}"  class="img-fluid rounded thumbnail-image">
                            <div class="card-detail-title">{{ $hotel->name }}</div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>