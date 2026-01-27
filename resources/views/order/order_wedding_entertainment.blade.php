{{-- WEDDING MAKEUP --}}
@if ($wedding_entertainments_id !== 'null' and $wedding_entertainments_id)
    @foreach ($wedding_entertainments_id as $wedding_entertainment_id)
        @php
            $weddingEntertainment = $wedding_entertainments->where('id',$wedding_entertainment_id)->first();
        @endphp
        @if ($weddingEntertainment)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-entertainment-{{ $wedding_entertainment_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingEntertainment->cover) }}" alt="{{ $weddingEntertainment->service }}">
                    </a>
                    <input type="checkbox" name="entertainments_id[]" value="{{ $wedding_entertainment_id }}">
                </div>
                <div class="name-card">
                    <b>{{ $weddingEntertainment->type }}</b>
                    <p>{{ $weddingEntertainment->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-entertainment-{{ $wedding_entertainment_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-android-color-palette" aria-hidden="true"></i>{{ $weddingEntertainment->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingEntertainment->cover) }}" alt="{{ $weddingEntertainment->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingEntertainment->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingEntertainment->duration." ".$weddingEntertainment->time !!}</p>
                                    </div>
                                    @if ($weddingEntertainment->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingEntertainment->description !!}</p>
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