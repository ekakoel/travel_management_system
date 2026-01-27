@section('title', __('messages.Promotion'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="fa fa-shopping-bag" aria-hidden="true"></i> Promotion {{ Auth::user()->position }}
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Promotion</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="owl-carousel owl-theme">
                        @foreach ($promotions as $promotion)
                            <div class="carousel-container">
                                @if ($promotion->status == "Draft")
                                    <div class="item-draft">
                                @else
                                    <div class="item">
                                @endif
                                    <img class="filter-grey" src="{{ asset('storage/promotion/cover/' . $promotion->cover) }}" alt="{{ $promotion->name }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="info-action">
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
                        @if (\Session::has('invalid'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('invalid') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-4 mobile">
                            @if (isset($activepromotion) or isset($draft_promotions) or isset($usedup_promotions) or isset($pending_promotions) or isset($invalid_promotions) or isset($Expired_promotions))
                                <div class="counter-container">
                                    @if (count($activepromotion)>0)
                                        <a href="#activepromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activepromotion->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draft_promotions)>0)
                                        <a href="#draftpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($usedup_promotions)>0)
                                        <a href="#useduppromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-usedup">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $usedup_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Used up</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($pending_promotions)>0)
                                        <a href="#pendingpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($invalid_promotions)>0)
                                        <a href="#invalidpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($expired_promotions)>0)
                                        <a href="#expiredpromotions">
                                            <div class="widget-expired">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-expired">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $expired_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Expired</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($rejected_promotions)>0)
                                        <a href="#rejectedpromotions">
                                            <div class="height-100-p widget-rejected">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $rejected_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-percent" aria-hidden="true"></i>Promotions</div>
                                </div>
                                @if (count($promotions)>0)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="input-group">
                                                    <div class="col-md-4">
                                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                        <input id="searchPromotionByName" type="text" onkeyup="searchPromotionByName()" class="form-control" name="search-promotion-byname" placeholder="Search by name...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbPartners" class="data-table table stripe hover" >
                                        <thead>
                                            <tr>
                                                <th data-priority="2" class="datatable-nosort" style="width: 5%;">No</th>
                                                <th data-priority="1" style="width: 15%;">Name</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 10%;">Start Period</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 10%;">End Period</th>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">Discounts</th>
                                                <th data-priority="2" style="width: 10%;">Status</th>
                                                <th data-priority="2" class="datatable-nosort" style="width: 15%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($promotions as $no=>$promotion)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $promotion->name }}</div>
                                                    </td>
                                                    <td>
                                                        <p>{{ date('d M y', strtotime($promotion->periode_start)) }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ date('d M y', strtotime($promotion->periode_end)) }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ '$ '. $promotion->discounts }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($promotion->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($promotion->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @elseif ($promotion->status == "Usedup")
                                                            <div class="status-usedup"></div>
                                                        @elseif ($promotion->status == "Expired")
                                                            <div class="status-expired"></div>
                                                        @elseif ($promotion->status == "Pending")
                                                            <div class="status-pending"></div>
                                                        @elseif ($promotion->status == "Invalid")
                                                            <div class="status-invalid"></div>
                                                        @elseif ($promotion->status == "Rejected")
                                                            <div class="status-rejected"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                
                                                        @if ($promotion->status == "Expired")
                                                            <td><div class="form-notif">cannot be changed</div></td>
                                                        @else
                                                            <td class="text-right">
                                                                <div class="table-action">
                                                                    <a href="#" data-toggle="modal" data-target="#detail-promotion-{{ $promotion->id }}">
                                                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Detail"><i class="icon-copy fa fa-eye"></i></button>
                                                                    </a>
                                                                    @canany(['posDev','posAuthor'])
                                                                        <a href="#" data-toggle="modal" data-target="#edit-promotion-{{ $promotion->id }}">
                                                                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                        </a>
                                                                        <form class="display-content" action="/fremove-promotion/{{ $promotion->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                    @endcanany
                                                </tr>
                                                {{-- MODAL DETAIL PROMOTION --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-promotion-{{ $promotion->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i> Detail Promotion</div>
                                                                </div>
                                                            
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="cover-preview-div">
                                                                            <img src="{{ asset('/storage/promotion/cover/'.$promotion->cover) }}" alt="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 m-t-8">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <div class="modal-subtitle">
                                                                                    Promotion Name :
                                                                                </div>
                                                                                <p>{{ $promotion->name }}</p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="modal-subtitle">
                                                                                    Status :
                                                                                </div>
                                                                                <p>{{ $promotion->status }}</p>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="modal-subtitle">
                                                                                    Start Period :
                                                                                </div>
                                                                                <p>{{ date('d M Y',strtotime($promotion->periode_start)) }}</p>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="modal-subtitle">
                                                                                    End Period :
                                                                                </div>
                                                                                <p>{{ date('d M Y',strtotime($promotion->periode_end)) }}</p>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="modal-subtitle">
                                                                                    Discounts :
                                                                                </div>
                                                                                <p>{{ currencyFormatUsd($promotion->discounts) }}</p>
                                                                            </div>

                                                                            <div class="col-md-12">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="modal-subtitle">
                                                                                    Term and Condition :
                                                                                </div>
                                                                                <p>{!! $promotion->term !!}</p>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="modal-subtitle">
                                                                                    Description:
                                                                                </div>
                                                                                <p>{!! $promotion->description !!}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @canany(['posDev','posAuthor'])
                                                    {{-- MODAL EDIT PROMOTION --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="edit-promotion-{{ $promotion->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Promotion</div>
                                                                    </div>
                                                                    <form id="update-promotion-{{ $promotion->id }}" action="/fupdate-promotion/{{ $promotion->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        {{ csrf_field() }}
                                                                        
                                                                        <div class="bannerzone text-center m-b-8">
                                                                            <label for="banner">Promotion Banner</label>
                                                                            <div class="banner-preview-div">
                                                                                <img src="{{ asset('/storage/promotion/cover/'.$promotion->cover) }}" alt="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="cover">Banner Image<span>*</span></label><br>
                                                                            <input type="file" name="cover" id="banner" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Banner" value="{{ old('cover') }}">
                                                                            @error('cover')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="status">Status</label>
                                                                            <select name="status" id="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                                                @if ($promotion->status == "Draft")
                                                                                    <option selected value="{{ $promotion->status }}">{{ $promotion->status }}</option>
                                                                                    <option value="Active">Active</option>
                                                                                @else
                                                                                    <option selected value="{{ $promotion->status }}">{{ $promotion->status }}</option>
                                                                                    <option value="Draft">Draft</option>
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="name">Promotion Name</label>
                                                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Name" value="{{ $promotion->name }}" required>
                                                                            @error('name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="discounts">Discounts</label>
                                                                            <div class="btn-icon">
                                                                                <span>$</span>
                                                                                <input type="number" min="1" id="discounts" name="discounts" class="input-icon form-control @error('discounts') is-invalid @enderror" placeholder="Minimum: $1" value="{{ $promotion->discounts }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="periode_start">Start Period</label>
                                                                            <input readonly type="text" id="periode_start" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Start Period" value="{{ date('d M y',strtotime($promotion->periode_start)) }}" required>
                                                                            @error('periode_start')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="periode_end">End Period</label>
                                                                            <input readonly type="text" id="periode_end" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="End Period" value="{{ date('d M y',strtotime($promotion->periode_end)) }}" required>
                                                                            @error('periode_end')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="term">Term and Condition</label>
                                                                            <textarea id="term" name="term" class="textarea_editor form-control @error('term') is-invalid @enderror" placeholder="Optional" value="{{ $promotion->term }}">{!! $promotion->term !!}</textarea>
                                                                            @error('term')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="description">Description</label>
                                                                            <textarea id="description" name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Optional" value="{{ $promotion->description }}">{!! $promotion->description !!}</textarea>
                                                                            @error('description')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-notif">This form is used to generate a booking code, Please ensure that all inputs are filled with accurate data before publishing.</div>
                                                                        <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                                        <input id="editor_id" name="editor_id" value="{{ Auth::user()->id }}" type="hidden">
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="update-promotion-{{ $promotion->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcanany
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-xl-12">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> No Promotion in this page, add one!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 text-right">
                                                <a href="#" data-toggle="modal" data-target="#add-promotion"><button class="btn btn-primary"><i class="ion-plus-round"></i> Create Promotion</button></a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- MODAL ADD PROMOTION --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="add-promotion" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Promotion</div>
                                                    </div>
                                                    <form id="create-promotion" action="/fadd-promotion" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group">
                                                            <div class="dropzone text-center pd-20 m-b-18">
                                                                <label for="cover">Cover Image</label>
                                                                <div class="cover-preview-div">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="cover">Cover Image<span>*</span></label><br>
                                                            <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                            @error('cover')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name">Promotion Name</label>
                                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Name" value="{{ old('name') }}" required>
                                                            @error('name')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group ">
                                                            <label for="discounts">Discounts</label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="number" min="1" id="discounts" name="discounts" class="input-icon form-control @error('discounts') is-invalid @enderror" placeholder="Minimum: $1" value="{{ old("discounts") }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="periode_start">Start Period</label>
                                                            <input readonly type="text" id="periode_start" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Start Period" value="{{ old('periode_start') }}" required>
                                                            @error('periode_start')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="periode_end">End Period</label>
                                                            <input readonly type="text" id="periode_end" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="End Period" value="{{ old('periode_end') }}" required>
                                                            @error('periode_end')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="term">Term and Condition</label>
                                                            <textarea id="term" name="term" class="textarea_editor form-control @error('term') is-invalid @enderror" placeholder="Optional" value="{{ old('term') }}"></textarea>
                                                            @error('term')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="description" >Description</label>
                                                            <textarea id="description" name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Optional" value="{{ old('description') }}"></textarea>
                                                            @error('description')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-notif">This form is used to create a promotion, Please ensure that all inputs are filled with accurate data before publishing.</div>
                                                        <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="create-promotion" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            @if (isset($activepromotion) or isset($draft_promotions) or isset($usedup_promotions) or isset($pending_promotions) or isset($invalid_promotions) or isset($Expired_promotions))
                                <div class="counter-container">
                                    @if (count($activepromotion)>0)
                                        <a href="#activepromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activepromotion->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draft_promotions)>0)
                                        <a href="#draftpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($usedup_promotions)>0)
                                        <a href="#useduppromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-usedup">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $usedup_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Used up</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($pending_promotions)>0)
                                        <a href="#pendingpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($invalid_promotions)>0)
                                        <a href="#invalidpromotions">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($expired_promotions)>0)
                                        <a href="#expiredpromotions">
                                            <div class="widget-expired">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-expired">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $expired_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Expired</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($rejected_promotions)>0)
                                        <a href="#rejectedpromotions">
                                            <div class="height-100-p widget-rejected">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $rejected_promotions->count() }} Promotion</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
function searchPromotionByName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchPromotionByName");
    filter = input.value.toUpperCase();
    table = document.getElementById("tbPartners");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>
<script>
function searchPartnerByCode() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchPartnerByCode");
    filter = input.value.toUpperCase();
    table = document.getElementById("tbPartners");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>

