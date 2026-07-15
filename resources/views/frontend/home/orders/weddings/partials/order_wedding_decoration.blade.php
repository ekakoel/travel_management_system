{{-- WEDDING DECORATION --}}
@if ($wedding_decorations_id !== 'null' and $wedding_decorations_id)
    @foreach ($wedding_decorations_id as $wedding_decoration_id)
        @php
            $weddingDecoration = $wedding_decorations->where('id',$wedding_decoration_id)->first();
        @endphp
        @if ($weddingDecoration)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-decoration-{{ $wedding_decoration_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingDecoration->cover) }}" alt="{{ $weddingDecoration->service }}">
                    </a>
                    <input type="checkbox" name="decorations_id[]" value="{{ $wedding_decoration_id }}">
                </div>
                <div class="name-card">
                    <b>@lang('messages.'.$weddingDecoration->type)</b>
                    <p>{{ $weddingDecoration->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-decoration-{{ $wedding_decoration_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fi-trees" aria-hidden="true"></i>{{ $weddingDecoration->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingDecoration->cover) }}" alt="{{ $weddingDecoration->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingDecoration->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingDecoration->duration." ".$weddingDecoration->time !!}</p>
                                    </div>
                                    @if ($weddingDecoration->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingDecoration->description !!}</p>
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