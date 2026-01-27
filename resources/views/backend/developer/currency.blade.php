@section('title', __('messages.Currency'))
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
                                <i class="icon-copy fa fa-money" aria-hidden="true"></i> Currency
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Currency</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="info-action">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            {{-- USD RATE --}}
                            <div class="col-md-12">
                                <div class="card-box m-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-usd" aria-hidden="true"></i> USD Rate</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-6 col-sm-6">
                                            <div class="subtitle">Recent Rate</div>
                                        </div>
                                        <div class="col-6 col-md-6 col-sm-6 text-right">
                                            <div class="usd-rate">{{ currencyFormatIdr($usd_rate) }}</div>
                                            <p>{{ $usd_rate }}</p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-ptext-margin">
                                                <div class="row">
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-up" aria-hidden="true"></i></span> <b>SELL</b></div>
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-down" aria-hidden="true"></i></span> <b>BUY</b></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($usdrates->sell) }}</div></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($usdrates->buy) }}</div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12 col-sm-12 text-right">
                                            <p><i>last updated : {{ date('d M Y (H:i)', strtotime($usdrates->updated_at)) }}</i></p>
                                        </div>
                                        @canany(['posDev','posAuthor'])
                                            <div class="edit-button">
                                                <a href="#" data-toggle="modal" data-target="#edit-usdrates-{{ $usdrates->id }}">
                                                    <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Update usd rate"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                </a>
                                            </div>
                                            {{-- MODAL EDIT USD RATE --}}
                                            <div class="modal fade" id="edit-usdrates-{{ $usdrates->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="fa fa-pencil"></i> Edit</div>
                                                            </div>
                                                            <form id="edit-usd-rate" action="/update-usdrates/{{ $usdrates->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <b>Recent USD Rate</b><br>
                                                                            <div class="title">{{ currencyFormatIdr($usdrates->rate) }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <hr class="form-hr">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="sell">Sell <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="sell" id="usdSell" wire:model="sell" class="form-control @error('sell') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $usdrates->sell }}" required>
                                                                                @error('sell')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="difference">Difference <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="difference" id="usdDifference" wire:model="difference" class="form-control @error('difference') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $usdrates->difference }}" required>
                                                                                @error('difference')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button class="btn btn-primary" type="submit" form="edit-usd-rate"><i class="icon-copy fa fa-check"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                            {{-- CNY RATE --}}
                            <div class="col-md-12">
                                <div class="card-box m-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-rmb" aria-hidden="true"></i> CNY Rate</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-6 col-sm-6">
                                            <div class="subtitle">Recent Rate</div>
                                        </div>
                                        <div class="col-6 col-md-6 col-sm-6 text-right">
                                            <div class="usd-rate">{{ currencyFormatIdr($cny_rate) }}</div>
                                            <p>{{ $cny_rate }}</p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-ptext-margin">
                                                <div class="row">
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-up" aria-hidden="true"></i></span> <b>SELL</b></div>
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-down" aria-hidden="true"></i></span> <b>BUY</b></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($cnyrates->sell) }}</div></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($cnyrates->buy) }}</div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12 col-sm-12 text-right">
                                            <p><i>last updated : {{ date('d M Y (H:i)', strtotime($cnyrates->updated_at)) }}</i></p>
                                        </div>
                                        @canany(['posDev','posAuthor'])
                                            <div class="edit-button">
                                                <a href="#" data-toggle="modal" data-target="#edit-cnyrates-{{ $cnyrates->id }}">
                                                    <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Update usd rate"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                </a>
                                            </div>
                                            {{-- MODAL EDIT CNY RATE --}}
                                            <div class="modal fade" id="edit-cnyrates-{{ $cnyrates->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="fa fa-pencil"></i> Edit</div>
                                                            </div>
                                                            <form id="edit-cny-rate" action="/update-cnyrates/{{ $cnyrates->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <b>Recent CNY Rate</b><br>
                                                                            <div class="title">{{ currencyFormatIdr($cnyrates->rate) }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <hr class="form-hr">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="sell">Sell <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="sell" id="cnySell" wire:model="sell" class="form-control @error('sell') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $cnyrates->sell }}" required>
                                                                                @error('sell')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="difference">Difference <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="difference" id="cnyDifference" wire:model="difference" class="form-control @error('difference') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $cnyrates->difference }}" required>
                                                                                @error('difference')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button class="btn btn-primary" type="submit" form="edit-cny-rate"><i class="icon-copy fa fa-check"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                            {{-- TWD RATE --}}
                            <div class="col-md-12">
                                <div class="card-box m-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle">NT<i class="icon-copy fa fa-usd" aria-hidden="true"></i> TWD Rate</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-6 col-sm-6">
                                            <div class="subtitle">Recent Rate</div>
                                        </div>

                                        <div class="col-6 col-md-6 col-sm-6 text-right">
                                            <div class="usd-rate">{{ currencyFormatIdr($twd_rate) }}</div>
                                            <p>{{ $twd_rate }}</p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-ptext-margin">
                                                <div class="row">
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-up" aria-hidden="true"></i></span> <b>SELL</b></div>
                                                    <div class="col-6"><span><i class="icon-copy fa fa-arrow-down" aria-hidden="true"></i></span> <b>BUY</b></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($twdrates->sell) }}</div></div>
                                                    <div class="col-6"><div class="usd-rate">{{ currencyFormatIdr($twdrates->buy) }}</div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12 col-sm-12 text-right">
                                            <p><i>last updated : {{ date('d M Y (H:i)', strtotime($twdrates->updated_at)) }}</i></p>
                                        </div>
                                        @canany(['posDev','posAuthor'])
                                            <div class="edit-button">
                                                <a href="#" data-toggle="modal" data-target="#edit-twdrates-{{ $twdrates->id }}">
                                                    <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Update twd rate"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                </a>
                                            </div>
                                            {{-- MODAL EDIT TWD RATE --}}
                                            <div class="modal fade" id="edit-twdrates-{{ $twdrates->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="fa fa-pencil"></i> Edit TWD Rate</div>
                                                            </div>
                                                            <form id="edit-twd-rate" action="/update-twdrates/{{ $twdrates->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <b>Recent TWD Rate</b><br>
                                                                            <div class="title">{{ currencyFormatIdr($twdrates->rate) }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <hr class="form-hr">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="sell">Sell <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="sell" id="twdSell" wire:model="sell" class="form-control @error('sell') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $twdrates->sell }}" required>
                                                                                @error('sell')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="difference">Difference <span>*</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input name="difference" id="twdDifference" wire:model="difference" class="form-control @error('difference') is-invalid @enderror" placeholder="Insert rate" type="text" value="{{ $twdrates->difference }}" required>
                                                                                @error('difference')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button class="btn btn-primary" type="submit" form="edit-twd-rate"><i class="icon-copy fa fa-check"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- TAX --}}
                    <div class="col-md-4">
                        <div class="card-box m-b-18">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-gavel" aria-hidden="true"></i> Tax Rate</div>
                            </div>
                            <div class="row">
                                <div class="col-9 col-md-8 col-sm-8">
                                    <div class="price-usd">{{ $tax->tax }} %</div>
                                    <p>last updated : {{ date('d M Y (H:i)', strtotime($tax->updated_at)) }}</p>
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="edit-button">
                                        <a href="#" data-toggle="modal" data-target="#edit-tax-{{ $tax->id }}">
                                            <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Update TAX"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                        </a>
                                    </div>
                                    {{-- MODAL EDIT TAX --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="edit-tax-{{ $tax->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="fa fa-pencil"></i> Edit TAX</div>
                                                    </div>
                                                    <form id="edit-tax" action="/update-tax/{{ $tax->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        {{ csrf_field() }}
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="lact-tax">Recent TAX</label>
                                                                    <div class="title">{{ $tax->tax }} %</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="tax">New TAX</label>
                                                                    <input name="tax" id="tax" wire:model="tax" class="form-control @error('tax') is-invalid @enderror" placeholder="New tax" type="text" required>
                                                                    @error('tax')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                            <input id="hotels_id" name="hotels_id" value="" type="hidden">
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button class="btn btn-primary" type="submit" form="edit-tax" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-check"></i> Save</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                    </div>
                    {{-- BANK ACCOUNT --}}
                    <div class="col-md-4">
                        <div class="card-box m-b-18">
                            <div class="card-box-title">
                                <div class="subtitle">
                                    <i class="icon-copy fa fa-bank" aria-hidden="true"></i>BANK Account
                                </div>
                            </div>
                            <div class="row">
                                @canany(['posDev','posAuthor'])
                                    <div class="edit-button">
                                        <a href="#" data-toggle="modal" data-target="#add-bank-account">
                                            <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Add BANK Account"><i class="icon-copy fa fa-plus" aria-hidden="true"></i></button>
                                        </a>
                                    </div>
                                    <div class="modal fade" id="add-bank-account" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add BANK Account</div>
                                                    </div>
                                                        <form id="add-bank" action="/fadd-bank-account" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="location">Location</label>
                                                                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Insert BANK location" value="{{ old('location') }}" required>
                                                                        @error('location')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="bank">BANK</label>
                                                                        <input type="text" name="bank" class="form-control @error('bank') is-invalid @enderror" placeholder="Insert BANK" value="{{ old('bank') }}" required>
                                                                        
                                                                        @error('bank')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="name">Name</label>
                                                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert name" value="{{ old('name') }}" required>
                                                                        
                                                                        @error('name')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="account_idr">Account IDR</label>
                                                                        <input type="text" name="account_idr" class="form-control @error('account_idr') is-invalid @enderror" placeholder="Insert account IDR" value="{{ old('account_idr') }}" required>
                                                                        
                                                                        @error('account_idr')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="account_usd">Account USD</label>
                                                                            <input type="text" name="account_usd" class="form-control @error('account_usd') is-invalid @enderror" placeholder="Insert account IDR" value="{{ old('account_usd') }}">
                                                                        
                                                                        @error('account_usd')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="address">Address</label>
                                                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ old('address') }}">
                                                                        
                                                                        @error('address')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="swift_code">Swift Code</label>
                                                                        <input type="text" name="swift_code" class="form-control @error('swift_code') is-invalid @enderror" placeholder="Insert Swift Code" value="{{ old('swift_code') }}">
                                                                        
                                                                        @error('swift_code')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="telephone">Phone Number</label>
                                                                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" placeholder="Insert phone number" value="{{ old('telephone') }}">
                                                                        
                                                                        @error('telephone')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="add-bank" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                                <div class="col-md-12">
                                    @foreach ($bank_acc as $no=>$perbankan)
                                        <div class="position-relatif">
                                            <div class="inner-subtitle">
                                                {{ $perbankan->bank }} ({{ $perbankan->currency }})
                                            </div>
                                            <div class="card-ptext-margin" >
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">Account Name</div>
                                                    <div class="ptext-value">{{ $perbankan->account_name }}</div>
                                                    <div class="ptext-title">Account Number</div>
                                                    <div class="ptext-value">{{ $perbankan->account_number }}</div>
                                                    <div class="ptext-title">Location</div>
                                                    <div class="ptext-value">{{ $perbankan->location }}</div>
                                                    <div class="ptext-title">Address</div>
                                                    <div class="ptext-value">{{ $perbankan->address }}</div>
                                                    <div class="ptext-title">Telephone</div>
                                                    <div class="ptext-value">{{ $perbankan->telephone }}</div>
                                                    <div class="ptext-title">SWIFT Code</div>
                                                    <div class="ptext-value">{{ $perbankan->swift_code }}</div>
                                                    <div class="ptext-title">BANK Code</div>
                                                    <div class="ptext-value">{{ $perbankan->bank_code }}</div>
                                                </div>
                                            </div>
                                            @canany(['posDev','posAuthor'])
                                                <div class="btn-container">
                                                    <a href="#" data-toggle="modal" data-target="#edit-bank-account-{{ $perbankan->id }}">
                                                        <button class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Edit BANK Account"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                    </a>
                                                    <form action="/delete-bank-account/{{ $perbankan->id }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn delete-btn" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove BANK Account {{ $perbankan->id }}"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                    </form>
                                                    <div class="modal fade" id="edit-bank-account-{{ $perbankan->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="fa fa-pencil"></i> Edit BANK Account</div>
                                                                    </div>
                                                                    <div class="product-detail-wrap">
                                                                        <form id="edit-bank" action="/fupdate-bank-account/{{ $perbankan->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="bank">BANK</label>
                                                                                        <input type="text" name="bank" class="form-control @error('bank') is-invalid @enderror" placeholder="Insert BANK" value="{{ $perbankan->bank }}" required>
                                                                                        @error('bank')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="currency" class="form-label">Currency<span> *</span></label>
                                                                                        <select name="currency" id="currency"  type="text" class="custom-select @error('currency') is-invalid @enderror" placeholder="Select currency" required>
                                                                                            <option {{ $perbankan->currency?"":"Selected" }} value="">Select Currency</option>
                                                                                            <option {{ $perbankan->currency == "IDR"?"Selected":"" }} value="IDR">IDR (Rp)</option>
                                                                                            <option {{ $perbankan->currency == "USD"?"Selected":"" }} value="USD">USD ($)</option>
                                                                                            <option {{ $perbankan->currency == "CNY"?"Selected":"" }} value="CNY">CNY (¥)</option>
                                                                                            <option {{ $perbankan->currency == "TWD"?"Selected":"" }} value="TWD">TWD (NT$)</option>
                                                                                        </select>
                                                                                        @error('currency')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="account_name">Account Name</label>
                                                                                        <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" placeholder="Insert account_name" value="{{ $perbankan->account_name }}" required>
                                                                                        @error('account_name')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="account_number">Account Number</label>
                                                                                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" placeholder="Insert account number" value="{{ $perbankan->account_number }}" required>
                                                                                        @error('account_number')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="location">Location</label>
                                                                                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Insert BANK location" value="{{ $perbankan->location }}" required>
                                                                                        @error('location')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="address">Address</label>
                                                                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ $perbankan->address }}" required>
                                                                                        @error('address')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="telephone">Telephone</label>
                                                                                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" placeholder="Insert phone number" value="{{ $perbankan->telephone }}" required>
                                                                                        @error('telephone')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="swift_code">SWIFT Code</label>
                                                                                        <input type="text" name="swift_code" class="form-control @error('swift_code') is-invalid @enderror" placeholder="Insert Swift Code" value="{{ $perbankan->swift_code }}">
                                                                                        @error('swift_code')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="bank_code">BANK Code</label>
                                                                                        <input type="text" name="bank_code" class="form-control @error('bank_code') is-invalid @enderror" placeholder="Insert BANK Code" value="{{ $perbankan->bank_code }}">
                                                                                        @error('bank_code')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="edit-bank" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcanany
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
