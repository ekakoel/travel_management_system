@extends('layouts.head')
@section('title', __('messages.Tours'))
@section('content')
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
                        <form id="search-form">
                            @csrf
                            <div class="search-container flex-end">
                                <div class="form-group">
                                    <select name="tour_type" class="form-control" id="tour_type">
                                        <option selected value="">@lang('messages.Search by type')</option>
                                        @foreach ($toursType as $type)
                                            <option value="{{ $type->id }}">
                                                {!! $type->$typeField !!}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="search-item text-right">
                                    <button type="submit" class="btn-search btn-primary">
                                        <i class='icon-copy fa fa-search' aria-hidden='true'></i> 
                                        @lang('messages.Search')
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- Tour List --}}
                        <div class="card-box-content" id="tour-list">
                            @include('frontend.tours.partials.tour-list', ['tours' => $tours, 'typeField' => $typeField, 'tourNameField' => $tourNameField])
                        </div>
                        {{-- Load More Button --}}
                        @if($tours->hasMorePages())
                            <div class="text-center mt-3 mb-3">
                                <button id="load-more" class="btn btn-primary" data-page="2">
                                    @lang('messages.Load More')
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
    <script>
        $(document).ready(function() {
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                $('#tour-list').html('<p>Loading...</p>'); 
                loadTours(1, true);
            });
    
            $('#load-more').on('click', function() {
                let page = $(this).data('page');
                loadTours(page, false);
            });
    
            function loadTours(page, isSearch = false) {
                let formData = $('#search-form').serialize();
                $.ajax({
                    url: "{{ route('tours.load-more') }}?page=" + page,
                    type: "GET",
                    data: formData,
                    beforeSend: function() {
                        $('#load-more').text("Loading...").prop('disabled', true);
                    },
                    success: function(response) {
                        if (isSearch) {
                            $('#tour-list').html('');
                            $('#load-more').data('page', 2);
                        }
    
                        let newContent = $(response.html);
                        $('#tour-list').append(newContent);
                        $('#tour-list .tour-card').each(function(index) {
                            let element = $(this);
                            setTimeout(function() {
                                element.css({
                                    "opacity": 1,
                                    "transform": "translateY(0)"
                                }).fadeIn();
                            }, 100 * index);
                        });
                        if (!response.hasMore) {
                            $('#load-more').hide();
                        } else {
                            $('#load-more').show().text("Load More").prop('disabled', false);
                            $('#load-more').data('page', page + 1);
                        }
                    }
                });
            }
        });
    </script>
@endsection
