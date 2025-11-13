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
                                <i class="icon-copy fi-page-edit"></i>{{ $bride->groom }}{{ $bride->groom_chinese ? "(".$bride->groom_chinese.")" : "" }} & {{ $bride->bride }}{{ $bride->bride_chinese ? "(".$bride->bride_chinese.")" : ""}}
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="wedding-planner">@lang('messages.Wedding Planner')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ 'Mr.'.$bride->groom." & Mrs.".$bride->bride }}</li>
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
                        <div class="card-box">
                            <div class="card-box-form-title">
                                <div class="form-title"><i class="icon-copy fi-page-edit"></i> @lang("messages.Wedding Planner")</div>
                            </div>
                            <div class="cardbox-content">
                                <form id="add-wedding-planner" action="/fadd-wedding-planner" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="wedding_location">@lang("messages.Wedding Location") <span>*</span></label>
                                        
                                        <select class="custom-select input-icon @error('wedding_location') is-invalid @enderror" id="wedding_location" name="wedding_location" required>
                                            <option selected value="">Select Location</option>
                                            @foreach ($hotels as $wedding_location)
                                                @if (count($wedding_location->weddings)>0)
                                                    <option value="{{ $wedding_location->id }}">{{ $wedding_location->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        
                                    </div>
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
                                    <input type="hidden" id="page_url" name="page_url">
                                    <input type="hidden" name="key" value="0">
                                </form>
                            </div>
                            <div class="card-box-footer">
                                <button type="submit" form="add-wedding-planner" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle"></i> @lang("messages.Create")</button>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var currentPageUrl = window.location.href;
                                document.getElementById('page_url').value = currentPageUrl;
                            });
                        </script>
                        <script>
                            $( function() {
                            $( "#inputWeddingDate" ).datepicker({                  
                                minDate: moment().add(7,'days').toDate(),
                            });
                            });
                        </script>
                    </div>
                </div>
