{{-- SUITE OR VILLA BRIDE --}}
<div id="suiteAndVillaBrides" class="col-md-6">
    <div class="page-subtitle">
        @lang('messages.Suite')/@lang('messages.Villa') (@lang("messages.Bride's"))
        <span>
            <a href="#" data-toggle="modal" data-target="#detail-bride-suite-villa-{{ $orderWedding->id }}"> 
                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
    {{-- MODAL DETAIL SUITE OR VILLA BRIDE --}}
    <div class="modal fade" id="detail-bride-suite-villa-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy dw dw-hotel"></i>@lang('messages.Suite') / @lang('messages.Villa') (@lang("messages.Bride's"))</div>
                    </div>
                    <div class="modal-img-container">
                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-room/' . $orderWedding->suite_villa->cover) }}" alt="{{ $hotel->type }}">
                        <div class="modal-service-name">
                            <b>{{ $hotel->name }}</b>
                            <p>{{ $orderWedding->suite_villa->rooms }}</p>
                            <p>{{ dateFormat($orderWedding->checkin) }} - {{ dateFormat($orderWedding->checkout) }}</p>
                        </div>
                    </div>
                    
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table tb-list">
        <tr>
            <td class="htd-1">
                @lang('messages.Suite') / @lang('messages.Villa')
            </td>
            <td class="htd-2">
                {{ $orderWedding->suite_villa->rooms }}
            </td>
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Duration')
            </td>
            <td class="htd-2">
                {{ $orderWedding->duration." nights" }}
            </td>
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Check-in')
            </td>
            <td class="htd-2">
                {{ dateFormat($orderWedding->checkin) }}
            </td>
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Check-out')
            </td>
            <td class="htd-2">
                {{ dateFormat($orderWedding->checkout) }}
            </td>
        </tr>
    </table>
</div>