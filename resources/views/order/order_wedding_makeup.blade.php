{{-- WEDDING MAKEUP --}}
@if ($wedding_makeups_id !== 'null' and $wedding_makeups_id)
    @foreach ($wedding_makeups_id as $wedding_makeup_id)
        @php
            $weddingMakeup = $wedding_makeups->where('id',$wedding_makeup_id)->first();
        @endphp
        @if ($weddingMakeup)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-makeup-{{ $wedding_makeup_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingMakeup->cover) }}" alt="{{ $weddingMakeup->service }}">
                    </a>
                    <input type="checkbox" name="makeup_id[]" value="{{ $wedding_makeup_id }}">
                </div>
                <div class="name-card">
                    <b>{{ $weddingMakeup->type }}</b>
                    <p>{{ $weddingMakeup->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-makeup-{{ $wedding_makeup_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-paintbrush" aria-hidden="true"></i>{{ $weddingMakeup->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingMakeup->cover) }}" alt="{{ $weddingMakeup->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingMakeup->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingMakeup->duration." ".$weddingMakeup->time !!}</p>
                                    </div>
                                    @if ($weddingMakeup->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingMakeup->description !!}</p>
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