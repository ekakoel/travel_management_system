@section('title', __('messages.Manual Book'))
@section('content')
    @extends('layouts.head')
{{-- @extends('layouts.loader') --}}
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><span><i class="fa fa-book"></i></span>Manual Book</div>
                        </div>
                            <table class="data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">@lang('messages.No')</th>
                                        <th style="width: 50%">@lang('messages.Name')</th>
                                        <th style="width: 15%">@lang('messages.Language')</th>
                                        <th style="width: 15%">@lang('messages.Date')</th>
                                        <th style="width: 15%">@lang('messages.Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($manual_book as $no=>$manualbook)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $manualbook->name }}</td>
                                            @if ($manualbook->language == "id")
                                                <td>@lang('messages.Indonesia')</td>
                                            @elseif($manualbook->language == "en")
                                                <td>@lang('messages.English')</td>
                                            @elseif($manualbook->language == "zh")
                                                <td>@lang('messages.Chinese')</td>
                                            @else
                                                <td>-</td>
                                            @endif
                                            <td>{{ dateFormat($manualbook->created_at) }}</td>
                                            <td>
                                                <a class="action-btn" href="#" data-toggle="modal" data-target="#manual-book-{{ $manualbook->id }}">
                                                    <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        {{-- MODAL VIEW MANUAL BOOK --}}
                                        <div class="modal fade" id="manual-book-{{ $manualbook->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                    <div class="modal-body pd-5">
                                                        <embed src="storage/document/{!! $manualbook->file_name !!}" frameborder="10" width="100%" height="850px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                            
                       
                        <div class="card-box-footer">
        
                        </div>
                    </div>
                </div>
            </div>
           
           
            @include('layouts.footer')
        </div>
    </div>