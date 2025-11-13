@extends('layouts.head')

@section('title', __('messages.Hotels'))

@section('content')
<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="row">
            {{-- Promotion Section --}}
            @if($promotions->isNotEmpty())
                <div class="col-md-12">
                    <div class="promotion-bookingcode">
                        @foreach ($promotions as $promotion)
                            @include('partials.promotion-card', ['promotion' => $promotion])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Hotel Section --}}
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle">
                            <i class="icon-copy dw dw-hotel"></i> @lang('messages.Hotel')
                        </div>
                    </div>
                    <form id="search-form">
                        @csrf
                        <div class="search-container flex-end p-b-8">
                            <div class="search-item position-relative">
                                <input type="text" class="form-control" id="hotel_name" name="hotel_name" 
                                    placeholder="@lang('messages.Search by name')">
                                <div id="hotel-suggestions" class="suggestion-box"></div>
                            </div>
                            <div class="search-item position-relative">
                                <input type="text" class="form-control" id="hotel_region" name="hotel_region" 
                                    placeholder="@lang('messages.Search by region')">
                                <div id="region-suggestions" class="suggestion-box"></div>
                            </div>
                            <div class="search-item text-right">
                                <button type="submit" class="btn-search btn-primary">
                                    <i class='icon-copy fa fa-search' aria-hidden='true'></i> 
                                    @lang('messages.Search')
                                </button>
                            </div>
                        </div>
                    </form>
                    {{-- Hotel List --}}
                    <div class="card-box-content" id="hotel-list">
                        @include('partials.hotel-list', ['hotels' => $hotels])
                    </div>

                    {{-- Load More Button --}}
                    @if($hotels->hasMorePages())
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
</div>
<script>
    $(document).ready(function() {
        // Autocomplete untuk nama hotel
        $('#hotel_name').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#hotel-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('hotels.autocomplete') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    let suggestions = response.hotels;
                    let dropdown = $('#hotel-suggestions');
                    dropdown.html('');

                    if (suggestions.length > 0) {
                        suggestions.forEach(hotel => {
                            if (hotel.name) {
                                dropdown.append(`<div class="suggestion-item hotel-item" data-name="${hotel.name}">${hotel.name}</div>`);
                            }
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        });

        // Autocomplete untuk region
        $('#hotel_region').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#region-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('hotels.autocompleteRegion') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    let suggestions = response.regions;
                    let dropdown = $('#region-suggestions');
                    dropdown.html('');

                    if (suggestions.length > 0) {
                        suggestions.forEach(region => {
                            if (region.region) {
                                dropdown.append(`<div class="suggestion-item region-item" data-name="${region.region}">${region.region}</div>`);
                            }
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        });

        // Pilih hotel dari dropdown
        $(document).on('click', '.hotel-item', function() {
            $('#hotel_name').val($(this).data('name'));
            $('#hotel-suggestions').hide();
        });

        // Pilih region dari dropdown
        $(document).on('click', '.region-item', function() {
            $('#hotel_region').val($(this).data('name'));
            $('#region-suggestions').hide();
        });

        // Sembunyikan dropdown jika klik di luar
        $(document).click(function(e) {
            if (!$(e.target).closest('.search-item').length) {
                $('#hotel-suggestions').hide();
                $('#region-suggestions').hide();
            }
        });
    });
</script>





<script>
    $(document).ready(function() {
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            $('#hotel-list').html('<p>Loading...</p>'); 
            loadHotels(1, true);
        });

        $('#load-more').on('click', function() {
            let page = $(this).data('page');
            loadHotels(page, false);
        });

        function loadHotels(page, isSearch = false) {
            let formData = $('#search-form').serialize();
            $.ajax({
                url: "{{ route('hotels.load-more') }}?page=" + page,
                type: "GET",
                data: formData,
                beforeSend: function() {
                    $('#load-more').text("Loading...").prop('disabled', true);
                },
                success: function(response) {
                    if (isSearch) {
                        $('#hotel-list').html('');
                        $('#load-more').data('page', 2);
                    }

                    let newContent = $(response.html);
                    $('#hotel-list').append(newContent);
                    $('#hotel-list .hotel-card').each(function(index) {
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
