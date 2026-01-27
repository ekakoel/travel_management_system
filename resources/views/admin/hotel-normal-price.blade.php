<div id="normalPrice" class="row m-b-18">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <i class="fa fa-usd" aria-hidden="true"></i> Normal Price
            </div>
            <div class="card-box-body">
                <div class="search-container">
                    <div class="input-group-icon">
                        <i class="icon-copy fa fa-search" aria-hidden="true"></i>
                        <input id="searchPriceByRoom" type="text" onkeyup="searchPriceByRoom()" class="input-icon form-control" name="search-byroom" placeholder="Filter by room">
                    </div>
                </div>
                <table id="tbPrice" class="data-table table stripe nowrap" >
                    <thead>
                        <tr>
                            <th style="width: 30%;">Room</th>
                            <th style="width: 30%;">Stay Period</th>
                            <th style="width: 20%;">Kick Back</th>
                            <th style="width: 10%;">Published Rate</th>
                            <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            @forelse ($normal_prices as $price)
                                @php
                                    $kick_back = $price->kick_back > 0 ? $price->kick_back : 0;
                                    $public_rate = $price->calculatePrice($usdrates,$tax) - $kick_back;
                                @endphp
                                <tr>
                                    <td>
                                        <b>{{ $price->rooms?->rooms }}</b>
                                    </td>
                                    <td>
                                        <p>{{ dateFormat($price->start_date)." - ".dateFormat($price->end_date) }}</p>
                                    </td>
                                    <td>
                                        <div class="rate-usd">{{ $kick_back > 0 ? currencyFormatUsd($kick_back):"-" }}</div>
                                    </td>
                                    <td class="text-right" style="padding-right: 32px !important">
                                        <div class="rate-usd">{!! currencyFormatUsd($public_rate) !!}</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="table-action">
                                            <a href="#" data-toggle="modal" data-target="#detail-price-{{ $price->id }}">
                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                            </a>
                                            @canany(['posDev','posAuthor'])
                                                <a href="#" data-toggle="modal" data-target="#edit-price-{{ $price->id }}">
                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                                </a>
                                                <form action="/delete-price/{{ $price->id }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                </form>
                                            @endcanany
                                        </div>
                                    </td>
                                </tr>
                                @include('admin.partials.modal-detail-hotel-normal-price', compact("price"))
                                @canany(['posDev','posAuthor'])
                                    @include('admin.partials.modal-update-hotel-normal-price', compact("price","rooms"))
                                @endcanany
                            @empty
                                <tr>
                                    <td colspan="5">No active prices available for this room.</td>
                                </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-box-footer">
                @canany(['posDev','posAuthor'])
                    <a href="/add-hotel-price-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Add Normal Price">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Price</button>
                    </a>
                @endcanany
            </div>
        </div>
    </div>
</div>