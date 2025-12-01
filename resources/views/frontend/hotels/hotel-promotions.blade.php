@extends('frontend.layouts.header')
@section('title', __('messages.Hotels'))
@section('content')
    <div class="body-container">
        <section id="hotelPromos">
            <nav class="breadcrumb-nav text-center">
                <ol class="breadcrumb-list">
                    <li><a href="{{ route('dashboard.index') }}">@lang('messages.Dashboard')</a></li>
                    <li class="active">@lang('messages.Hotels')</li>
                </ol>
            </nav>
            <div class="heading-page-hotels">
                <div class="heading-page-content">
                    <h1>
                        @lang('messages.Hotels') - @lang('messages.Resorts') - @lang('messages.Villas')<br>
                        @lang('messages.Promotions')
                    </h1>
                </div>
                
            </div>
            <div class="content-container">
                <div class="pd-ltr-20">
                    <div class="row">
                        <div class="col-md-12 py-3">
                            <div class="filter-container">
                                <div class="filter-box">
                                    <form id="search-form" class="form-filter">
                                        @csrf
                                        <div class="search-container d-flex align-items-center gap-2 flex-wrap">
                                            <div class="form-search">
                                                <input type="text" class="form-control" id="hotel_name" name="hotel_name" 
                                                    placeholder="@lang('messages.Search by name')...">
                                                <div id="hotel-suggestions" class="suggestion-box"></div>
                                            </div>
                                            <div class="form-search">
                                                <input type="text" class="form-control" id="hotel_region" name="hotel_region" 
                                                    placeholder="@lang('messages.Search by region')...">
                                                <div id="region-suggestions" class="suggestion-box"></div>
                                            </div>
                                            <div class="text-right d-flex gap-2">
                                                <button type="button" id="clear-btn" class="btn btn-danger padding-btn">
                                                    <i class='icon-copy fa fa-times' aria-hidden='true'></i> 
                                                    @lang('messages.Clear')
                                                </button>
                                                <button type="submit" class="btn btn-primary padding-btn">
                                                    <i class='icon-copy fa fa-search' aria-hidden='true'></i> 
                                                    @lang('messages.Search')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 py-3">
                            <div class="card-grid-container" id="hotel-promo-list">
                                @include('frontend.hotels.partials.hotel-promo-list', ['hotels' => $hotels])
                                
                            </div>
                            <div class="text-center mt-9" id="load-more-promo-container">
                                @if($hotels->hasMorePages())
                                    <button id="load-more-promo" class="btn btn-primary" data-page="2">
                                        @lang('messages.Load More')
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            $(document).ready(function() {
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
                        url: "{{ route('hotel-promotions.autocomplete') }}",
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
            

                // Saat form search disubmit
                $('#search-form').on('submit', function(e) {
                    e.preventDefault();
                    $('#hotel-promo-list').html('<p class="text-center">Loading...</p>');
                    loadHotels(1, true); // mode search baru
                });

                // Saat klik tombol Load More
                $(document).on('click', '#load-more-promo', function() {
                    let page = $(this).data('page');
                    loadHotels(page, false); // mode load tambahan
                });

                // Fungsi utama load data hotel via AJAX
                function loadHotels(page, isSearch = false) {
                    let formData = $('#search-form').serialize();

                    $.ajax({
                        url: "{{ route('frontend.hotels.promo-load-more') }}?page=" + page,
                        type: "GET",
                        data: formData,
                        beforeSend: function() {
                            $('#load-more-promo').text("Loading...").prop('disabled', true);
                        },
                        success: function(response) {
                            // Kalau mode pencarian baru, reset hasil lama
                            if (isSearch) {
                                $('#hotel-promo-list').html('');
                                $('#load-more-promo').data('page', 2); // reset ke page 2
                            }

                            // Tambahkan hasil baru di bawah yang sudah ada
                            let newContent = $(response.html);
                            $('#hotel-promo-list').append(newContent.hide().fadeIn(300));

                            // Animasi kartu baru (opsional)
                            newContent.find('.hotel-card').each(function(index) {
                                $(this).css({opacity: 0, transform: 'translateY(20px)'}).delay(100 * index).animate({
                                    opacity: 1,
                                    top: 0
                                }, 300);
                            });

                            // Jika tidak ada halaman berikutnya, sembunyikan tombol
                            if (!response.hasMore) {
                                $('#load-more-promo').hide();
                            } else {
                                $('#load-more-promo')
                                    .text("@lang('messages.Load More')")
                                    .prop('disabled', false)
                                    .data('page', page + 1)
                                    .show();
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Failed to load more hotels. Please try again.');
                            $('#load-more-promo').text("@lang('messages.Load More')").prop('disabled', false);
                        }
                    });
                }

            });
            document.addEventListener('DOMContentLoaded', function () {
                const clearBtn = document.getElementById('clear-btn');
                const form = document.getElementById('search-form');

                clearBtn.addEventListener('click', function () {
                    // Bersihkan semua input text
                    form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');

                    // Opsional: Bersihkan hasil pencarian / suggestion box
                    document.getElementById('hotel-suggestions').innerHTML = '';
                    document.getElementById('region-suggestions').innerHTML = '';

                    // Jika kamu ingin langsung reload hasil (misal tampilkan semua hotel kembali tanpa filter)
                    // kamu bisa panggil fungsi AJAX yang sama seperti saat "Search"
                    if (typeof loadHotelData === 'function') {
                        loadHotelData(1, true); // true = reset ke awal
                    }
                });
            });
        </script>
    </div>
@endsection
