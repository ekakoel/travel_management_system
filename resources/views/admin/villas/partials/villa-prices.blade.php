<div class="row" id="villaPrices">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="fa fa-usd" aria-hidden="true"></i> Prices</div>
            </div>
            @if (count($prices) > 0)
                <div class="input-container">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                        <input id="searchPriceByYears" type="text" onkeyup="searchPriceByYears()" class="form-control" name="search-byyear" placeholder="Filter by year">
                    
                    </div>
                </div>
                <table id="tbPrice" class="data-table table stripe nowrap" >
                    <thead>
                        <tr>
                            <th data-priority="1" style="width: 40%;">Stay Period</th>
                            <th style="width: 30%;">Kick Back</th>
                            <th style="width: 20%;">Published Rate</th>
                            <th data-priority="1" class="datatable-nosort text-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($prices as $price)
                                <tr>
                                    <td>
                                        <p>{{ date('Y-m-d',strtotime($price->start_date))." - ".date('Y-m-d',strtotime($price->end_date)) }}</p>
                                    </td>
                                    
                                    <td>
                                        @if ($price->kick_back > 0)
                                            <div class="rate-usd">{{ currencyFormatUsd($price->kick_back) }}</div>
                                        @else
                                            <div class="rate-usd">-</div>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="rate-usd">
                                            @if ($price->kick_back > 0)
                                                <span><strike>{{ currencyFormatUsd($price->calculatePrice($usdrates,$tax)) }}</strike></span>&nbsp;|
                                            @endif
                                            {{ currencyFormatUsd($price->public_rate) }}
                                        </div>
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
                                                <form action="{{ route('func.villa-price.delete',$price->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                </form>
                                            @endcanany
                                        </div>
                                    </td>
                                    {{-- MODAL PRICE DETAIL =========================================================================================================================================================--}}
                                    <div class="modal fade" id="detail-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="dw dw-eye"></i> Price</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card-subtitle">
                                                                USD Rate
                                                            </div>
                                                            <div class="card-text">
                                                                <p>{{ currencyFormatIdr($usdrates->rate) }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card-subtitle">
                                                                        Villa Name
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p>{{ $villa->name }}</p>
                                                                    </div>
                                                                    <div class="card-subtitle">
                                                                        Room
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p>{{ count($villa->rooms) > 1 ? count($villa->rooms)." rooms": "1 room" }}</p>
                                                                    </div>
                                                                    <div class="card-subtitle">
                                                                        Stay Period Start
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p>{{ dateFormat($price->start_date) }}</p>
                                                                    </div>
                                                                    <div class="card-subtitle">
                                                                        Stay Period End
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p>{{ dateFormat($price->end_date) }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card-subtitle">
                                                                        Contract Rate
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p class="rate-usd">{{ currencyFormatUsd($price->contractrate) }}</p>
                                                                    </div>
                                                                
                                                                    <div class="card-subtitle">
                                                                        Markup
                                                                    </div>
                                                                    <div class="card-text">
                                                                        <p class="rate-usd">{{ currencyFormatUsd($price->markup) }}</p>
                                                                    </div>
                                                                    @if ($price->kick_back > 0)
                                                                        <div class="card-subtitle">
                                                                            Kick Back
                                                                        </div>
                                                                        <div class="card-text">
                                                                            <p class="rate-kick-back">{{ currencyFormatUsd($price->kick_back) }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="card-text">
                                                                <div class="card-subtitle">Benefits</div>
                                                                <p class="m-b-8">
                                                                    <b>English:</b><br>
                                                                    {!! $price->benefits ? $price->benefits : "-" !!}
                                                                </p>
                                                                <p class="m-b-8">
                                                                    <b>Traditional:</b><br>
                                                                    {!! $price->benefits_traditional ? $price->benefits_traditional: "-" !!}
                                                                </p>
                                                                <p class="m-b-8">
                                                                    <b>Simplified:</b><br>
                                                                    {!! $price->benefits_simplified ? $price->benefits_simplified: "-" !!}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        @if ($price->kick_back >0)
                                                            <div class="col-12">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="card-subtitle">
                                                                            Published Rate
                                                                        </div>
                                                                        <div class="price-usd">
                                                                            <strike>{{ currencyFormatUsd($price->calculatePrice($usdrates,$tax)) }}</strike>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="card-subtitle">
                                                                            Published With Kick Back Rate
                                                                        </div>
                                                                        <div class="price-usd">
                                                                            {{ currencyFormatUsd($price->public_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="col-12">
                                                                <div class="card-subtitle">
                                                                    Published Rate
                                                                </div>
                                                                <div class="price-usd">
                                                                    {{ currencyFormatUsd($price->public_rate) }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-box-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- MODAL PRICE UPDATE =========================================================================================================================================================--}}
                                    @canany(['posDev','posAuthor'])
                                        <div class="modal fade" id="edit-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Price Edit</div>
                                                        </div>
                                                        <form id="updateVillaPrice-{{ $price->id }}" action="{{ route('func.villa-price.update',$price->id) }}" method="post" enctype="multipart/form-data">
                                                            @method('put')
                                                            {{ csrf_field() }}
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="start_date">Stay Period Start <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                                    <input readonly type="text" id="start_date" name="start_date" class="input-icon date-picker form-control @error('start_date') is-invalid @enderror" placeholder="Insert Markup" value="{{ date('d F Y', strtotime($price->start_date)) }}" required>
                                                                                    @error('start_date')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="end_date">Stay Period End <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                                    <input readonly type="text" id="end_date" name="end_date" class="input-icon date-picker form-control @error('end_date') is-invalid @enderror" placeholder="Insert Markup" value="{{ date('d F Y', strtotime($price->end_date)) }}" required>
                                                                                    @error('end_date')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="contract_rate">Contract Rate </label>
                                                                                <div class="btn-icon">
                                                                                    <span>Rp</span>
                                                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $price->contract_rate }}" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="markup">Markup </label>
                                                                                <div class="btn-icon">
                                                                                    <span>$</span>
                                                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $price->markup }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="kick_back">Kick Back</label>
                                                                                <div class="btn-icon">
                                                                                    <span>$</span>
                                                                                    <input type="number" id="kick_back" name="kick_back" class="input-icon form-control @error('kick_back') is-invalid @enderror" placeholder="Insert kick back" value="{{ $price->kick_back }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                         <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="tab-inner-title">Benefits</div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="benefits" class="form-label">English</label>
                                                                                        <textarea name="benefits" id="benefits" class="textarea_editor form-control @error('benefits') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $price->benefits !!}</textarea>
                                                                                        @error('benefits')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="benefits_traditional" class="form-label">Traditional</label>
                                                                                        <textarea name="benefits_traditional" id="benefits_traditional" class="textarea_editor form-control @error('benefits_traditional') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $price->benefits_traditional !!}</textarea>
                                                                                        @error('benefits_traditional')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="benefits_simplified" class="form-label">Simplified</label>
                                                                                        <textarea name="benefits_simplified" id="benefits_simplified" class="textarea_editor form-control @error('benefits_simplified') is-invalid @enderror" placeholder="Insert some text" type="text">{!! $price->benefits_simplified !!}</textarea>
                                                                                        @error('benefits_simplified')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                <input id="villa_id" name="villa_id" value="{{ $villa->id }}" type="hidden">
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="updateVillaPrice-{{ $price->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcanany
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            @else
                <div class="notif">!!! The villa doesn't have a price yet, please add a price now!</div>
            @endif
            @canany(['posDev','posAuthor'])
                <div class="card-box-footer">
                    <a href="{{ route('view.add-villa-price',$villa->id) }}" data-toggle="tooltip" data-placement="top" title="Add Normal Price">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Price</button>
                    </a>
                </div>
            @endcanany
        </div>
    </div>
</div>