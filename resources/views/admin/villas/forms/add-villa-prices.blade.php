@section('title', __('messages.Villas'))
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
                                <div class="title"><i class="micon fa fa-plus" aria-hidden="true"></i> Add Prices to {{ $villa->name }}</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="/villas-admin">Villas</a></li>
                                        <li class="breadcrumb-item"><a href="/detail-villa-{{ $villa->id }}">{{ $villa->name }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Add Prices</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Add Villa Prices</div>
                                </div>
                                 @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form id="add-price" action="{{ route('func.villa-price.add',$villa->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    {{ csrf_field() }}
                                    <div class="form-container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading-form">Villa Price</div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="startDate[]" class="form-lable">Stay Period Start <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                <input type="date" id="startDate[]" name="start_date[]" class="input-icon form-control @error('start_date[]') is-invalid @enderror" placeholder="Select date" value="{{ old('start_date[]') }}" required>
                                                                @error('start_date[]')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="endDate[]" class="form-lable">Stay Period End <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                <input type="date" id="endDate[]" name="end_date[]" class="input-icon form-control @error('end_date[]') is-invalid @enderror" placeholder="Select date" value="{{ old('end_date[]') }}" required>
                                                                @error('end_date[]')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="contract_rate[]">Contract Rate <span>*</span> </label>
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
                                                    <label for="markup[]">Markup <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="number" id="markup[]" name="markup[]" class="input-icon form-control @error('markup[]') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup[]') }}" required>
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
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Benefits</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="benefits[]" class="form-label">English</label>
                                                            <textarea name="benefits[]" id="benefits[]" class="form-control @error('benefits[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits[]') }}</textarea>
                                                            @error('benefits[]')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="benefits_traditional[]" class="form-label">Traditional</label>
                                                            <textarea name="benefits_traditional[]" id="benefits_traditional[]" class="form-control @error('benefits_traditional[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits_traditional[]') }}</textarea>
                                                            @error('benefits_traditional[]')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="benefits_simplified[]" class="form-label">Simplified</label>
                                                            <textarea name="benefits_simplified[]" id="benefits_simplified[]" class="form-control @error('benefits_simplified[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits_simplified[]') }}</textarea>
                                                            @error('benefits_simplified[]')
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
                                    <div class="after-add-more"></div>
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More</button>
                                        </div>
                                    </div>
                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    <input id="villa_id" name="villa_id" value="{{ $villa->id }}" type="hidden">
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-price" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                    <a href="{{ route('admin.villa.show',$villa->id) }}">
                                        <button type="button" class="btn btn-danger" ><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="copy hide">
                            <div class="control-group">
                                <div class="form-container p-t-27">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="heading-form">Villa Price</div>
                                            <button style="top: 13px !important; right: 27px !important" class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="startDate[]" class="form-lable">Stay Period Start <span>*</span></label>
                                                        <div class="btn-icon">
                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                            <input type="date" id="startDate[]" name="start_date[]" class="input-icon form-control @error('start_date[]') is-invalid @enderror" placeholder="Select date" value="{{ old('start_date[]') }}" required>
                                                            @error('start_date[]')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="endDate[]" class="form-lable">Stay Period End <span>*</span></label>
                                                        <div class="btn-icon">
                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                            <input type="date" id="endDate[]" name="end_date[]" class="input-icon form-control @error('end_date[]') is-invalid @enderror" placeholder="Select date" value="{{ old('end_date[]') }}" required>
                                                            @error('end_date[]')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="contract_rate[]">Contract Rate <span>*</span> </label>
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
                                                <label for="markup[]">Markup <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup[]" name="markup[]" class="input-icon form-control @error('markup[]') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup[]') }}" required>
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
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Benefits</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="benefits[]" class="form-label">English</label>
                                                        <textarea name="benefits[]" id="benefits[]" class="form-control @error('benefits[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits[]') }}</textarea>
                                                        @error('benefits[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="benefits_traditional[]" class="form-label">Traditional</label>
                                                        <textarea name="benefits_traditional[]" id="benefits_traditional[]" class="form-control @error('benefits_traditional[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits_traditional[]') }}</textarea>
                                                        @error('benefits_traditional[]')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="benefits_simplified[]" class="form-label">Simplified</label>
                                                        <textarea name="benefits_simplified[]" id="benefits_simplified[]" class="form-control @error('benefits_simplified[]') is-invalid @enderror" placeholder="Insert some text" type="text">{{ old('benefits_simplified[]') }}</textarea>
                                                        @error('benefits_simplified[]')
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
                        
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

