{{-- WEDDING --}}
<div class="col-md-6">
    <div class="page-subtitle">
        @lang("messages.Wedding")
        @if ($wedding_planner->status == "Draft")
            <span>
                <a href="#" data-toggle="modal" data-target="#update-wedding-detail-{{ $wedding_planner->id }}"> 
                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update wedding detail')" aria-hidden="true"></i>
                </a>
            </span>
            
        @endif
    </div>
    <table class="table tb-list" >
        <tr>
            <td class="htd-1">
                @lang('messages.Check-in')
            </td>
            @if (date('Y-m-d',strtotime($wedding_planner->checkin)) >= date('Y-m-d',strtotime($wedding_planner->checkout)))
                <td class="htd-2 bg-error">
                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkin)) }}
                </td>
            @else
                <td class="htd-2">
                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkin)) }}
                </td>
            @endif
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Wedding Date')
            </td>
            @if ($wedding_planner->slot)
                @if (date('Y-m-d',strtotime($wedding_planner->checkin)) < date('Y-m-d',strtotime($wedding_planner->wedding_date)) and date('Y-m-d',strtotime($wedding_planner->checkout)) > date('Y-m-d',strtotime($wedding_planner->wedding_date)))
                    <td class="htd-2">
                        {{ date('Y-m-d',strtotime($wedding_planner->wedding_date))." ".date('(H.i)',strtotime($wedding_planner->slot)) }}
                    </td>
                @else
                    <td class="htd-2 bg-error">
                        {{ date('Y-m-d',strtotime($wedding_planner->wedding_date))." ".date('(H.i)',strtotime($wedding_planner->slot)) }}
                    </td>
                @endif
            @else
                <td class="htd-2">
                    {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) }}
                </td>
            @endif
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Check-out')
            </td>
            @if (date('Y-m-d',strtotime($wedding_planner->checkout)) <= date('Y-m-d',strtotime($wedding_planner->checkin)))
                <td class="htd-2 bg-error">
                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkout)) }}
                </td>
            @else
                <td class="htd-2">
                    {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkout)) }}
                </td>
            @endif
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Duration')
            </td>
            @if (date('Y-m-d',strtotime($wedding_planner->checkout)) <= date('Y-m-d',strtotime($wedding_planner->checkin)))
                <td class="htd-2 bg-error">
                    @lang('messages.Invalid')
                </td>
            @else
                <td class="htd-2">
                    {{ $wedding_planner->duration }} @lang('messages.nights')
                </td>
            @endif
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Number of Invitations')
            </td>
            <td class="htd-2">
                {{ $wedding_planner->number_of_invitations }} @lang('messages.Invitations')
            </td>
        </tr>
    </table>
</div>

@if ($wedding_planner->status == "Draft")
    {{-- MODAL UPDATE WEDDING DETAIL --}}
    <div class="modal fade" id="update-wedding-detail-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Wedding Detail')</div>
                    </div>
                    <form id="updateWeddingDetail" action="/fupdate-wedding-planner-wedding/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="checkin">@lang("messages.Check-in")</label>
                                    <input type="text" readonly name="checkin" class="form-control date-picker @error('checkin') is-invalid @enderror" value="{{ date('Y-m-d',strtotime($wedding_planner->checkin)) }}" placeholder="@lang("messages.Select date")"  required>
                                    @error('checkin')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="checkout">@lang("messages.Check-out")</label>
                                    <input type="text" readonly name="checkout" class="form-control date-picker @error('checkout') is-invalid @enderror" value="{{ date('Y-m-d',strtotime($wedding_planner->checkout)) }}" placeholder="@lang("messages.Select date")"  required>
                                    @error('checkout')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="wedding_date">@lang("messages.Wedding Date")</label>
                                    <input type="text" readonly name="wedding_date" class="form-control date-picker @error('wedding_date') is-invalid @enderror" value="{{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) }}" placeholder="@lang("messages.Select date")"  required>
                                    @error('wedding_date')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="number_of_invitations">@lang("messages.Number of invitations")</label>
                                    <input type="number" name="number_of_invitations" class="form-control @error('number_of_invitations') is-invalid @enderror"  placeholder="@lang("messages.Number of invitations")" value="{{ $wedding_planner->number_of_invitations }}">
                                    @error('number_of_invitations')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    </form>
                    <div class="card-box-footer">
                        <button type="submit" form="updateWeddingDetail" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif