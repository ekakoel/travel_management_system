{{-- WEDDING MAKEUP --}}
@if ($wedding_documentations_id !== 'null' and $wedding_documentations_id)
    @foreach ($wedding_documentations_id as $wedding_documentation_id)
        @php
            $weddingDocumentation = $wedding_documentations->where('id',$wedding_documentation_id)->first();
        @endphp
        @if ($weddingDocumentation)
            <div class="card">
                <div class="image-container">
                    <a href="#" data-toggle="modal" data-target="#wedding-documentation-{{ $wedding_documentation_id }}">
                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingDocumentation->cover) }}" alt="{{ $weddingDocumentation->service }}">
                    </a>
                    <input type="checkbox" name="documentations_id[]" value="{{ $wedding_documentation_id }}">
                </div>
                <div class="name-card">
                    <b>{{ $weddingDocumentation->type }}</b>
                    <p>{{ $weddingDocumentation->service }}</p>
                </div>
            </div>
            {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="wedding-documentation-{{ $wedding_documentation_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fi-camera" aria-hidden="true"></i>{{ $weddingDocumentation->type }}</div>
                            </div>
                            <div class="card-banner m-b-8">
                                <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingDocumentation->cover) }}" alt="{{ $weddingDocumentation->service }}" loading="lazy">
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <b>Service: </b><p>{!! $weddingDocumentation->service !!}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <b>Duration: </b><p>{!! $weddingDocumentation->duration." ".$weddingDocumentation->time !!}</p>
                                    </div>
                                    @if ($weddingDocumentation->description)
                                        <div class="col-sm-12">
                                            <b>Description: </b><p>{!! $weddingDocumentation->description !!}</p>
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