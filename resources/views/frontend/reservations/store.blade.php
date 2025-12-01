@extends('frontend.layouts.header')
@section('title', __('messages.Create Reservation'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    <div class="body-container">
        <section id="createReservation" class="mb-3">
            <nav class="breadcrumb-nav text-center">
                <ol class="breadcrumb-list">
                    <li><a href="{{ route('view.index-reservation') }}">@lang('messages.Reservation')</a></li>
                    <li class="active">@lang('messages.Create Reservation')</li>
                    {{-- <li><a href="{{ route('view.hotel-detail',$hotel->code) }}">{{ $hotel->name }}</a></li>
                    <li class="active">@lang('messages.Check Price') ({{ dateFormat($checkin)." - ".dateFormat($checkout) }})</li> --}}
                </ol>
            </nav>
            <div class="container mb-3">
                @include('frontend.partials.alert')
            </div>
            <div class="heading-page-hotels">
                <div class="heading-page-content">
                    <h2>@lang('messages.Reservation')</h2>
                    <p>@lang('messages.Number'): {{ $reservation_code }}</p>
                </div>
            </div>
            <form action="{{ route('func.reservations.store') }}" method="POST">
                @csrf
                @if($service)
                    <input type="hidden" name="service" value="{{ $service }}">
                @else
                    <div class="form-group">
                        <label for="service">@lang('messages.Service')<span>*</span></label>
                        <select name="service" type="text" class="form-control custom-select @error('service') is-invalid @enderror" required>
                            <option disabled selected value="">@lang('messages.Select one')</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->name }}">@lang('messages.'.$service->name)</option>
                            @endforeach
                        </select>
                        @error('service')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif
                @if($service_id)
                    <input type="hidden" name="service_id" value="{{ $service_id }}">
                @else
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service</label>
                        <input type="number" id="service_id" name="service_id" class="form-control" placeholder="Enter service ID" required>
                        <small class="text-muted">*Bisa diganti dengan dropdown list service di kemudian hari</small>
                    </div>
                @endif
                @if($checkin && $checkout)
                    <input type="hidden" name="checkin" value="{{ $checkin }}">
                    <input type="hidden" name="checkout" value="{{ $checkout }}">
                @else
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="check_in" class="form-label">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="check_out" class="form-label">Check-out Date (optional)</label>
                            <input type="date" id="check_out" name="check_out" class="form-control">
                        </div>
                    </div>
                @endif
                <!-- Guests -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adult_guests">Number of Guests Adult<span> *</span></label>
                            <div class="btn-icon">
                                <span><i class="icon-copy fa fa-user" aria-hidden="true"></i></span>
                                <input name="adult_guests" type="number" min="1" value="{{ old('adult_guests') }}" class="form-control input-icon @error('adult_guests') is-invalid @enderror" placeholder="Adult" required>
                            </div>
                            @error('adult_guests')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="child_guests">Number of Guests Child<span> *</span></label>
                            <div class="btn-icon">
                                <span><i class="icon-copy fa fa-child" aria-hidden="true"></i></span>
                                <input name="child_guests" type="number" min="1" value="{{ old('child_guests') }}" class="form-control input-icon @error('child_guests') is-invalid @enderror" placeholder="Child" required>
                            </div>
                            @error('child_guests')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Price -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="total_price" class="form-label">Total Price</label>
                        <input type="number" id="total_price" name="total_price" step="0.01" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency</label>
                        <select id="currency" name="currency" class="form-select">
                            <option value="USD" selected>USD</option>
                            <option value="IDR">IDR</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>
                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Reservation</button>
                </div>
            </form>
        </section>
    </div>
@endsection
