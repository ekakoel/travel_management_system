@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>

        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy fi-page-edit"></i>@lang('messages.Wedding Planners')
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Wedding Planners')</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
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
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">@lang('messages.Upcoming Wedding Plans')</div>
                                </div>
                                @if (count($wedding_planners)>0)
                                    <div class="input-container">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchByWeddingDate" type="text" onkeyup="searchByWeddingDate()" class="form-control" name="search-wedding-by-wedding-date" placeholder="@lang('messages.Search by wedding date')">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchHotelByBride" type="text" onkeyup="searchHotelByBride()" class="form-control" name="search-wedding-by-bride" placeholder="@lang('messages.Search by Bride')">
                                        </div>
                                    </div>
                                    
                                    <table id="tbHotels" class="data-table table stripe hover nowrap dataTable">
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 20%;">Wedding Date</th>
                                                <th style="width: 30%;">Bride</th>
                                                <th style="width: 20%;" class="datatable-nosort">Invitations</th>
                                                <th style="width: 20%;" class="datatable-nosort">Status</th>
                                                <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($wedding_planners as $no=>$wedding_planner)
                                                <tr>
                                                    <td>
                                                        {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) }}<br>
                                                        {{ "@".$wedding_planner->hotel->name }}
                                                    </td>
                                                    <td>
                                                        {{ $wedding_planner->bride->groom }}{{ $wedding_planner->bride->groom_chinese ? "(".$wedding_planner->bride->groom_chinese.")" : "" }} & {{ $wedding_planner->bride->bride }}{{ $wedding_planner->bride->bride_chinese ? "(".$wedding_planner->bride->bride_chinese.")" : ""}}
                                                    </td>
                                                    
                                                    
                                                    <td>
                                                        {{ $wedding_planner->number_of_invitations }}
                                                    </td>
                                                    <td>
                                                        {{ $wedding_planner->status }}
                                                    </td>
                                                        <td class="text-center">
                                                            <div class="table-action">
                                                                <a href="/edit-wedding-planner-{{ $wedding_planner->id }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/fdelete-wedding-planner/{{ $wedding_planner->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                </tr>
                                            @endforeach
                                            
                                        </tbody>
                                    </table>
                                @else
                                    <div class="page-notification">
                                        <i class="icon-copy fa fa-info-circle" aria-hidden="true"></i>
                                        @lang('messages.Wedding Planners') <br>
                                        @lang('messages.Wedding planners can help you plan your wedding more easily, with various customizable features to suit your needs, and they are integrated with the available service system. Create your wedding plan now and experience its benefits!')
                                    </div>
                                @endif
                                
                               
                                <div class="card-box-footer">
                                    <a href="#" data-toggle="modal" data-target="#add-new-wedding-planner"><button class="btn btn-primary"><i class="ion-plus-round"></i> @lang('messages.New Wedding Planner')</button></a>
                                </div>
                                <div class="modal fade" id="add-new-wedding-planner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-plus"></i> @lang('messages.New Wedding Planner')</div>
                                                </div>
                                                <form id="addNewWeddingPlanner" action="/fadd-wedding-planner" method="post" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="hotel_id">@lang("messages.Wedding Location") <span>*</span></label>
                                                                <select class="custom-select input-icon @error('hotel_id') is-invalid @enderror" id="hotel_id" name="hotel_id" required>
                                                                    <option selected value="">@lang('messages.Select Hotel')</option>
                                                                    @foreach ($hotels as $hotel)
                                                                        @if (count($hotel->wedding_venue)>0)
                                                                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="groom_name">@lang("messages.Groom's Name") <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy fi-torso"></i></span>
                                                                    <input type="text" name="groom_name" id="groom_name" class="form-control input-icon @error('groom_name') is-invalid @enderror" placeholder="@lang("messages.Groom's Name")" value="{{ old('groom_name') }}" required>
                                                                </div>
                                                                @error('groom_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bride_name">@lang("messages.Bride's Name") <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy fi-torso-female"></i></span>
                                                                    <input type="text" name="bride_name" id="bride_name" class="form-control input-icon @error('bride_name') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" value="{{ old('bride_name') }}" required>
                                                                </div>
                                                                @error('bride_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">

                                                            <div class="form-group">
                                                                <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                                    <input name="wedding_date" type="text" id="inputWeddingDate" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('wedding_date') }}" required>
                                                                </div>
                                                                @error('wedding_date')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="number_of_invitations">@lang('messages.Number of Invitations') <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy fi-torsos-all"></i></span>
                                                                    <input name="number_of_invitations" type="number" min="1" id="number_of_invitations" class="form-control input-icon @error('number_of_invitations') is-invalid @enderror" placeholder="Insert number of invitations" type="text" value="{{ old('number_of_invitations') }}" required>
                                                                </div>
                                                                @error('number_of_invitations')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>

                                                        </div>
                                                        
                                                    </div>
                                                    
                                                   
                                                    <input type="hidden" id="page_url" name="page_url" value="wedding-planner">
                                                    <input type="hidden" name="key" value="0">
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="addNewWeddingPlanner" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Create')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    <script>
        function searchByWeddingDate() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchByWeddingDate");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbHotels");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
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
        function searchHotelByBride() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchHotelByBride");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbHotels");
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
@endsection