@section('title', __('messages.Order'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <x-backend.page-hero>
                    <x-slot name="heading">
                        <i class="icon-copy fa fa-tags"></i>  @lang('messages.Orders')
                    </x-slot>
                </x-backend.page-hero>
                <div class="row">
                    
                </div>
            </div>
        </div>
    </div>
@endsection
