@if (count($weddings) > 0)
    <div class="trans-box m-b-18">
        <div class="trans-box-title">
            <i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i>@lang('messages.Weddings')
        </div>
        <div class="trans-box-content">
            @foreach ($weddings as $wedding)
                @php
                    $w_hotel = $wedding->hotels;
                    $weddingInclude = substr($wedding->include, 0, 100);
                @endphp
                <div class="trans-card">
                    <a href="/wedding-hotel-{{ $w_hotel->code }}#weddingPackage">
                        <div class="trans-img-container">
                            <img src="{{ asset('storage/weddings/wedding-cover/' . $wedding->cover) }}" class="img-fluid rounded thumbnail-image">
                            {{-- <div class="trans-card-title-b-0">{{ $wedding->name }}</div> --}}
                            
                        </div>
                    </a>
                    <div class="trans-img-container">
                        <div class="front-image-title">
                            <p>{{ $wedding->name }}</p>
                        </div>
                        <div class="top-lable">
                            <i class="icon-copy fa fa-map-marker"></i> <p>{{ $w_hotel->name }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="trans-box-footer">
            <a href="/weddings">
                <button class="btn btn-primary text-white"> @lang('messages.Weddings') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif