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
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Food & Beferage</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Vendors</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Food & Beverage</li>
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
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Food & Beverage</div>
                                </div>
                                <form id="add-food-and-beverage" action="/fadd-food-and-beverage" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="type" class="form-label">Type</label>
                                                <select id="type" name="type" class="form-control custom-select @error('type') is-invalid @enderror" required>
                                                    <option selected value="">Select Type</option>
                                                    <option value="Food">Food</option>
                                                    <option value="Beverage">Beverage</option>
                                                </select>
                                                @error('type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="categoryFood" class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="category" class="form-label">Category Food</label>
                                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" placeholder="ex: Asian Set Menu" value="{{ old('category') }}" required>
                                                @error('category')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div id="categoryBeverage" class="col-12 col-sm-6 col-md-6 hide">
                                            <div class="form-group">
                                                <label for="category" class="form-label">Category Beverage</label>
                                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" placeholder="ex: Sea food" value="{{ old('category') }}" required>
                                                @error('category')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div id="duration" class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="duration" class="form-label">Duration</label>
                                                <input type="number" min="1" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ old('duration') }}" required>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include" class="form-label">Include</label>
                                                <textarea id="include" name="include"  class="textarea_editor form-control border-radius-0 @error('include') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('include') }}"></textarea>
                                                @error('include')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea id="description" name="description"  class="textarea_editor form-control border-radius-0 @error('description') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('description') }}"></textarea>
                                                @error('description')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="note" class="form-label">Note</label>
                                                <textarea id="note" name="note"  class="textarea_editor form-control border-radius-0 @error('note') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('note') }}"></textarea>
                                                @error('note')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="agent_rate">Agent Rate</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="agent_rate" name="agent_rate"  class="form-control numeric-input @error('agent_rate') is-invalid @enderror" placeholder="Insert agent rate" value="{{ old('agent_rate') }}">
                                                    @error('agent_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="public_rate">Public Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="public_rate" name="public_rate"  class="form-control numeric-input @error('public_rate') is-invalid @enderror" placeholder="Insert public rate" value="{{ old('public_rate') }}" required>
                                                    @error('public_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="fee">Fee</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="fee" name="fee"  class="form-control numeric-input @error('fee') is-invalid @enderror" placeholder="Insert fee" value="{{ old('fee') }}">
                                                    @error('fee')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-wedding-venue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Wedding Venues</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#wedding-package">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
        <script>
            document.getElementById('type').addEventListener('change', function() {
                var typeFB = this.value;
                if (typeFB == "Food") {
                    var cf = document.getElementById('categoryFood');
                    cf.style.display='block';
                    cf.style.animation='feed-up 1s linear';
                    var cb = document.getElementById('categoryBeverage');
                    cb.style.display='none';
                }else{
                    var cf = document.getElementById('categoryFood');
                    cf.style.display='none';
                    var cb = document.getElementById('categoryBeverage');
                    cb.style.display='block';
                    cb.style.animation='feed-up 1s linear';
                }
            });
        </script>
    @endcan
@endsection
