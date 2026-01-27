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
                            <i class="icon-copy fa fa-institution" aria-hidden="true"></i> Edit Wedding Venue</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Vendors</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Wedding Venue {{ $weddingVenue->name }}</li>
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
                                    <div class="title">Detail {{ $weddingVenue->name }}</div>
                                </div>
                                <form id="edit-wedding-venue" action="/fedit-wedding-venue-{{ $weddingVenue->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="cover-preview" class="form-label">Cover Image</label>
                                                                <div class="dropzone">
                                                                    <div id="cover-img-preview">
                                                                        <img src="{{ asset('storage/hotels/hotels-wedding-venue/'. $weddingVenue->cover)  }}" alt="{{ $weddingVenue->name }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Cover Image </label>
                                                        <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" onchange="updateCoverPreview(event)">
                                                        @error('cover')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Status</label>
                                                        <select id="status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                                                            <option selected="{{ $weddingVenue->status }}">{{ $weddingVenue->status }}</option>
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
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert hotel name" value="{{ $weddingVenue->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="capacity" class="form-label">Capacity </label>
                                                <input type="text" id="capacity" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ $weddingVenue->capacity }}" required>
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
                                            <div class="form-group">
                                                <label for="period_start" class="form-label">Period Start </label>
                                                <input readonly type="text" name="period_start" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select date" value="{{ date('d F Y',strtotime($weddingVenue->periode_start)) }}" required>
                                                @error('period_start')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="period_end" class="form-label">Period End </label>
                                                <input readonly type="text" name="period_end" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ date('d F Y',strtotime($weddingVenue->periode_end)) }}" required>
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
                                        @php
                                            $slot_json = json_decode($weddingVenue->slot);
                                            $arrangement_json = json_decode($weddingVenue->arrangement_price);
                                            $basic_json = json_decode($weddingVenue->basic_price);
                                            $cslot = count($slot_json);
                                        @endphp
                                        @for ($i = 0; $i < $cslot; $i++)
                                            <div class="col-md-12">
                                                <div class="control-group">
                                                    <div class="row">
                                                        <div class="col-6 col-md-3">
                                                            <div class="form-group">
                                                                <label for="slot[]" class="form-label">Slot</label>
                                                                <input type="text" id="slot[]" name="slot[]" class="form-control time-picker @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ date('H:i',strtotime($slot_json[$i])) }}" required>
                                                                @error('slot[]')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <div class="form-group">
                                                                <label for="basic_price[]">Basic Price</label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="text" id="basic_price[]" name="basic_price[]"  class="form-control @error('basic_price[]') is-invalid @enderror" value="{{ $basic_json[$i] }}" required>
                                                                    @error('basic_price[]')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <div class="form-group">
                                                                <label for="arrangement_price[]">Arrangement Price</label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="text" id="arrangement_price[]" name="arrangement_price[]"  class="form-control @error('arrangement_price[]') is-invalid @enderror" value="{{ $arrangement_json[$i] }}" required>
                                                                    @error('arrangement_price[]')
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
                                        @endfor
                                        <div class="after-add-more"></div>
                                        <div class="col-12 col-sm-12 col-md-12 text-right">
                                            <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More Slot</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="line-with-text">
                                                <span class="line-text">Additional Informations</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description </label>
                                                <textarea id="description" name="description" class="textarea_editor form-control" placeholder="Insert description" required>{{ $weddingVenue->description }}</textarea>
                                                @error('description')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="term_and_condition" class="form-label">Terms and Conditions</label>
                                                <textarea id="term_and_condition" name="term_and_condition" class="textarea_editor form-control" placeholder="Insert additional information">{{ $weddingVenue->term_and_condition }}</textarea>
                                            </div>
                                            @error('term_and_condition')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                        <input id="page" name="page" value="edit-wedding-venue" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="edit-wedding-venue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#wedding-venues">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
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
                                            <div class="form-group">
                                                <label for="slot[]" class="form-label">Slot</label>
                                                <input type="time" id="slot[]" name="slot[]" class="form-control @error('slot[]') is-invalid @enderror" placeholder="Slot available" value="{{ old('slot[]') }}" required>
                                                @error('slot[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
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
    
