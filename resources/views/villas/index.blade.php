@extends('layouts.head')

@section('title', __('messages.Villas'))

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

            {{-- Villa Section --}}
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle">
                            <i class="dw dw-villa"></i> @lang('messages.Villa')
                        </div>
                    </div>

                    <form id="search-form" class="m-b-8">
                        @csrf
                        <div class="search-container flex-end">
                            <div class="search-item position-relative">
                                <input type="text" class="form-control" id="villa_name" name="villa_name" 
                                    placeholder="@lang('messages.Search by name')">
                                <div id="villa-suggestions" class="suggestion-box"></div>
                            </div>
                            <div class="search-item position-relative">
                                <input type="text" class="form-control" id="villa_region" name="villa_region" 
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
                    

                    {{-- Villa List --}}
                    <div class="card-box-content" id="villa-list">
                        @include('villas.partials.villa-list', ['villas' => $villas])
                    </div>

                    {{-- Load More Button --}}
                    @if($villas->hasMorePages())
                        <div class="text-center mt-3">
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
        // Autocomplete untuk nama villa
        $('#villa_name').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#villa-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('villas.autocomplete') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    let suggestions = response.villas;
                    let dropdown = $('#villa-suggestions');
                    dropdown.html('');

                    if (suggestions.length > 0) {
                        suggestions.forEach(villa => {
                            if (villa.name) {
                                dropdown.append(`<div class="suggestion-item villa-item" data-name="${villa.name}">${villa.name}</div>`);
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
        $('#villa_region').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#region-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('villas.autocompleteRegion') }}",
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

        // Pilih villa dari dropdown
        $(document).on('click', '.villa-item', function() {
            $('#villa_name').val($(this).data('name'));
            $('#villa-suggestions').hide();
        });

        // Pilih region dari dropdown
        $(document).on('click', '.region-item', function() {
            $('#villa_region').val($(this).data('name'));
            $('#region-suggestions').hide();
        });

        // Sembunyikan dropdown jika klik di luar
        $(document).click(function(e) {
            if (!$(e.target).closest('.search-item').length) {
                $('#villa-suggestions').hide();
                $('#region-suggestions').hide();
            }
        });
    });
</script>





<script>
    $(document).ready(function() {
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            $('#villa-list').html('<p>Loading...</p>'); 
            loadVillas(1, true);
        });

        $('#load-more').on('click', function() {
            let page = $(this).data('page');
            loadVillas(page, false);
        });

        function loadVillas(page, isSearch = false) {
            let formData = $('#search-form').serialize();
            $.ajax({
                url: "{{ route('villas.load-more') }}?page=" + page,
                type: "GET",
                data: formData,
                beforeSend: function() {
                    $('#load-more').text("Loading...").prop('disabled', true);
                },
                success: function(response) {
                    if (isSearch) {
                        $('#villa-list').html('');
                        $('#load-more').data('page', 2);
                    }

                    let newContent = $(response.html);
                    $('#villa-list').append(newContent);
                    $('#villa-list .villa-card').each(function(index) {
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
