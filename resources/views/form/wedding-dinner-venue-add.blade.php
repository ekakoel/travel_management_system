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
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Dinner Venue
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Dinner Venue</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="product-wrap">
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box p-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle">Dinner Venue</div>
                                    </div>
                                    <form id="addWeddingDinnerVenue" action="/fadd-dinner-venue-{{ $hotel->id }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="cover-preview" class="form-label">Cover Image</label>
                                                            <div class="dropzone">
                                                                <div class="cover-preview-div">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="cover" class="form-label">Cover Image <span> *</span></label><br>
                                                    <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                    @error('cover')
                                                        <div class="alert-form alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Wedding Venue</label>
                                                    <select name="wedding_venue_id" id="wedding_venue_id" class="custom-select @error('wedding_venue_id') is-invalid @enderror" required>
                                                        <option selected value="">Select Venue</option>
                                                        
                                                    </select>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                           
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" min="1" id="capacity" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" value="{{ old('capacity') }}" required>
                                                    @error('capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="duration_day" class="form-label">Duration (day)</label>
                                                    <input type="number" min="1" id="duration_day" name="duration_day" class="form-control @error('duration_day') is-invalid @enderror" placeholder="Day" value="{{ old('duration_day') }}" required>
                                                    @error('duration_day')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="duration_night" class="form-label">Duration (night)</label>
                                                    <input type="number" min="0" id="duration_night" name="duration_night" class="form-control @error('duration_night') is-invalid @enderror" placeholder="Night" value="{{ old('duration_night') }}" required>
                                                    @error('duration_night')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="period_start" class="form-label">Period Start</label>
                                                    <input readonly type="text" id="period_start" name="period_start" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_start') }}" required>
                                                    @error('period_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="period_end" class="form-label">Period End</label>
                                                    <input readonly type="text" id="period_end" name="period_end" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_end') }}" required>
                                                    @error('period_end')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea id="description" name="description" class="textarea_editor form-control @error('Description') is-invalid @enderror" placeholder="Insert description" value="{{ old('description') }}"></textarea>
                                                    @error('description')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
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
                                                    <label for="cancellation_policy" class="form-label">Cancellation Policy</label>
                                                    <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control @error('Description') is-invalid @enderror" placeholder="Insert cancellation policy" value="{{ old('cancellation_policy') }}"></textarea>
                                                    @error('cancellation_policy')
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
                                                    <label for="additional_info" class="form-label">Additional Information</label>
                                                    <textarea id="additional_info" name="additional_info" class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Insert additional_info" value="{{ old('additional_info') }}"></textarea>
                                                    @error('additional_info')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="remark" class="form-label">Remark</label>
                                                    <textarea id="remark" name="remark" class="textarea_editor form-control @error('Description') is-invalid @enderror" placeholder="Insert Remark" value="{{ old('remark') }}"></textarea>
                                                    @error('remark')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="control-group">
                                                <div class="row">
                                                    <div class="col-6 col-md-3">
                                                        <div class="form-group">
                                                            <label for="slot[]" class="form-label">Time</label>
                                                            <input type="time" id="slot[]" name="slot[]" class="form-control @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ old('slot[]') }}" required>
                                                            @error('slot[]')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <div class="form-group">
                                                            <label for="arrangement_price[]">Arrangement Price</label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="text" id="arrangement_price[]" name="arrangement_price[]"  class="form-control @error('arrangement_price[]') is-invalid @enderror" required>
                                                                @error('arrangement_price[]')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <div class="form-group">
                                                            <label for="basic_price[]">Basic Price</label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="text" id="basic_price[]" name="basic_price[]"  class="form-control @error('basic_price[]') is-invalid @enderror" required>
                                                                @error('basic_price[]')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="after-add-more"></div>
                                            <div class="col-12 col-sm-12 col-md-12 text-right">
                                                <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add more Time</button>
                                            </div>
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="hotel_id" name="hotel_id" value="{{ $hotel->id }}" type="hidden">
                                        </div>
                                    </form>
                                    {{-- ADD MORE SERVICE --}}
                                    <div class="copy hide">
                                        <div class="col-md-12">
                                            <div class="control-group">
                                                <div class="row">
                                                        <div class="col-6 col-md-3">
                                                            <div class="form-group">
                                                                <label for="slot[]" class="form-label">Time</label>
                                                                <input type="time" id="slot[]" name="slot[]" class="form-control @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ old('slot[]') }}" required>
                                                                @error('slot[]')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <div class="form-group">
                                                                <label for="arrangement_price[]">Arrangement Price</label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="text" id="arrangement_price[]" name="arrangement_price[]"  class="form-control @error('arrangement_price[]') is-invalid @enderror" required>
                                                                    @error('arrangement_price[]')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <div class="form-group">
                                                                <label for="basic_price[]">Basic Price</label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="text" id="basic_price[]" name="basic_price[]"  class="form-control @error('basic_price[]') is-invalid @enderror" required>
                                                                    @error('basic_price[]')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1" style="align-self: center; padding-bottom:17px;">
                                                            <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            var ro = 1;
                                            $(".add-more").click(function(){ 
                                                ro++;
                                                var html = $(".copy").html();
                                                $(".after-add-more").before(html);
                                            });
                                            $("body").on("click",".remove",function(){ 
                                                $(this).parents(".control-group").remove();
                                            });
                                        });
                                    </script>
                                    <div class="card-box-footer">
                                        <button type="submit" form="addWeddingDinnerVenue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create Wedding</button>
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
    @endcan
@endsection
