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
                                <i class="icon-copy fa fa-pencil"></i>@lang('messages.Update Wedding Invitations')
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="wedding-planner">@lang('messages.Wedding Planner')</a></li>
                                    <li class="breadcrumb-item"><a href="edit-wedding-planner-{{ $wedding_planner->id }}">{{ $wedding_planner->groom_name." & ".$wedding_planner->bride_name }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@lang('messages.Invitations')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                {{-- ALERT --}}
                @if (count($errors) > 0)
                    @include('partials.alert', ['type' => 'danger', 'messages' => $errors->all()])
                @endif
                @if (\Session::has('success'))
                    @include('partials.alert', ['type' => 'success', 'messages' => [\Session::get('success')]])
                @endif
                @if (\Session::has('danger'))
                    @include('partials.alert', ['type' => 'danger', 'messages' => [\Session::get('danger')]])
                @endif
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box m-b-18">
                            <div class="card-box-title m-b-18">
                                <div class="subtitle">
                                    <i class="icon-copy fa fa-users"></i>@lang('messages.Invitations')
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <table id="tbHotels" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 5%;">No</th>
                                                <th data-priority="2" style="width: 25%;">Name</th>
                                                <th style="width: 25%;">ID/Passport</th>
                                                <th style="width: 15%;">Phone</th>
                                                <th style="width: 10%;">Country</th>
                                                <th class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invitations as $no=>$invitation)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        @if ($invitation->sex == 'f')
                                                            Mrs.
                                                        @else
                                                            Mr.
                                                        @endif
                                                        {{ $invitation->name }}
                                                        @if ($invitation->chinese_name)
                                                            {{ " (".$invitation->chinese_name.")" }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $invitation->passport_no }}
                                                    </td>
                                                    <td>
                                                        {{ $invitation->phone }}
                                                    </td>
                                                    <td>
                                                        {{ $invitation->country }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="table-action">
                                                            <a href="#" data-toggle="modal" data-target="#update-invitation-{{ $invitation->id }}">
                                                                <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                            </a>
                                                            <form class="display-content" action="/fdelete-wedding-invitations/{{ $invitation->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                    <div class="modal fade" id="update-invitation-{{ $invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i>@lang('messages.Update Invitation')</div>
                                                                    </div>
                                                                    <form id="updateInvitation-{{ $invitation->id }}" action="/fupdate-wedding-invitations/{{ $invitation->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="sex">Sex <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <select name="sex" class="custom-select @error('sex') is-invalid @enderror" required>
                                                                                                @if ($invitation->sex == 'f')
                                                                                                    <option selected value="{{ $invitation->sex }}">@lang('messages.Female')</option>
                                                                                                    <option value="m">@lang('messages.Male')</option>
                                                                                                @else
                                                                                                    <option selected value="{{ $invitation->sex }}">@lang('messages.Male')</option>
                                                                                                    <option value="f">@lang('messages.Female')</option>
                                                                                                @endif
                                                                                            </select>
                                                                                            @error('sex')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="name" class="form-label">Name</label>
                                                                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert name" value="{{ $invitation->name }}" required>
                                                                                        @error('name')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="chinese_name" class="form-label">Chinese Name</label>
                                                                                        <input type="text" name="chinese_name" class="form-control @error('chinese_name') is-invalid @enderror" placeholder="Insert chinese name" value="{{ $invitation->chinese_name }}">
                                                                                        @error('chinese_name')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="phone">Telephone</label>
                                                                                        <div class="btn-icon">
                                                                                            <span><i class="icon-copy fa fa-mobile" aria-hidden="true"></i></span>
                                                                                            <input type="text" id="phone" name="phone"  class="form-control phone @error('phone') is-invalid @enderror" value="{{ $invitation->phone }}">
                                                                                            @error('phone')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="country">Country <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <select name="country" class="custom-select @error('country') is-invalid @enderror" value="{{ old('country') }}" required>
                                                                                                <option selected value="{{ $invitation->country }}">{{ $invitation->country }}</option>
                                                                                                @foreach ($countries as $country)
                                                                                                @if ($country->country_name != $invitation->country)
                                                                                                    <option value="{{ $country->country_name }}">{{ $country->country_name." (".$country->code2.")" }}</option>
                                                                                                @endif
                                                                                                @endforeach
                                                                                            </select>
                                                                                            @error('country')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="passport_no" class="form-label">ID/Passport Number</label>
                                                                                        <input type="text" name="passport_no" class="form-control @error('passport_no') is-invalid @enderror" placeholder="Insert ID or Passport number" value="{{ $invitation->passport_no }}" required>
                                                                                        @error('passport_no')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="updateInvitation-{{ $invitation->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Save')</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                
                            </div>
                        </div>
                        <div class="card-box m-b-18">
                            <div class="card-box-title m-b-18">
                                <div class="subtitle">
                                    <i class="icon-copy fa fa-plus"></i>@lang('messages.Add Invitations')
                                </div>
                            </div>
                            <form id="addWeddingInvitations" action="/fadd-wedding-invitations/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sex[]">Sex <span>*</span></label>
                                            <div class="btn-icon">
                                                <select name="sex[]" class="custom-select @error('sex[]') is-invalid @enderror" required>
                                                    <option selected value="">@lang('messages.Select')</option>
                                                    <option value="m">@lang('messages.Male')</option>
                                                    <option value="f">@lang('messages.Female')</option>
                                                </select>
                                                @error('sex[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name[]" class="form-label">Name</label>
                                            <input type="text" name="name[]" class="form-control @error('name[]') is-invalid @enderror" placeholder="Insert name" value="{{ old('name[]') }}" required>
                                            @error('name[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="chinese_name[]" class="form-label">Chinese Name</label>
                                            <input type="text" name="chinese_name[]" class="form-control @error('chinese_name[]') is-invalid @enderror" placeholder="Insert chinese name" value="{{ old('chinese_name[]') }}">
                                            @error('chinese_name[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone[]">Telephone</label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fa fa-mobile" aria-hidden="true"></i></span>
                                                <input type="text" id="phone[]" name="phone[]"  class="form-control phone[] @error('phone[]') is-invalid @enderror" placeholder="Telephone number" value="{{ old('phone[]') }}">
                                                @error('phone[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country[]">Country <span>*</span></label>
                                            <div class="btn-icon">
                                                <select name="country[]" class="custom-select @error('country[]') is-invalid @enderror" value="{{ old('country[]') }}" required>
                                                    <option selected value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->country_name }}">{{ $country->country_name." (".$country->code2.")" }}</option>
                                                    @endforeach
                                                </select>
                                                @error('country[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="passport_no[]" class="form-label">ID/Passport Number</label>
                                            <input type="text" name="passport_no[]" class="form-control @error('passport_no[]') is-invalid @enderror" placeholder="Insert ID or Passport number" value="{{ old('passport_no[]') }}" required>
                                            @error('passport_no[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr class="form-hr">
                                    </div>
                                    <div class="after-add-more"></div>
                                    <div class="col-12 col-sm-12 col-md-12 text-right">
                                        <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More Invitation</button>
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    <input type="hidden" name="wedding_planner_id" value="{{ $wedding_planner->id }}">
                                    <button type="submit" form="addWeddingInvitations" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                    <a href="/edit-wedding-planner-{{ $wedding_planner->id }}">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fi-arrow-left"></i> @lang('messages.Back')</button>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- ADD MORE SERVICE --}}
                    <div class="copy hide">
                        <div class="col-md-12">
                            <div class="control-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sex[]">Sex <span>*</span></label>
                                            <div class="btn-icon">
                                                <select name="sex[]" class="custom-select @error('sex[]') is-invalid @enderror" required>
                                                    <option selected value="">@lang('messages.Select')</option>
                                                    <option value="m">@lang('messages.Male')</option>
                                                    <option value="f">@lang('messages.Female')</option>
                                                </select>
                                                @error('sex[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name[]" class="form-label">Name</label>
                                            <input type="text" name="name[]" class="form-control @error('name[]') is-invalid @enderror" placeholder="Insert name" value="{{ old('name[]') }}" required>
                                            @error('name[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="chinese_name[]" class="form-label">Chinese Name</label>
                                            <input type="text" name="chinese_name[]" class="form-control @error('chinese_name[]') is-invalid @enderror" placeholder="Insert chinese name" value="{{ old('chinese_name[]') }}">
                                            @error('chinese_name[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone[]">Telephone</label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fa fa-mobile" aria-hidden="true"></i></span>
                                                <input type="text" id="phone[]" name="phone[]"  class="form-control phone[] @error('phone[]') is-invalid @enderror" value="{{ old('phone[]') }}">
                                                @error('phone[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country[]">Country <span>*</span></label>
                                            <div class="btn-icon">
                                                <select name="country[]" class="custom-select @error('country[]') is-invalid @enderror" value="{{ old('country[]') }}" required>
                                                    <option selected value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->country_name }}">{{ $country->country_name." (".$country->code2.")" }}</option>
                                                    @endforeach
                                                </select>
                                                @error('country[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="passport_no[]" class="form-label">ID/Passport Number</label>
                                            <input type="text" name="passport_no[]" class="form-control @error('passport_no[]') is-invalid @enderror" placeholder="Insert ID or Passport number" value="{{ old('passport_no[]') }}" required>
                                            @error('passport_no[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="btn-remove-add-more">
                                        <button class="remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-md-12">
                                        <hr class="form-hr">
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
    <script type="text/javascript">
        $(document).ready(function() {
            var ro = 1;
            var limit = 5;
            var t = 1
            $(".add-more").click(function(){ 
                if (t < limit) {
                    t++;
                    ro++;
                    var html = $(".copy").html();
                    $(".after-add-more").before(html);
                }
            });
            $("body").on("click",".remove",function(){ 
                $(this).parents(".control-group").remove();
                t--;
            });
        });
    </script>
@endsection
