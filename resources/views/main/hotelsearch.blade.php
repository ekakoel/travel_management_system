 @section('title', __('messages.Search Hotels'))
 @section('content')
 @extends('layouts.head')
    @include('component.sysload')

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="title">
                    <i class="icon-copy fa fa-search"></i> @lang('messages.Search Hotels')
                </div>
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                        ['url' => route('view.hotels'), 'label' => __('messages.Hotels')],
                        ['label' => __('Search Hotels')],
                    ]
                ])
            </div>
            @include('partials.alerts')
            @if ($promotions->isNotEmpty())
                <div class="promotion-bookingcode">
                    @foreach ($promotions as $promotion)
                        @include('partials.promotion-card', ['promotion' => $promotion])
                    @endforeach
                </div>
            @endif
            <div class="card-box">
                <form action="{{ route('view.hotels-search') }}" method="POST">
                    @csrf
                    <div class="search-container flex-end">
                        <div class="search-item">
                            <input type="text" class="form-control" name="hotel_name" 
                                   placeholder="@lang('messages.Search by name')" 
                                   value="{{ old('hotel_name', request('hotel_name')) }}">
                        </div>
                        <div class="search-item">
                            <input type="text" class="form-control" name="hotel_region" 
                                   placeholder="@lang('messages.Search by region')" 
                                   value="{{ old('hotel_region', request('hotel_region')) }}">
                        </div>
                        <button type="submit" class="btn-search btn-primary">
                            <i class="icon-copy fa fa-search"></i> @lang('messages.Search')
                        </button>
                    </div>
                </form>

                <div class="card-box-content">
                    @forelse ($hotels as $hotel)
                        @include('partials.hotel-card', ['hotel' => $hotel])
                    @empty
                        <div class="info-container">
                            <div class="info-title">
                                <i class="icon-copy fa fa-exclamation"></i> 
                                @lang('messages.The hotel you were looking for was not found, please try with other keywords')!
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
