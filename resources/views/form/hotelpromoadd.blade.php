@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row mb-30">
                <div class="col-md-12 col-md-12">
                    <div class="card-box pd-20  mb-30">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="images/icons/Tour.png" alt="">
                            </div>
                            <div class="col-md-10">
                                <h4 class="font-20 weight-500 mb-10 text-capitalize">
                                    <div class="weight-600 font-30 text-blue">Add Promo for {{ $hotel->name }}</div>
                                </h4>
                                <p class="font-18">Lengkapi form dibawah untuk menambahkan data Promo. Pastikan setiap kolom
                                    di isi dengan data yang sebenarnya, karena setelah data di upload maka secara real time
                                    data tersebut dapat dilihat oleh semua user pada halaman Dashboard</p>
                                <p>Author: {{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="add-promo">
                <form action="/fadd-promo" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- {{ csrf_field() }} --}}
                    {{-- <div class="form-group ">
                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                    </div>
                    <div class="form-group ">
                        <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                    </div>
                    <div class="form-group ">
                        <input name="service" value="{{ $hotel->name }}" type="hidden">
                    </div>
                    <div class="form-group ">
                        <input name="service_name" value="{{ $room->rooms }}" type="hidden">
                    </div>
                    <div class="form-group ">
                        <input name="status" value="Draft" type="hidden">
                    </div> --}}
                    <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                    <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                    <input name="service" value="{{ $hotel->name }}" type="hidden">
                    <div class="form-group row">
                        <label for="name" class="col-sm-10 col-md-2 col-form-label">Promo Name</label>
                        <div class="col-sm-12 col-md-12">
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Promo Name" type="text" required>
                            @error('name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>

                    <div class="form-group row">
                        <label class="col-sm-10 col-md-2 col-form-label">Room</label>
                        <div class="col-sm-6 col-md-12">
                            <select id="rooms_id" name="rooms_id" value="{{ old('rooms_id') }}" class="custom-select col-12 @error('rooms_id') is-invalid @enderror" required>
                                @foreach ($hotel->rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->rooms }}</option>
                                @endforeach
                            </select>
                            @error('rooms_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Booking Period Start</label>
                                <div class="col-sm-12 col-md-12">
                                    <input id="book_periode_start" name="book_periode_start" class="form-control date-picker @error('book_periode_start') is-invalid @enderror" placeholder="Select Date" type="text" required>
                                    @error('book_periode_start')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Booking Period End</label>
                                <div class="col-sm-12 col-md-12">
                                    <input id="book_periode_end" name="book_periode_end" class="form-control date-picker @error('book_periode_end') is-invalid @enderror" placeholder="Select Date" type="text" required>
                                    @error('book_periode_end')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12  col-form-label">Stay Period Start</label>
                                <div class="col-sm-12 col-md-12">
                                    <input id="periode_start" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Select Date" type="text" required>
                                    @error('periode_start')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Stay Period End</label>
                                <div class="col-sm-12 col-md-12">
                                    <input id="periode_end" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Select Date" type="text" required>
                                    @error('periode_end')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">Price</label>
                        <div class="col-sm-12 col-md-12">
                            <input id="price" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" type="number" placeholder="Price">
                            @error('price')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">Inclusion</label>
                        <div class="col-sm-12 col-md-12">
                            <textarea id="include" name="include" value="{{ old('include') }}" class="textarea_editor form-control border-radius-0 @error('include') is-invalid @enderror"  placeholder="Insert Inclusion ..."  required></textarea>
                            @error('include')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">Additional Information</label>
                        <div class="col-sm-12 col-md-12">
                            <textarea name="additional_info" id="additional_info" value="{{ old('additional_info') }}" class="textarea_editor form-control border-radius-0 @error('additional_info') is-invalid @enderror" placeholder="Insert Additional Information ..." required></textarea>
                            @error('additional_info')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12 text-right">

                        <button type="submit" class="btn btn-info">Add Promo</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
            

            
            @include('layouts.footer')
        </div>
    </div>
