{{-- WEDDING DINNER VENUE --}}
@if ($wedding_dinner_venues_id !== 'null' and $wedding_dinner_venues_id)
    @foreach ($wedding_dinner_venues_id as $wedding_dinner_venue_id)
        @php
            $weddingDinnerVenue = $wedding_dinner_venues->where('id',$wedding_dinner_venue_id)->first();
        @endphp
        @if ($weddingDinnerVenue)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-dinner-venue-{{ $wedding_dinner_venue_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingDinnerVenue->cover) }}" alt="{{ $weddingDinnerVenue->service }}">
                    </a>
                    <input type="checkbox" name="dinner_venue_id[]" value="{{ $wedding_dinner_venue_id }}">
                </div>
                <div class="name-card">
                    <b>@lang('messages.Dinner Venue')</b>
                    <p>{{ $weddingDinnerVenue->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-dinner-venue-{{ $wedding_dinner_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-birthday-cake" aria-hidden="true"></i>{{ $weddingDinnerVenue->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingDinnerVenue->cover) }}" alt="{{ $weddingDinnerVenue->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingDinnerVenue->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingDinnerVenue->duration." ".$weddingDinnerVenue->time !!}</p>
                                    </div>
                                    @if ($weddingDinnerVenue->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingDinnerVenue->description !!}</p>
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