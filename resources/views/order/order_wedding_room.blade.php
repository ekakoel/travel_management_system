{{-- WEDDING ROOM --}}
@if ($hotelrooms_id !== 'null' and $hotelrooms_id)
    @foreach ($hotelrooms_id as $hotelroom_id)
        @php
            $weddingRoom = $hotelrooms->where('id',$hotelroom_id)->first();
        @endphp
        @if ($weddingRoom)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-room-{{ $hotelroom_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/hotels/hotels-room/' . $weddingRoom->cover) }}" alt="{{ $weddingRoom->service }}">
                    </a>
                    <input type="checkbox" name="suite_and_villas_id[]" value="{{ $hotelroom_id }}">
                </div>
                <div class="name-card">
                    <b>Suite and Villa</b>
                    <p>{{ $weddingRoom->rooms }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING ROOM --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-room-{{ $hotelroom_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i>{{ $wedding->hotels->name }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/hotels/hotels-room/' . $weddingRoom->cover) }}" alt="{{ $weddingRoom->rooms }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Room: </b><p>{!! $weddingRoom->rooms !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $wedding->duration !!}</p>
                                    </div>
                                    @if ($weddingRoom->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingRoom->description !!}</p>
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