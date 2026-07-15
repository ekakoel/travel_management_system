{{-- WEDDING OTHER SERVICE --}}
@if ($wedding_others_id !== 'null' and $wedding_others_id)
    @foreach ($wedding_others_id as $wedding_other_id)
        @php
            $weddingOther = $wedding_others->where('id',$wedding_other_id)->first();
        @endphp
        @if ($weddingOther)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-entertainment-{{ $wedding_other_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingOther->cover) }}" alt="{{ $weddingOther->service }}">
                    </a>
                    <input type="checkbox" name="other_service_id[]" value="{{ $wedding_other_id }}">
                </div>
                <div class="name-card">
                    <b>@lang('messages.Other Services')</b>
                    <p>{{ $weddingOther->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-entertainment-{{ $wedding_other_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy ion-android-color-palette" aria-hidden="true"></i>{{ $weddingOther->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingOther->cover) }}" alt="{{ $weddingOther->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingOther->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingOther->duration." ".$weddingOther->time !!}</p>
                                    </div>
                                    @if ($weddingOther->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingOther->description !!}</p>
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