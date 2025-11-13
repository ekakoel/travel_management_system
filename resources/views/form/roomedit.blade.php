@section('title', __('messages.Hotel Room'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-briefcase" aria-hidden="true"></i> Edit Room</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="room-admin">Hotel</a></li>
                                <li class="breadcrumb-item"><a href="/detail-hotel-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Room {{ $room->rooms }}</li>
                            </ol>
                        </nav>
                    </div>
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    Detail Room
                                </div>
                                <div class="card-box-body">
                                    <form id="edit-room" action="/fedit-room-{{ $room->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    @if ($room->cover != "")
                                                        <div class="col-md-6">
                                                            <div class="preview-cover">
                                                                <img src="{{ asset('storage/hotels/hotels-room/'. $room->cover)  }}" alt="{{ $room->name }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="dropzone">
                                                            <div class="cover-preview-div">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="cover" class="form-label">Cover Image </label>
                                                            <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                            @error('cover')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="cover" class="form-label">Status</label>
                                                            <select id="status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                                                                <option selected="{{ $room->status }}">{{ $room->status }}</option>
                                                                <option value="Active">Active</option>
                                                                <option value="Draft">Draft</option>
                                                                <option value="Archived">Archived</option>
                                                            </select>
                                                            @error('status')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rooms" class="form-label">Name </label>
                                                    <input type="text" id="rooms" name="rooms" class="form-control @error('rooms') is-invalid @enderror" placeholder="Insert hotel rooms" value="{{ $room->rooms }}" required>
                                                    @error('rooms')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="capacity_adult" class="form-label">Capacity Adult</label>
                                                    <input type="number" id="capacity_adult" min="1" name="capacity_adult" class="form-control @error('capacity_adult') is-invalid @enderror" placeholder="Insert capacity for adult" value="{{ $room->capacity_adult }}" required>
                                                    @error('capacity_adult')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="capacity_child" class="form-label">Capacity Child</label>
                                                    <input type="number" id="capacity_child" name="capacity_child" class="form-control @error('capacity_child') is-invalid @enderror" placeholder="Insert capacity for child" value="{{ $room->capacity_child }}">
                                                    @error('capacity_child')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="room_view" class="form-label">Room View</label>
                                                    <input required type="text" id="room_view" name="room_view" class="form-control @error('room_view') is-invalid @enderror" value="{{ old('room_view', $room->view ?? '') }}" placeholder="Start typing...">
                                                    <div id="room-view-suggestions" class="suggestion-box"></div>
                                                    @error('room_view')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="beds" class="form-label">Bed Type</label>
                                                    <input required type="text" id="bed_type" name="beds" class="form-control @error('beds') is-invalid @enderror" value="{{ old('beds', $room->beds ?? '') }}" placeholder="Start typing...">
                                                    <div id="bed-type-suggestions" class="suggestion-box"></div>
                                                    @error('beds')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="size" class="form-label">Room Size (m²)</label>
                                                    <input type="number" id="size" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size', $room->size ?? '') }}" placeholder="Insert size...">
                                                    @error('size')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="amenities" class="form-label">Amenities</label>
                                                            <textarea id="amenities" name="amenities" class="textarea_editor form-control" placeholder="Insert amenities">{{ $room->amenities }}</textarea>
                                                        </div>
                                                        @error('amenities')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="amenities_traditional" class="form-label">Amenities (Traditional)</label>
                                                            <textarea id="amenities_traditional" name="amenities_traditional" class="textarea_editor form-control" placeholder="Insert amenities in Chinese traditional">{{ $room->amenities_traditional }}</textarea>
                                                        </div>
                                                        @error('amenities_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="amenities_simplified" class="form-label">Amenities (Simplified)</label>
                                                            <textarea id="amenities_simplified" name="amenities_simplified" class="textarea_editor form-control" placeholder="Insert amenities in Chinese Simplified">{{ $room->amenities_simplified }}</textarea>
                                                        </div>
                                                        @error('amenities_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="additional_info" class="form-label">Additional Information</label>
                                                    <textarea id="additional_info" name="additional_info" class="textarea_editor form-control" placeholder="Insert additional information">{{ $room->additional_info }}</textarea>
                                                </div>
                                                @error('additional_info')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="additional_info_traditional" class="form-label">Additional Information (Traditional)</label>
                                                    <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor form-control" placeholder="Insert additional information in Chinese traditional">{{ $room->additional_info_traditional }}</textarea>
                                                </div>
                                                @error('additional_info_traditional')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="additional_info_simplified" class="form-label">Additional Information (Simplified)</label>
                                                    <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor form-control" placeholder="Insert additional information in Chinese Simplified">{{ $room->additional_info_simplified }}</textarea>
                                                </div>
                                                @error('additional_info_simplified')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                            <input id="page" name="page" value="edit-room" type="hidden">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-box-footer">
                                    <button type="submit" form="edit-room" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="/detail-hotel-{{ $hotel->id }}#rooms">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
<script>
    $(document).ready(function() {
        $('#room_view').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#room-view-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('autocomplate.room_view') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    let suggestions = response.views;
                    let dropdown = $('#room-view-suggestions');
                    dropdown.html('');

                    if (suggestions.length > 0) {
                        suggestions.forEach(view => {
                            if (view.name) {
                                dropdown.append(`<div class="suggestion-item view-item" data-name="${view.name}">${view.name}</div>`);
                            }
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        });

        $('#bed_type').on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                $('#bed-type-suggestions').hide();
                return;
            }

            $.ajax({
                url: "{{ route('autocomplate.bed_type') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    let suggestions = response.beds;
                    let dropdown = $('#bed-type-suggestions');
                    dropdown.html('');

                    if (suggestions.length > 0) {
                        suggestions.forEach(bed => {
                            if (bed.name) {
                                dropdown.append(`<div class="suggestion-item bed-item" data-name="${bed.name}">${bed.name}</div>`);
                            }
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        });

        $(document).on('click', '.view-item', function() {
            $('#room_view').val($(this).data('name'));
            $('#room-view-suggestions').hide();
        });
        $(document).on('click', '.bed-item', function() {
            $('#bed_type').val($(this).data('name'));
            $('#bed-type-suggestions').hide();
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('.search-item').length) {
                $('#room-view-suggestions').hide();
                $('#bed-type-suggestions').hide();
            }
        });
    });
</script>

@endsection
    
