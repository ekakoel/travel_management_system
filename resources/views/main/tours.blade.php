@section('title', __('messages.Tours'))
@section('content')
    @extends('layouts.head')
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
                                        <div class="text-card">
                                            {{ date('d M y', strtotime($promotion->periode_start)) . ' - ' . date('d M y', strtotime($promotion->periode_end)) }}
                                        </div>
                                    </div>
                                    <div class="content-card-promo">
                                        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top"
                                            title='@lang('messages.Ongoing promotion'){{ ' ' . $promotion->name . ' ' }}@lang('messages.and get discounts'){{ " $" . $promotion->discounts . ' ' }}@lang('messages.until'){{ ' ' . date('d M y', strtotime($promotion->periode_end)) }}'><i
                                                class="fa fa-question" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="dw dw-map-2"></i> @lang('messages.Tour Package')</div>
                        </div>
                        <form action="/search-tours" method="POST" role="search">
                            {{ csrf_field() }}
                            <div class="search-container flex-end">
                                <div class="search-item">
                                    <select name="tour_location" class="custom-select" value="{{ old('tour_location') }}">
                                        <option value="">@lang('messages.Search by location')</option>
                                        @php $t_lc = ""; @endphp
                                        @foreach ($tours as $t_location)
                                            @if ($t_location->location !== $t_lc)
                                                <option value="{{ $t_location->location }}">{{ $t_location->location }}</option>
                                                @php $t_lc = $t_location->location; @endphp
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="search-item">
                                    <select name="tour_type" class="custom-select" value="{{ old('tour_type') }}">
                                        <option value="">@lang('messages.Search by type')</option>
                                        @php $t_ty = ""; @endphp
                                        @foreach ($tours as $t_type)
                                            @if ($t_type->type !== $t_ty)
                                                <option value="{{ $t_type->type }}">{{ $t_type->type }}</option>
                                                @php $t_ty = $t_type->type; @endphp
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="search-item">
                                    <input type="text" style="text-transform: uppercase;" class="form-control"
                                        name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                </div>
                                <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search'
                                        aria-hidden='true'></i> @lang('messages.Search')</button>
                            </div>
                        </form>
                        <div class="card-box-content">
                            @foreach ($tours as $tour)
                                <div class="card">
                                    <a href="tour-{{ $tour->code }}">
                                        <div class="image-container">
                                            <a href="{{ route('view.tour-detail',$tour->code) }}">
                                                <img src="{{ asset('storage/tours/tours-cover/' . $tour->cover) }}"
                                                    class="img-fluid rounded thumbnail-image">
                                            </a>
                                            <a href="tour-{{ $tour->code }}">
                                                <div class="card-detail-title">{{ $tour->name }}</div>
                                            </a>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination">
                            <div class="pagination-panel">
                                {{ $tours->links() }}
                            </div>
                            <div class="pagination-desk">
                                {{ $tours->total() }} <span>@lang('messages.Tours Available')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection
