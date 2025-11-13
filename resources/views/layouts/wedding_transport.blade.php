
@php
    $transport_id = json_decode($weddings->transport_id);
@endphp
    <div class="tab-inner-title">
        Transport
        @if ($weddings->status !== "Active")
            <span>
                <a href="#" data-toggle="modal" data-target="#add-wedding-transports-active">
                    @if ($weddings->transport_id != 'null')
                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                    @else
                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                    @endif
                </a>
            </span>
        @endif
    </div>
    {{-- WEDDING TRANSPORTATION --------------------------------------------------------------------------------------------------------------------------------}}
    @if (count($transports) > 0)
        @if ($transport_id != "null" and $transport_id )
            <div class="card-box-content m-b-8">
                @foreach ($transport_id as $transportid)
                    @php
                        $transports_active = $transports->where('id',$transportid)->first();
                        if ($transports_active) {
                            $t_price = $transport_prices->where('transports_id',$transports_active->id)->where('duration',$hotel->airport_duration)->first();
                            if ($t_price) {
                                $cr_transport = ceil($t_price->contract_rate/$usdrates->rate); 
                                $cr_markup = $cr_transport + $t_price->markup;
                                $cr_tax =  ceil($cr_markup * ($taxes->tax / 100));
                                $transport_price = ($cr_tax + $cr_markup);
                            }else{
                                $transport_price = 0;
                            }
                        }else{
                            $transport_price = 0;
                        }
                    @endphp
                    @if ($transport_price > 0)
                        
                        <div class="card">
                            <a href="#" data-toggle="modal" data-target="#detail-transports_active-{{ $transportid }}">
                                <div class="card-image-container">
                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/transports/transports-cover/' . $transports_active->cover) }}" alt="{{ $transports_active->name }}">
                                    <div class="name-card">
                                        <b>{{ $transports_active->name }}</b>
                                    </div>
                                </div>
                                <div class="price-card-usd m-t-8">
                                    {{ currencyFormatUsd($transport_price) }}
                                </div>
                                <div class="label-capacity">
                                    {{ $transports_active->capacity." seats" }}
                                </div>
                            </a>
                        </div>
                        {{-- MODAL TRANSPORT DETAIL --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="detail-transports_active-{{ $transportid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-car"></i>{{ $transports_active->brand." - ". $transports_active->name }}</div>
                                        </div>
                                        <div class="card-banner m-b-8">
                                            <img class="rounded" src="{{ asset('storage/transports/transports-cover/' . $transports_active->cover) }}" alt="{{ $transports_active->cover }}" loading="lazy">
                                        </div>
                                        @if ($transports_active->name)
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-sm-4">
                                                        <b>Transport: </b><p>{!! $transports_active->brand." ".$transports_active->name !!}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <b>Capacity: </b><p>{{ $transports_active->capacity." guests" }}</p>
                                                    </div>
                                                    
                                                    
                                                    <div class="col-sm-12">
                                                        <b>Description: </b><p>{!! $transports_active->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                    <div class="modal-label-price">
                                        {{ currencyFormatUsd($transport_price) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="card-box-content-empty">
                <p>Transport service have not been added to the wedding package yet.</p>
            </div>
        @endif
        @if ($weddings->status !== "Active")
            <div class="card-grid-footer">
                {{-- MODAL ADD WEDDING SUITE AND VILLAS --------------------------------------------------------------------------------------------------------------- --}}
                <div class="modal fade" id="add-wedding-transports-active" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-car"></i>Transports</div>
                                </div>
                                @if ($transports)
                                    <form id="addTransport" action="/fadd-wedding-transports/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                                        @method('put')
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    @foreach ($transports as $transport)
                                                        @php
                                                            $tprice = $transport_prices->where('transports_id',$transport->id)->first();
                                                            if ($tprice) {
                                                                $cr_transport = ceil($tprice->contract_rate/$usdrates->rate); 
                                                                $cr_markup = $cr_transport + $tprice->markup;
                                                                $cr_tax =  ceil($cr_markup * ($taxes->tax / 100));
                                                                $transport_price = ($cr_tax + $cr_markup);
                                                            }else{
                                                                $transport_price = 0;
                                                            }
                                                        @endphp
                                                        @if ($tprice )
                                                            <div class="col-md-4 m-b-8">
                                                                <div class="card">
                                                                    <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}">
                                                                    <input type="checkbox" name="transport_id[]" value="{{ $transport->id }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $transport->name }}</b>
                                                                        <p>{{ $transport->capacity." seats" }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        @endif
                                                        
                                                    @endforeach
                                                </div>
                                                @error('transport_id[]')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addTransport" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row">
            <div class="col-12">
                <p>Sorry, Transport are not available at the moment. Please add transports to the vendor page before proceeding.<br> Use the following link to go to Vendors Page.</p>
                <a href="/vendors-admin" style="color: blue">Vendors Admin</a>
            </div>
        </div>
    @endif
