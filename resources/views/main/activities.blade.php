@section('title', __('messages.Activities'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-12">
                    <div class="promotion-bookingcode">
                        @if (isset($promotions))
                            @foreach ($promotions as $promotion)
                                <div class="bookingcode-card">
                                    <div class="icon-card promotion">
                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                    </div>
                                    <div class="content-card">
                                        <div class="code">{{ $promotion->name }}</div>
                                        <div class="text-card">@lang('messages.Promo Period')</div>
                                        <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                    </div>
                                    <div class="content-card-promo">
                                        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="fa fa-child"></i> @lang("messages.Activities")</div>
                        </div>
                        {{-- SEARCH ACTIVITY --}}
                        <form action="/search-activities" method="POST" role="search";>
                            {{ csrf_field() }}
                            <div class="search-container flex-end">
                                <div class="search-item">
                                    <input type="text" class="form-control" name="location" placeholder="@lang('messages.Search by location')" value="{{ old('location') }}">
                                </div>
                                <div class="search-item">
                                    <select id="activities_type" name="activities_type" value="{{ old('activities_type') }}" class="custom-select @error('activities_type') is-invalid @enderror">
                                        <option value="">@lang('messages.Search by Type')</option>
                                        @foreach ($type as $type)
                                            <option value="{{ $type->type }}">@lang('messages.'.$type->type)</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="search-item">
                                    <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                </div>
                                <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Search')</button>
                            </div>
                        </form>
                        {{-- LIST ACTIVITIES --}}
                        <div class="card-box-content">
                            @foreach ($activities as $activity)
                                <div class="card">
                                    <div class="image-container">
                                        <div class="first">
                                            <div class="card-lable">
                                                <div class="meta-box">
                                                    <p><i class="icon-copy fa fa-map-marker" aria-hidden="true"> </i>{{ " ".$activity->location }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="/activity-{{ $activity->code }}">
                                            <img src="{{ asset('storage/activities/activities-cover/' . $activity->cover) }}" class="img-fluid rounded thumbnail-image">
                                            <div class="card-detail-title">{{ $activity->name }}</div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination">
                            <div class="pagination-panel">
                                {{ $activities->links() }}
                            </div>
                            <div class="pagination-desk">
                                {{ $activities->total() }} <span>@lang('messages.Hotels Available')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
@endsection
