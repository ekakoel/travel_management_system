{{-- WEDDING VENUE --}}
<div id="weddingVenue" class="col-md-6">
    <div class="page-subtitle">
        @lang('messages.Wedding Venue')
        <span>
            <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $hotel->id }}"> 
                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
    {{-- MODAL DETAIL WEDDING VENUE --}}
    <div class="modal fade" id="detail-wedding-venue-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy dw dw-hotel"></i>@lang('messages.Wedding Venue')</div>
                    </div>
                    <div class="modal-img-container">
                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->type }}">
                        <div class="modal-service-name">
                            <b>{{ $hotel->name }}</b>
                            <p>{!! $hotel->address !!}</p>
                            <p>{{ date("m/d/Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                        </div>
                    </div>
                    
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-ptext-margin">
        <table class="table tb-list">
            <tr>
                <td class="htd-1">
                    @lang('messages.Hotel')
                </td>
                <td class="htd-2">
                    {{ $hotel->name }}
                </td>
            </tr>
            <tr>
                <td class="htd-1">
                    @lang('messages.Address')
                </td>
                <td class="htd-2">
                    {{ $hotel->address }}
                </td>
            </tr>
        </table>
    </div>
</div>