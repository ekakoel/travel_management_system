{{-- WEDDING MAKEUP --}}
@if ($wedding_transports_id !== 'null' and $wedding_transports_id)
    @foreach ($wedding_transports_id as $wedding_transport_id)
        @php
            $weddingTransport = $wedding_transports->where('id',$wedding_transport_id)->first();
        @endphp
        @if ($weddingTransport)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-transport-{{ $wedding_transport_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/transports/transports-cover/'. $weddingTransport->cover) }}" alt="{{ $weddingTransport->brand }}">
                    </a>
                    <input type="checkbox" name="transport_id[]" value="{{ $wedding_transport_id }}">
                </div>
                <div class="name-card">
                    <b>{{ $weddingTransport->brand }}</b>
                    <p>{{ $weddingTransport->name }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-transport-{{ $wedding_transport_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-car" aria-hidden="true"></i>@lang('messages.Transport')</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/transports/transports-cover/'. $weddingTransport->cover) }}" alt="{{ $weddingTransport->name }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>@lang('messages.Service'): </b><p>@lang('messages.Transport')</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>@lang('messages.Capacity'): </b><p>{!! $weddingTransport->capacity." seats" !!}</p>
                                    </div>
                                    @if ($weddingTransport->description)
                                        <div class="col-sm-12">
                                            <b>@lang('messages.Description'): </b><p>{!! $weddingTransport->description !!}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
