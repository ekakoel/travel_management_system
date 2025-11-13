@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            @if (count($errors) > 0)
                <div class="alert-error-code">
                    <div class="alert alert-danger w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert-error-code">
                    <div class="alert alert-success w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="title">
                            <i class="dw dw-hotel" aria-hidden="true"></i>{{ $hotel->name }}
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="/weddings">@lang('messages.Wedding Venues')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{!! $hotel->name !!}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                {{-- ATTENTIONS MOBILE --}}
                <div class="col-md-4 mobile">
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            @include('main.wedding-usr.wedding-venue')
                            @include('main.wedding-usr.wedding-ceremony-venue')
                            @include('main.wedding-usr.wedding-reception-venue')
                            @include('main.wedding-usr.wedding-package')
                        </div>
                    </div>
                </div>

                {{-- ATTENTIONS DESKTOP --}}
                <div class="col-md-4 desktop">
                    @if (count($attentions)>0)
                        <div class="row m-b-18">
                            @include('layouts.attentions')
                        </div>
                    @endif
                    {{-- @if (count($wedding_planners)>0)
                        <div class="row m-b-18">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <div class="card-box-form-title">
                                        <div class="form-title"><i class="icon-copy fi-page-edit"></i> @lang("messages.Wedding Planner")</div>
                                    </div>
                                    <div class="cardbox-content">
                                        <div class="card-notification">@lang('messages.Your wedding planner at this hotel')</div>
                                        <hr class="form-hr">
                                        @foreach ($wedding_planners as $no_wp=>$wp)
                                            @php
                                                $wp_bride = $brides->where('id',$wp->bride_id)->first();
                                            @endphp
                                            <ul>
                                                <li>
                                                    {{ ++$no_wp.". " }}
                                                    {{ "Mr.".$wp_bride->groom }}
                                                    @if ($wp_bride->groom_chinese)
                                                        {{ " (".$wp_bride->groom_chinese.")" }}
                                                    @endif
                                                    & 
                                                    {{ "Mrs.".$wp_bride->bride }}
                                                    @if ($wp_bride->bride_chinese)
                                                        {{ " (".$wp_bride->bride_chinese.") " }}
                                                    @endif
                                                    @if ($wp->wedding_date > $now)
                                                        - {{ date('Y-m-d',strtotime($wp->wedding_date)) }}
                                                    @endif
                                                    <a href="/edit-wedding-planner-{{ $wp->id }}" class="text-link">
                                                        <i class="fa fa-pencil"></i> @lang('messages.Edit')
                                                    </a>
                                                </li>
                                            </ul>
                                            
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @include('form.wedding-planner-hotel') --}}
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection

