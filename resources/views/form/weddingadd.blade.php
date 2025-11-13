@php
    $check_no = 0;
@endphp
@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Wedding Package
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">New Wedding Package</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="product-wrap">
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box p-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle">Wedding Package</div>
                                    </div>
                                    <form id="addWedding" action="/fadd-wedding-package-{{ $hotel->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="cover-preview" class="form-label">Cover Image</label>
                                                            <div class="dropzone">
                                                                <div id="cover-img-preview"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="cover" class="form-label">Cover Image <span> *</span></label><br>
                                                    <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required onchange="updateCoverPreview(event)">
                                                    @error('cover')
                                                        <div class="alert-form alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Wedding Package Property</span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Package Name</label>
                                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert wedding package name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" min="1" id="capacity" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" value="{{ old('capacity') }}" required>
                                                    @error('capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="duration" class="form-label">Duration</label>
                                                    <input type="number" min="0" id="duration" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Night" value="{{ old('duration') }}" required>
                                                    @error('duration')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="period_start" class="form-label">Period Start</label>
                                                    <input readonly type="text" id="period_start" name="period_start" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_start') }}" required>
                                                    @error('period_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="period_end" class="form-label">Period End</label>
                                                    <input readonly type="text" id="period_end" name="period_end" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_end') }}" required>
                                                    @error('period_end')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Wedding Package Services</span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="suites_and_villas_id" class="form-label">Suites / Villas</label>
                                                    <select name="suites_and_villas_id" id="suites_and_villas_id" placeholder="Suites and Villas" class="form-control custom-select @error('suites_and_villas_id') is-invalid @enderror" required>
                                                        <option selected value="">Select Room</option>
                                                        @foreach ($rooms as $room)
                                                            <option value="{{ $room->id }}">{{ $room->rooms }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('suites_and_villas_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="transport_id" class="form-label">Transport</label>
                                                    <select name="transport_id" id="transport_id" placeholder="test" class="form-control custom-select @error('transport_id') is-invalid @enderror">
                                                        <option selected value="">Select Transport</option>
                                                        @foreach ($transports as $transport)
                                                            <option value="{{ $transport->id }}">{{ $transport->brand." - ".$transport->name." (".$transport->capacity." guests)" }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('transport_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="ceremony_venue_id" class="form-label">Ceremony Venue</label>
                                                    <select name="ceremony_venue_id" id="ceremony_venue_id" class="form-control custom-select @error('ceremony_venue_id') is-invalid @enderror" required>
                                                        <option selected value="">Select Venue</option>
                                                        @foreach ($weddingVenues as $wedding_venue)
                                                            <option value="{{ $wedding_venue->id }}" data-slot="{{ $wedding_venue->slot }}" venue-name="{{ $wedding_venue->name }}">{{ $wedding_venue->name." (".$wedding_venue->capacity." guests)" }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('ceremony_venue_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                @php
                                                    $vendorWeddingVenueDecorations_by_hotel = $vendorWeddingVenueDecorations->where('hotel_id',$hotel->id);
                                                    $vendorWeddingVenueDecorations_by_vendor = $vendorWeddingVenueDecorations->where('hotel_id',NULL);
                                                @endphp
                                                <div class="form-group">
                                                    <label for="ceremony_venue_decoration_id" class="form-label">Ceremony Venue Decoration</label>
                                                    <select name="ceremony_venue_decoration_id" id="ceremony_venue_decoration_id" class="form-control custom-select @error('ceremony_venue_decoration_id') is-invalid @enderror">
                                                        <option selected value="">Basic Decoration</option>
                                                        @if ($vendorWeddingVenueDecorations_by_hotel)
                                                            @foreach ($vendorWeddingVenueDecorations_by_hotel as $ceremony_venue_decoration_hotel)
                                                                <option value="{{ $ceremony_venue_decoration_hotel->id }}">{{ $ceremony_venue_decoration_hotel->service." (".$ceremony_venue_decoration_hotel->capacity." guests)" }}</option>
                                                            @endforeach
                                                        @endif
                                                        @if ($vendorWeddingVenueDecorations_by_vendor)
                                                            @foreach ($vendorWeddingVenueDecorations_by_vendor as $ceremony_venue_decoration_vendor)
                                                                <option value="{{ $ceremony_venue_decoration_vendor->id }}">{{ $ceremony_venue_decoration_vendor->service." (".$ceremony_venue_decoration_vendor->capacity." guests)" }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('ceremony_venue_decoration_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="reception_venue_id" class="form-label">Reception Venue</label>
                                                    <select name="reception_venue_id" id="reception_venue_id" class="form-control custom-select @error('reception_venue_id') is-invalid @enderror">
                                                        <option selected value="">Select Reception Venue</option>
                                                        @foreach ($receptionVenues as $reception_venue)
                                                            <option value="{{ $reception_venue->id }}">{{ $reception_venue->name." (".$reception_venue->capacity." guests)" }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('reception_venue_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                @php
                                                    $receptionVenueDecorations_by_hotel = $receptionVenueDecorations->where('hotel_id',$hotel->id);
                                                    $receptionVenueDecorations_by_vendor = $receptionVenues->where('hotel_id',NULL);
                                                @endphp
                                                <div class="form-group">
                                                    <label for="reception_venue_decoration_id" class="form-label">Reception Venue Decoration</label>
                                                    <select name="reception_venue_decoration_id" id="reception_venue_decoration_id" class="form-control custom-select @error('reception_venue_decoration_id') is-invalid @enderror">
                                                        <option selected value="">Basic Decoration</option>
                                                        @if ($receptionVenueDecorations_by_hotel)
                                                            @foreach ($receptionVenueDecorations_by_hotel as $reception_venue_decoration_hotel)
                                                                <option value="{{ $reception_venue_decoration_hotel->id }}">{{ $reception_venue_decoration_hotel->service." (".$reception_venue_decoration_hotel->capacity." guests)" }}</option>
                                                            @endforeach
                                                        @endif
                                                        @if ($receptionVenueDecorations_by_vendor)
                                                            @foreach ($receptionVenueDecorations_by_vendor as $reception_venue_decoration_vendor)
                                                                <option value="{{ $reception_venue_decoration_vendor->id }}">{{ $reception_venue_decoration_vendor->service." (".$reception_venue_decoration_vendor->capacity." guests)" }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('reception_venue_decoration_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- @if (count($lunchVenues)>0)
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="lunch_venue_id" class="form-label">Lunch Venue</label>
                                                        <select name="lunch_venue_id" id="lunch_venue_id" class="form-control custom-select @error('lunch_venue_id') is-invalid @enderror">
                                                            <option selected value="">Select Lunch Venue</option>
                                                            @foreach ($lunchVenues as $lunch_venue)
                                                                <option value="{{ $lunch_venue->id }}">{{ $lunch_venue->name." (".$lunch_venue->min_capacity." - ".$lunch_venue->max_capacity." guests)" }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('lunch_venue_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                            @if (count($dinnerVenues)>0)
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="dinner_venue_id" class="form-label">Dinner Venue</label>
                                                        <select name="dinner_venue_id" id="dinner_venue_id" class="form-control custom-select @error('dinner_venue_id') is-invalid @enderror">
                                                            <option selected value="">Select Dinner Venue</option>
                                                            @foreach ($dinnerVenues as $dinner_venue)
                                                                <option value="{{ $dinner_venue->id }}">{{ $dinner_venue->name." (".$dinner_venue->capacity." guests)" }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('dinner_venue_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif --}}
                                            <div class="col-12 col-sm-12">
                                                @php
                                                    $adser_entertainments = $additionalServices->where('type','Entertainment');
                                                    $adser_makeups = $additionalServices->where('type','Make-up');
                                                    $adser_documentations = $additionalServices->where('type','Documentation');
                                                    $adser_others = $additionalServices->where('type','Other');
                                                @endphp
                                                <div class="line-with-text">
                                                    <span class="line-text">Additional Services</span>
                                                </div>
                                                <div class="form-group">
                                                    @if ($adser_entertainments)
                                                        <div class="subtitle m-b-8 m-t-18">Entertainment</div>
                                                        <div class="grid-4">
                                                            @foreach ($adser_entertainments as $entertainment_service)
                                                                @php
                                                                    $entertainment_id = ++$check_no;
                                                                @endphp
                                                                <div class="custom-control custom-checkbox mb-5">
                                                                    <input type="checkbox" class="custom-control-input" id="customCheck{{ $entertainment_id }}" name="additional_service[]" value="{{ $entertainment_service->id }}">
                                                                    <label class="custom-control-label" for="customCheck{{ $entertainment_id }}">{{ $entertainment_service->service }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <hr class="form-hr">
                                                    @endif
                                                    @if ($adser_makeups)
                                                        <div class="subtitle m-b-8 m-t-18">Make-up</div>
                                                        <div class="grid-4">
                                                            @foreach ($adser_makeups as $makeup_service)
                                                                @php
                                                                    $makeup_id = ++$check_no;
                                                                @endphp
                                                                <div class="custom-control custom-checkbox mb-5">
                                                                    <input type="checkbox" class="custom-control-input" id="customCheck{{ $makeup_id }}" name="additional_service[]" value="{{ $makeup_service->id }}">
                                                                    <label class="custom-control-label" for="customCheck{{ $makeup_id }}">{{ $makeup_service->service }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <hr class="form-hr">
                                                    @endif
                                                    @if ($adser_documentations)
                                                        <div class="subtitle m-b-8 m-t-18">Documentations</div>
                                                        <div class="grid-4">
                                                            @foreach ($adser_documentations as $documentation_service)
                                                                @php
                                                                    $documentation_id = ++$check_no;
                                                                @endphp
                                                                <div class="custom-control custom-checkbox mb-5">
                                                                    <input type="checkbox" class="custom-control-input" id="customCheck{{ $documentation_id }}" name="additional_service[]" value="{{ $documentation_service->id }}">
                                                                    <label class="custom-control-label" for="customCheck{{ $documentation_id }}">{{ $documentation_service->service }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <hr class="form-hr">
                                                    @endif
                                                    @if ($adser_others)
                                                        <div class="subtitle m-b-8 m-t-18">Other Services</div>
                                                        <div class="grid-4">
                                                            @foreach ($adser_others as $other_service)
                                                                @php
                                                                    $other_id = ++$check_no;
                                                                @endphp
                                                                <div class="custom-control custom-checkbox mb-5">
                                                                    <input type="checkbox" class="custom-control-input" id="customCheck{{ $other_id }}" name="additional_service[]" value="{{ $other_service->id }}">
                                                                    <label class="custom-control-label" for="customCheck{{ $other_id }}">{{ $other_service->service }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Additional Informations</span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="include" class="form-label">Include <span>*</span></label>
                                                    <textarea id="include" name="include" class="textarea_editor form-control @error('include') is-invalid @enderror" placeholder="Insert include" value="{{ old('include') }}" required></textarea>
                                                    @error('include')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="payment_process" class="form-label">Payment Process</label>
                                                    <textarea id="payment_process" name="payment_process" class="textarea_editor form-control @error('Description') is-invalid @enderror" placeholder="Insert Remark" value="{{ old('payment_process') }}"></textarea>
                                                    @error('payment_process')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="cancellation_policy" class="form-label">Cancellation Policy</label>
                                                    <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control @error('Description') is-invalid @enderror" placeholder="Insert cancellation policy" value="{{ old('cancellation_policy') }}"></textarea>
                                                    @error('cancellation_policy')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                    <textarea id="terms_and_conditions" name="terms_and_conditions" class="textarea_editor form-control @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert terms_and_conditions" value="{{ old('terms_and_conditions') }}"></textarea>
                                                    @error('terms_and_conditions')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Slot</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="slot-container">
                                                    <div class="slot-item">
                                                        <div class="form-group">
                                                            <label for="slot">Slot</label>
                                                            <input type="time" name="slot[]"  class="form-control @error('slot') is-invalid @enderror" value="{{ old('slot') }}">
                                                            @error('slot')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button id="add-more" type="button" class="btn btn-primary add-more m-b-8"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More Slot</button>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Prices</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="week_day_price">Week Day Price</label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="text" id="week_day_price" name="week_day_price"  class="form-control @error('week_day_price') is-invalid @enderror" value="{{ old('week_day_price') }}" required>
                                                        @error('week_day_price')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="holiday_price">Holiday Price</label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="text" id="holiday_price" name="holiday_price"  class="form-control @error('holiday_price') is-invalid @enderror" value="{{ old('holiday_price') }}" required>
                                                        @error('holiday_price')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="hotel_id" name="hotel_id" value="{{ $hotel->id }}" type="hidden">
                                        </div>
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addWedding" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                        <a href="/weddings-hotel-admin-{{ $hotel->id }}">
                                            <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-remove" aria-hidden="true"></i> Cancel</button>
                                        </a>
                                    </div>
                                </div>
                                @if (count($attentions)>0)
                                    <div class="col-md-4">
                                        <div class="card-box m-b-18 p-b-18">
                                            <div class="banner-right">
                                                <div class="title">Attention</div>
                                                <ul class="attention">
                                                    @foreach ($attentions as $attention)
                                                        <li><p><b>"{{ $attention->name }}"</b> {{ $attention->attention }}</p></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
        <script>
            function updateCoverPreview(event) {
                var input = event.target;
                var reader = new FileReader();
                reader.onload = function() {
                    var dataURL = reader.result;
                    var previewDiv = document.getElementById('cover-img-preview');
                    previewDiv.innerHTML = '';
                    var imgElement = document.createElement('img');
                    imgElement.src = dataURL;
                    imgElement.className = 'img-fluid rounded';
                    previewDiv.appendChild(imgElement);
                };
                reader.readAsDataURL(input.files[0]);
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#add-more').click(function() {
                    var html = `
                        <div class="slot-item">
                            <div class="form-group">
                                <label for="slot">Slot</label>
                                    <input type="time" name="slot[]" class="form-control input-w-button-right @error('slot') is-invalid @enderror" value="">
                                    <div class="btn-remove-input">
                                        <button class="btn btn-danger remove" type="button"><i class="fa fa-times"></i></button>
                                    </div>
                                @error('slot')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    `;
                    $('.slot-container').append(html);
                    $('.time-picker').timepicker();
                });
                $('body').on('click', '.remove', function() {
                    $(this).closest('.slot-item').remove();
                });
            });
        </script>
    @endcan
@endsection
