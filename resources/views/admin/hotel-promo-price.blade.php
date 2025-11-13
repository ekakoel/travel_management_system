<div id="promo" class="row m-b-18">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <i class="fa fa-percent" aria-hidden="true"></i> Promotion Price
            </div>
            <div class="card-box-body">
                @if (count($promos) > 0)
                    <div class="search-container">
                        <div class="input-group-icon">
                            <i class="icon-copy fa fa-search" aria-hidden="true"></i>
                            <input id="searchPromoByName" type="text" onkeyup="searchPromoByName()" class="input-icon form-control" name="search-promo-byname" placeholder="Filter by name">
                        </div>
                    </div>
                    <table id="tbPromo" class="data-table table nowrap" >
                        <thead>
                            <tr>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 30%;">Period</th>
                                <th style="width: 30%;">Published Rate</th>
                                <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promos as $promo)
                                @php
                                    $promo_room = $rooms->where('id',$promo->rooms_id)->first();
                                @endphp
                                @if ($promo->book_periode_end < $now)
                                    <tr class="expired-background">
                                @elseif ($promo->book_periode_start <= $now && $promo->book_periode_end >= $now)
                                    <tr class="onbook-background">
                                @else
                                    <tr>
                                @endif
                                    <td>
                                        @if ($promo->promotion_type == "Hot Deal")
                                            <div class="promotion-name bg-red">{{ __('messages.'.$promo->promotion_type) }}</div>
                                        @elseif ($promo->promotion_type == "Best Choice")
                                            <div class="promotion-name bg-green">{{ __('messages.'.$promo->promotion_type) }}</div>
                                        @elseif ($promo->promotion_type == "Best Price")
                                            <div class="promotion-name bg-orange">{{ __('messages.'.$promo->promotion_type) }}</div>
                                        @elseif ($promo->promotion_type == "Special Offer")
                                            <div class="promotion-name bg-blue">{{ __('messages.'.$promo->promotion_type) }}</div>
                                        @endif
                                        <b>{{ $promo->name }}</b><br>
                                        <p>
                                            {{ $promo_room->rooms }}
                                            @if ($promo->minimum_stay > 1)
                                                (min: {{ $promo->minimum_stay }} nights)
                                            @else
                                                (min: {{ $promo->minimum_stay }} night)
                                            @endif
                                            <br>
                                        </p>
                                        @if ($promo->booking_code != "")
                                            <b>{{ $promo->booking_code }}</b><br>
                                        @endif
                                        @include('partials.status-icon', ['status' => $promo->status])
                                    </td>
                                    <td>
                                        <b>Booking Period</b>
                                        <p>{{ dateFormat($promo->book_periode_start) }} - {{ dateFormat($promo->book_periode_end) }}<br></p>
                                        @if ($promo->book_periode_end < $now)
                                            <div class="expired-ico">
                                                <img src="{{ asset ('storage/icon/expired.png') }}" alt="{{ $promo->name }}" loading="lazy">
                                            </div>
                                        @endif
                                        <b>Stay Period</b>
                                        <p>{{ dateFormat($promo->periode_start) }} - {{ dateFormat($promo->periode_end) }}</p>
                                    </td>
                                    <td>
                                        <div class="rate-usd">{!! currencyFormatUsd($promo->calculatePrice($usdrates, $tax)) !!}</div>
                                        <div class="rate-idr">{{ currencyFormatIdr($promo->calculatePrice($usdrates, $tax) * $usdrates->rate) }}</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="table-action">
                                            <a href="#" data-toggle="modal" data-target="#detail-promo-{{ $promo->id }}">
                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                            </a>
                                            @canany(['posDev','posAuthor'])
                                                <a href="#" data-toggle="modal" data-target="#edit-promo-{{ $promo->id }}">
                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                </a>
                                                <form action="/delete-promo/{{ $promo->id }}" method="post">
                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                    <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete">
                                                        <i class="icon-copy fa fa-trash"></i></button>
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            @endcanany
                                        </div>
                                    </td>
                                    {{-- MODAL DETAIL PROMO --}}
                                    @include('admin.partials.modal-detail-hotel-promo-price', compact("promo"))
                                    @canany(['posDev','posAuthor'])
                                        @include('admin.partials.modal-update-hotel-promo-price', compact("promo"))
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="notif">The hotel doesn't have a promotion yet, please add a promotion now!</div>
                @endif
            </div>
            <div class="card-box-footer">
                @canany(['posDev','posAuthor'])
                    <a href="/send-promo-to-specific-agent-{{ $hotel->id }}">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send to Specific Email</button>
                    </a>
                    <a href="/send-promo-to-agent-{{ $hotel->id }}">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send Email Promo</button>
                    </a>
                    <a href="#" data-toggle="modal" data-target="#add-promo-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Detail">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Promo</button>
                    </a>
                @endcanany
            </div>
            {{-- MODAL ADD PROMO --}}
            @canany(['posDev','posAuthor'])
                @include('admin.partials.modal-add-hotel-promo-price')
            @endcanany
        </div>
    </div>
</div>