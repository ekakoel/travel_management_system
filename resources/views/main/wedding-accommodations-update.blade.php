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
                                <i class="icon-copy fa fa-pencil"></i>@lang('messages.Update Wedding Accommodation')
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="wedding-planner">@lang('messages.Wedding Planner')</a></li>
                                    <li class="breadcrumb-item"><a href="edit-wedding-planner-{{ $wedding_planner->id }}">{{ $bride->groom." & ".$bride->bride }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@lang('messages.Wedding Accommodation')</li>
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
                                    <i class="icon-copy fa fa-hotel"></i>@lang('messages.Accommodations')
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-12">
                                    @if (count($accommodation_invs)>0)
                                        @php
                                            $acc_no = 1;
                                        @endphp
                                        <table id="tbHotels" class="data-table table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">@lang('messages.No')</th>
                                                    <th style="width: 30%;">@lang('messages.Hotel')</th>
                                                    <th style="width: 20%;">@lang('messages.Room')</th>
                                                    <th style="width: 20%;">@lang('messages.Guests')</th>
                                                    <th style="width: 15%;">@lang('messages.Status')</th>
                                                    <th style="width: 10%;">@lang('messages.Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        {{ $acc_no }}
                                                    </td>
                                                    <td>
                                                        {{ $hotel->name }}
                                                    </td>
                                                    <td>
                                                        {{ $room->rooms }}
                                                    </td>
                                                    <td>
                                                        {{ "Mr. ".$bride->groom }}
                                                        @if ($bride->groom_chinese)
                                                            {{ "(".$bride->groom_chinese.")" }}
                                                        @endif
                                                        ,
                                                        {{ "Mrs. ".$bride->groom }}
                                                        @if ($bride->bride_chinese)
                                                            {{ "(".$bride->bride_chinese.")" }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @lang('messages.Include')
                                                    </td>
                                                    <td class="text-center">
                                                        -
                                                    </td>
                                                </tr>
                                                @foreach ($accommodation_invs as $accommodation_inv)
                                                    @php
                                                        $room_inv = $rooms->where('id',$accommodation_inv->rooms_id)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            {{ ++$acc_no }}
                                                        </td>
                                                        <td>
                                                            {{ $hotel->name }}
                                                        </td>
                                                        <td>
                                                            {{ $room_inv->rooms }}
                                                        </td>
                                                        <td>
                                                            {{ $accommodation_inv->guest_detail }}
                                                        </td>
                                                        <td>
                                                            @lang('messages.Include')
                                                        </td>
                                                        <td class="text-center">
                                                            -
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="page-notification">
                                            @lang('messages.Invitation not found!')<br>
                                            @lang('messages.You cannot add accommodation because the invited guests are not found, please add invitations first so that you can add accommodation!')
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-box m-b-18">
                            <div class="card-box-title m-b-18">
                                <div class="subtitle">
                                    <i class="icon-copy fa fa-plus"></i>@lang('messages.Add Invitations')
                                </div>
                            </div>
                            <form id="addWeddingAccommodations" action="/fadd-wedding-accommodation/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="wedding_room_id[]">Room <span>*</span></label>
                                                <select id="wedding_room_id[]" name="wedding_room_id[]" class="custom-select col-12 @error('wedding_room_id[]') is-invalid @enderror" required>
                                                    <option selected value="">Select room</option>
                                                    @foreach ($rooms as $sroom)
                                                        <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                                    @endforeach
                                                </select>
                                                @error('wedding_room_id[]')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                <input onchange="fRequest()" id="number_of_guests[]" type="number" min="1" max="{{ $room->capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>
                                                @error('number_of_guests[]')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="guest_detail[]">@lang('messages.Guest Name')</label>
                                                <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>
                                                @error('guest_detail[]')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-3" style="place-self: padding-bottom: 6px;">
                                            <div class="form-group">
                                                <label for="extra_bed_id[]">@lang('messages.Extra Bed')</label>
                                                <select name="extra_bed_id[]" id="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required>
                                                    <option selected value="0">@lang('messages.Select extra bed')</option>
                                                    @foreach ($extrabed as $eb)
                                                        <option value="{{ $eb->id }}">@lang('messages.'.$eb->name) @lang('messages.'.$eb->type)</option>
                                                    @endforeach
                                                </select>
                                                @error('extra_bed[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <hr class="form-hr">
                                        </div>


                                    <div class="after-add-more"></div>
                                    <div class="col-12 col-sm-12 col-md-12 text-right">
                                        <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More</button>
                                    </div>
                                </div>
                                <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                <div class="card-box-footer">
                                    <button type="submit" form="addWeddingAccommodations" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="wedding_room_id[]">Room <span>*</span></label>
                                            <select id="wedding_room_id[]" name="wedding_room_id[]" class="custom-select @error('wedding_room_id[]') is-invalid @enderror" required>
                                                <option selected value="">Select room</option>
                                                @foreach ($rooms as $sroom)
                                                    <option value="{{ $sroom->id }}">{{ $sroom->rooms }}</option>
                                                @endforeach
                                            </select>
                                            @error('wedding_room_id[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                            <input onchange="fRequest()" id="number_of_guests[]" type="number" min="1" max="{{ $room->capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>
                                            @error('number_of_guests[]')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="guest_detail[]">@lang('messages.Guest Name')</label>
                                            <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>
                                            @error('guest_detail[]')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3" style="place-self: padding-bottom: 6px;">
                                        <div class="form-group">
                                            <label for="extra_bed_id[]">@lang('messages.Extra Bed')</label>
                                            <select name="extra_bed_id[]" id="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required>
                                                <option selected value="0">@lang('messages.Select extra bed')</option>
                                                @foreach ($extrabed as $eb)
                                                    <option value="{{ $eb->id }}">@lang('messages.'.$eb->name) @lang('messages.'.$eb->type)</option>
                                                @endforeach
                                            </select>
                                            @error('extra_bed[]')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                   
                                    <div class="btn-remove-add-more" data-toggle="tooltip" data-placement="top" title="Remove">
                                        <button class="remove" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
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
