@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"><i class="micon fa fa-plus" aria-hidden="true"></i> Add Prices</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="/hotels-admin">Hotels</a></li>
                                        <li class="breadcrumb-item"><a href="/detail-hotel-{{ $hotels->id }}">{{ $hotels->name }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Add Prices</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8 m-b-18">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Price</div>
                                </div>
                                <form id="add-price" action="/fadd-price" method="post" enctype="multipart/form-data">
                                    @csrf
                                    {{ csrf_field() }}
                                    <div class="form-container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading-form">Normal Price</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rooms_id[]">Room </label>
                                                    <select id="rooms_id[]" name="rooms_id[]" class="custom-select col-12 @error('rooms_id[]') is-invalid @enderror" required>
                                                        <option selected value="">Select room</option>
                                                        @foreach ($rooms as $sroom)
                                                            <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('rooms_id[]')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="start_date[]">Start Date  </label>
                                                    
                                                        <input  name="start_date[]" id="start_date[]" wire:model="start_date[]" class="form-control @error('start_date[]') is-invalid @enderror" placeholder="Insert date" type="date" value="{{ old('start_date[]') }}" required>
                                                    @error('start_date[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="end_date[]">End Date  </label>
                                                    <input  name="end_date[]" id="end_date[]" wire:model="end_date[]" class="form-control @error('end_date[]') is-invalid @enderror" placeholder="Select date" type="date" value="{{ old('end_date[]') }}" required>
                                                    @error('end_date[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="contract_rate[]">Contract Rate </label>
                                                    <div class="btn-icon">
                                                        <span>Rp</span>
                                                        <input type="number" id="contract_rate[]" name="contract_rate[]" class="input-icon form-control @error('contract_rate[]') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate[]') }}" required>
                                                        @error('contract_rate[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="markup[]">Markup </label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="number" id="markup[]" name="markup[]" class="input-icon form-control @error('markup[]') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup[]') }}">
                                                        @error('markup[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kick_back[]">Kick Back</label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="number" id="kick_back[]" name="kick_back[]" class="input-icon form-control @error('kick_back[]') is-invalid @enderror" placeholder="Insert kick back" value="{{ old('kick_back[]') }}">
                                                        @error('kick_back[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="after-add-more"></div>
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More</button>
                                        </div>
                                    </div>
                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    <input id="hotels_id" name="hotels_id" value="{{ $hotels->id }}" type="hidden">
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-price" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                    <a href="/detail-hotel-{{ $hotels->id }}">
                                        <button type="button" class="btn btn-danger" ><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="copy hide">
                            <div class="control-group">
                                <div class="form-container p-t-27">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="heading-form">Normal Price</div>
                                            <button style="top: 4px !important; right: 19px !important" class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rooms_id[]">Room </label>
                                                <select id="rooms_id[]" name="rooms_id[]" class="custom-select col-12 @error('rooms_id[]') is-invalid @enderror" required>
                                                    <option selected value="">Select room</option>
                                                    @foreach ($rooms as $sroom)
                                                        <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                                    @endforeach
                                                </select>
                                                @error('rooms_id[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="start_date[]">Start Date  </label>
                                                
                                                    <input  name="start_date[]" id="start_date[]" wire:model="start_date[]" class="form-control @error('start_date[]') is-invalid @enderror" placeholder="Insert date" type="date" value="{{ old('start_date[]') }}" required>
                                                @error('start_date[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="end_date[]">End Date  </label>
                                                <input  name="end_date[]" id="end_date[]" wire:model="end_date[]" class="form-control @error('end_date[]') is-invalid @enderror" placeholder="Select date" type="date" value="{{ old('end_date[]') }}" required>
                                                @error('end_date[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="contract_rate[]">Contract Rate </label>
                                                <div class="btn-icon">
                                                    <span>Rp</span>
                                                    <input type="number" id="contract_rate[]" name="contract_rate[]" class="input-icon form-control @error('contract_rate[]') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate[]') }}" required>
                                                    @error('contract_rate[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="markup[]">Markup </label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup[]" name="markup[]" class="input-icon form-control @error('markup[]') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup[]') }}">
                                                    @error('markup[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="kick_back[]">Kick Back</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="kick_back[]" name="kick_back[]" class="input-icon form-control @error('kick_back[]') is-invalid @enderror" placeholder="Insert kick back" value="{{ old('kick_back[]') }}">
                                                    @error('kick_back[]')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
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
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection

