@section('title', __('messages.Transports'))
@section('content')
    @extends('layouts.head')
{{-- @include('component.sysload') --}}
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-12">
                    <div class="promotion-bookingcode">
                        @if (isset($promotions))
                            @foreach ($promotions as $promotion)
                                <div class="bookingcode-card">
                                    <div class="icon-card promotion">
                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                    </div>
                                    <div class="content-card">
                                        <div class="code">{{ $promotion->name }}</div>
                                        <div class="text-card">@lang('messages.Promo Period')</div>
                                        <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                    </div>
                                    <div class="content-card-promo">
                                        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-car" aria-hidden="true"></i> @lang("messages.Transport")</div>
                    </div>
                    <form action="/search-transports" method="POST" role="search";>
                        {{ csrf_field() }}
                        <div class="search-container flex-end">
                            <div class="search-item">
                                <input type="text" class="form-control" name="brand" placeholder="@lang('messages.Search by brand')" value="{{ old('brand') }}">
                            </div>
                            <div class="search-item">
                                <select name="type" value="{{ old('type') }}" class="custom-select col-12 @error('type') is-invalid @enderror">
                                    <option selected value="">@lang('messages.Search by type')</option>
                                    @foreach ($type as $type)
                                        <option value="{{ $type->type }}">@lang('messages.'.$type->type)</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="search-item">
                                <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                            </div>
                            <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Search')</button>
                        </div>
                    </form>
                    <div class="card-box-content">
                        @foreach ($transports as $transport)
                            <div class="card">
                                <div class="image-container">
                                    <div class="first">
                                        <div class="card-lable">
                                            <div class="meta-box">
                                                <p><i class="icon-copy ion-ios-person"></i> {{ $transport->capacity. " " }}@lang('messages.Seat')</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/transport-{{ $transport->code }}">
                                        <img src="{{ getThumbnail('/transports/transports-cover/' . $transport->cover,320,200) }}" class="img-fluid rounded thumbnail-image">
                                        <div class="front-image-title">
                                            <p>{{ $transport->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>


    {{-- <div class="container mt-4">
    <h1 class="text-center mb-4 fw-bold">Daftar Transportasi</h1>

    <!-- Section Promosi -->
    @if($promotions->isNotEmpty())
    <div class="row mb-4">
        @foreach($promotions as $promo)
            <div class="col-md-4">
                <div class="alert alert-primary text-center">
                    <strong>{{ $promo->title }}</strong> - {{ $promo->discount }}% OFF
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Grid of Transport Cards -->
    <div class="row">
        @foreach($transports as $transport)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ getThumbnail($transport->cover,320,200) }}" class="card-img-top" alt="{{ $transport->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $transport->name }}</h5>
                        <p class="text-muted">{{ $transport->brand }} - {{ ucfirst($transport->type) }}</p>
                        <p><strong>Kapasitas:</strong> {{ $transport->capacity }} orang</p>
                        <p class="text-success fw-bold">Harga: Rp {{ number_format($transport->prices->first()->contract_rate ?? 0, 0, ',', '.') }}</p>
                        <a href="{{ route('transport.show', $transport->id) }}" class="btn btn-primary mt-auto">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $transports->links('pagination::bootstrap-5') }}
    </div>
</div> --}}




{{-- <div class="main-container">
    <div class="pd-ltr-20">
        <h1 class="text-center mb-4 fw-bold">Daftar Transportasi</h1>

        <!-- Section Promosi -->
        @if($promotions->isNotEmpty())
        <div class="row mb-4">
            @foreach($promotions as $promo)
                <div class="col-md-4">
                    <div class="alert alert-primary text-center">
                        <strong>{{ $promo->title }}</strong> - {{ $promo->discount }}% OFF
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Grid of Transport Cards -->
        <div class="row">
            @foreach($transports as $transport)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ getThumbnail('/transports/transports-cover/' . $transport->cover,320,200) }}" class="card-img-top" alt="{{ $transport->name }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $transport->name }}</h5>
                            <p class="text-muted">{{ $transport->brand }} - {{ ucfirst($transport->type) }}</p>
                            <p><strong>Kapasitas:</strong> {{ $transport->capacity }} orang</p>
                            <p class="text-success fw-bold">Harga: Rp {{ number_format($transport->prices->first()->contract_rate ?? 0, 0, ',', '.') }}</p>
                            <a href="{{ route('view.transport-detail', $transport->code) }}" class="btn btn-primary mt-auto">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $transports->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div> --}}


{{-- <div class="main-container">
    <div class="pd-ltr-20">
    <h1 class="text-center mb-4 fw-bold text-primary">Daftar Transportasi</h1>

    <!-- Section Promosi -->
    @if($promotions->isNotEmpty())
    <div class="row mb-4">
        @foreach($promotions as $promo)
            <div class="col-md-4">
                <div class="alert alert-warning text-center fw-bold shadow-sm">
                    🎉 <strong>{{ $promo->title }}</strong> - {{ $promo->discount }}% OFF!
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Grid Transport -->
    <div class="row">
        @foreach($transports as $transport)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative">
                        <img src="{{ getThumbnail('/transports/transports-cover/' . $transport->cover,320,200) }}" class="card-img-top transport-img" alt="{{ $transport->name }}">
                        <div class="overlay">
                            <div class="text-white text-center fw-bold">Lihat Detail</div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-dark fw-bold">{{ $transport->name }}</h5>
                        <p class="text-muted small">{{ $transport->brand }} - {{ ucfirst($transport->type) }}</p>
                        <p class="text-secondary"><strong>Kapasitas:</strong> {{ $transport->capacity }} orang</p>
                        <p class="text-success fw-bold">Harga: Rp {{ number_format($transport->prices->first()->contract_rate ?? 0, 0, ',', '.') }}</p>
                        <a href="{{ route('view.transport-detail', $transport->code) }}" class="btn btn-primary mt-auto rounded-pill">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $transports->links('pagination::bootstrap-5') }}
    </div>
    </div>
</div>

<style>
    /* Efek gambar lebih modern */
    .transport-img {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease-in-out;
    }
    .transport-img:hover {
        transform: scale(1.05);
    }

    /* Overlay efek saat hover */
    .position-relative {
        overflow: hidden;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s ease-in-out;
    }
    .position-relative:hover .overlay {
        opacity: 1;
    }

    /* Border-radius dan efek shadow */
    .card {
        transition: box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
</style> --}}
@endsection
