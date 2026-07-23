@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero>
                        <x-slot name="heading">
                            <i class="icon-copy fa fa-institution" aria-hidden="true"></i> Add Ceremony Venue
                        </x-slot>
                    </x-backend.page-hero>
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
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">New Ceremony Venue</div>
                                </div>
                                <form id="add-wedding-venue" action="/fadd-wedding-venue" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="backend-form-field">
                                                        <label for="cover-preview" class="form-label">Cover Image</label>
                                                        <div class="dropzone">
                                                            <div id="cover-img-preview">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="cover" class="form-label">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" onchange="updateCoverPreview(event)">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert venue name!" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="capacity" class="form-label">Capacity </label>
                                                <input type="number" min="1" id="capacity" name="capacity" class="backend-form-control @error('capacity') is-invalid @enderror" placeholder="ex: 2" value="{{ old('capacity') }}" required>
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="line-with-text">
                                                <span class="line-text">Period</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="period_start" class="form-label">Period Start </label>
                                                <input readonly type="text" name="period_start" class="backend-form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select date" value="{{ old('period_start') }}" required>
                                                @error('period_start')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="period_end" class="form-label">Period End </label>
                                                <input readonly type="text" name="period_end" class="backend-form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ old('period_end') }}" required>
                                                @error('period_end')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="line-with-text">
                                                <span class="line-text">Slot</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="control-group">
                                                <div class="row">
                                                    <div class="col-6 col-md-3">
                                                        <div class="backend-form-field">
                                                            <label for="slot[]" class="form-label">Slot</label>
                                                            <input type="time" id="slot[]" name="slot[]" class="backend-form-control @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ old('slot[]') }}" required>
                                                            @error('slot[]')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <div class="backend-form-field">
                                                            <label for="basic_price[]">Basic Price</label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="text" id="basic_price[]" name="basic_price[]"  class="backend-form-control @error('basic_price[]') is-invalid @enderror" required>
                                                                @error('basic_price[]')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <div class="backend-form-field">
                                                            <label for="arrangement_price[]">Arrangement Price</label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="text" id="arrangement_price[]" name="arrangement_price[]"  class="backend-form-control @error('arrangement_price[]') is-invalid @enderror" required>
                                                                @error('arrangement_price[]')
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
                                            <button type="button" class="backend-button backend-button-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add more Time</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="line-with-text">
                                                <span class="line-text">Additional Information</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea data-backend-richtext="true" id="description" name="description"  class="textarea_editor backend-form-control border-radius-0 @error('description') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('description') }}"></textarea>
                                                @error('description')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="term_and_condition" class="form-label">Terms and Conditions</label>
                                                <textarea data-backend-richtext="true" id="term_and_condition" name="term_and_condition"  class="textarea_editor backend-form-control border-radius-0 @error('term_and_condition') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('term_and_condition') }}"></textarea>
                                                @error('term_and_condition')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-wedding-venue" class="backend-button backend-button-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Wedding Venues</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#wedding-package">
                                        <button type="button"class="backend-button backend-button-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ADD MORE SLOT --}}
                        <div class="copy hide">
                            <div class="col-md-12">
                                <div class="control-group">
                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <div class="backend-form-field">
                                                <label for="slot[]" class="form-label">Slot</label>
                                                <input type="time" id="slot[]" name="slot[]" class="backend-form-control @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ old('slot[]') }}" required>
                                                @error('slot[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="backend-form-field">
                                                <label for="basic_price[]">Basic Price</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="basic_price[]" name="basic_price[]"  class="backend-form-control @error('basic_price[]') is-invalid @enderror" required>
                                                    @error('basic_price[]')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="backend-form-field">
                                                <label for="arrangement_price[]">Arrangement Price</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="arrangement_price[]" name="arrangement_price[]"  class="backend-form-control @error('arrangement_price[]') is-invalid @enderror" required>
                                                    @error('arrangement_price[]')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="align-self: center; padding-bottom:17px;">
                                            <button class="backend-button backend-button-danger remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
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
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
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
    @endcan
@endsection
