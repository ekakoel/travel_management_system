@extends('layouts.head')
@section('title', __('messages.Dashboard'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        @include('partials.alerts')
        <div class="pd-ltr-20">
            @if ($promotions->count() > 0)
                <div class="promotion-bookingcode">
                    @foreach ($promotions as $promotion)
                        @include('partials.promotion-card', compact('promotion'))
                    @endforeach
                </div>
            @endif
            @if (Auth::user()->status === 'Block')
                <div class="info">
                    <div style="color: brown">
                        @lang('messages.Your account has been disabled because it does not comply with the established terms.')
                    </div>
                </div>
            @else
                {{-- Hotel Section --}}
                @if (isset($menu_hotel) && $hotels->isNotEmpty())
                    @php
                        $displayedHotels = [];
                        $limit = 8;
                    @endphp
                    <div class="trans-box">
                        <div class="trans-box-title">
                            <i class="dw dw-hotel"></i> @lang('messages.Hotel Promotions')
                        </div>
                        <div class="trans-box-content">
                            @foreach ($hotels as $hotel)
                                @include('frontend.hotels.partials.hotel-card', compact('hotel'))
                            @endforeach
                        </div>
                        <div class="trans-box-footer">
                            <a href="/hotels">
                                <button class="btn btn-primary text-white">@lang('messages.Hotels') <i
                                        class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                            </a>
                        </div>
                    </div>
                @endif
                {{-- Transport Section --}}
                @if (isset($menu_transport) && $transports->isNotEmpty())
                    <div class="trans-box">
                        <div class="trans-box-title">
                            <i class="icon-copy ion-android-car"></i>@lang('messages.Transports')
                        </div>
                        <div class="trans-box-content">
                            @foreach ($transports as $transport)
                                @include('partials.transport-card', compact('transport'))
                            @endforeach
                        </div>
                        <div class="trans-box-footer">
                            <a href="/transports">
                                <button class="btn btn-primary text-white">@lang('messages.Transportation') <i
                                        class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                            </a>
                        </div>
                    </div>
                @endif
                {{-- Wedding Section --}}
                @if (isset($menu_wedding) && $weddings->isNotEmpty())
                    <div class="trans-box m-b-18">
                        <div class="trans-box-title">
                            <i class="icon-copy fi-torso-business"></i><i
                                class="icon-copy fi-torso-female"></i>@lang('messages.Weddings')
                        </div>
                        <div class="trans-box-content">
                            @foreach ($weddings as $wedding)
                                @include('partials.wedding-card', compact('wedding'))
                            @endforeach
                        </div>
                        <div class="trans-box-footer">
                            <a href="/weddings">
                                <button class="btn btn-primary text-white">@lang('messages.Weddings') <i
                                        class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
