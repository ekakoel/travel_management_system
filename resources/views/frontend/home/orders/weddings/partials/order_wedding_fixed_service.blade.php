{{-- WEDDING FIXED SERVICE --}}
@if ($wedding_fixed_services_id !== 'null' and $wedding_fixed_services_id)
    <div class="card-box-content">
        @foreach ($wedding_fixed_services_id as $wedding_fixed_service_id)
            @php
                $weddingFixedService = $wedding_fixed_services->where('id',$wedding_fixed_service_id)->first();
            @endphp
            @if ($weddingFixedService)
                <div class="card">
                    <a href="#" data-toggle="modal" data-target="#wedding-fixed_service-{{ $wedding_fixed_service_id }}">
                        <div class="image-container">
                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/'. $weddingFixedService->cover) }}" alt="{{ $weddingFixedService->service }}">
                        </div>
                    </a>
                    <div class="name-card">
                        <p>{{ $weddingFixedService->service }}</p>
                    </div>
                </div>
                {{-- MODAL WEDDING FIXED SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                <div class="modal fade" id="wedding-fixed_service-{{ $wedding_fixed_service_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-left">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fi-trees" aria-hidden="true"></i>{{ $weddingFixedService->type }}</div>
                                </div>
                                <div class="card-banner m-b-8">
                                    <img class="rounded" src="{{ asset('storage/vendors/package/'. $weddingFixedService->cover) }}" alt="{{ $weddingFixedService->service }}" loading="lazy">
                                </div>
                                <div class="card-text">
                                    <div class="row ">
                                        <div class="col-sm-4">
                                            <b>Service: </b><p>{!! $weddingFixedService->service !!}</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <b>Duration: </b><p>{!! $weddingFixedService->duration." ".$weddingFixedService->time !!}</p>
                                        </div>
                                        @if ($weddingFixedService->description)
                                            <div class="col-sm-12">
                                                <b>Description: </b><p>{!! $weddingFixedService->description !!}</p>
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
    </div>
@endif