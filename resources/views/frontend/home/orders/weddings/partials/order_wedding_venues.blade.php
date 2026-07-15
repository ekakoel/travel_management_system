{{-- WEDDING VENUE --}}
@if ($wedding_venues_id !== 'null' and $wedding_venues_id)
    @foreach ($wedding_venues_id as $wedding_venue_id)
        @php
            $weddingVenue = $wedding_venues->where('id',$wedding_venue_id)->first();
        @endphp
        @if ($weddingVenue)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-venue-{{ $wedding_venue_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $weddingVenue->cover) }}" alt="{{ $weddingVenue->service }}">
                    </a>
                    <input type="checkbox" name="wedding_venue_id[]" value="{{ $wedding_venue_id }}">
                </div>
                <div class="name-card">
                    <b>@lang('messages.'.$weddingVenue->type)</b>
                    <p>{{ $weddingVenue->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-venue-{{ $wedding_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i> {{ $weddingVenue->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/' . $weddingVenue->cover) }}" alt="{{ $weddingVenue->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingVenue->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingVenue->duration." ".$weddingVenue->time !!}</p>
                                    </div>
                                    @if ($weddingVenue->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingVenue->description !!}</p>
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