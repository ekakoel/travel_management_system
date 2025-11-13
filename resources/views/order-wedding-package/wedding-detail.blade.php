{{-- WEDDING AND RECEPTION --}}
<div id="wedding" class="col-md-6">
    <div id="subtitle-wedding-date" class="page-subtitle">
        @if ($orderWedding->service == "Ceremony Venue")
            @if ($orderWedding->reception_venue_id)
                @lang('messages.Wedding') & @lang('messages.Reception')
            @else
                @lang('messages.Wedding')
            @endif
        @elseif ($orderWedding->service == "Reception Venue")
            @if ($orderWedding->ceremony_venue_id)
                @lang('messages.Wedding') & @lang('messages.Reception')
            @else
                @lang('messages.Reception')
            @endif
        @elseif ($orderWedding->service == "Wedding Package")
            @lang('messages.Wedding') & @lang('messages.Reception')
        @endif
        <span>
            <a href="#" data-toggle="modal" data-target="#update-wedding-order-wedding-{{ $orderWedding->id }}">
                <i class="fa fa-pencil"></i>
            </a>
        </span>
    </div>
    <div class="card-ptext-margin">
        <table class="table tb-list">
            @if ($orderWedding->ceremony_venue_id)
                <tr>
                    <td class="htd-1">
                        @lang('messages.Wedding Date')
                    </td>
                    <td class="htd-2">
                        {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})
                    </td>
                </tr>
            @endif
            @if ($orderWedding->reception_venue_id)
                <tr>
                    <td class="htd-1">
                        @lang('messages.Reception Date')
                    </td>
                    <td class="htd-2">
                        {{ date("m/d/y (H.i)",strtotime($orderWedding->reception_date_start)) }}
                    </td>
                </tr>
            @endif
        </table>
        {{-- MODAL UPDATE WEDDING AND RECEPTION--}}
        <div class="modal fade" id="update-wedding-order-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="title">
                                <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                @if ($orderWedding->service == "Ceremony Venue")
                                    @if ($orderWedding->reception_venue_id)
                                        @lang('messages.Wedding') & @lang('messages.Reception')
                                    @else
                                        @lang('messages.Wedding')
                                    @endif
                                @elseif ($orderWedding->service == "Reception Venue")
                                    @if ($orderWedding->ceremony_venue_id)
                                        @lang('messages.Wedding') & @lang('messages.Reception')
                                    @else
                                        @lang('messages.Reception')
                                    @endif
                                @elseif ($orderWedding->service == "Wedding Package")
                                    @lang('messages.Wedding') & @lang('messages.Reception')
                                @endif
                            </div>
                        </div>
                        <form id="updateWeddingOrderWedding" action="/fupdate-wedding-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                @if ($orderWedding->service == "Wedding Package")
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="wedding_date">@lang("messages.Wedding Date")<span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                <input readonly id="wedding-date" name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" value="{{ dateFormat($orderWedding->wedding_date) }}" required>
                                            </div>
                                            @error('wedding_date')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="slot">@lang('messages.Slot') <span>*</span></label>
                                            <select name="slot" class="custom-select col-12 @error('slot') is-invalid @enderror" required>
                                                <option value="">@lang('messages.Select one')</option>
                                                @foreach ($slots as $slot)
                                                    <option value="{{ $slot }}" {{ $orderWedding->slot == $slot?'selected':'' }}>{{ date('h:i A',strtotime($slot)) }}</option>
                                                @endforeach
                                            </select>
                                            @error('slot')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @else
                                    @if ($orderWedding->ceremony_venue_id)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="wedding_date">@lang("messages.Wedding Date")<span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <input readonly id="wedding-date" name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" value="{{ dateFormat($orderWedding->wedding_date) }}" required>
                                                </div>
                                                @error('wedding_date')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="slot">@lang('messages.Slot') <span>*</span></label>
                                                <select name="slot" class="custom-select col-12 @error('slot') is-invalid @enderror" required>
                                                    <option value="">@lang('messages.Select one')</option>
                                                    @foreach ($slots as $slot)
                                                        <option value="{{ $slot }}" {{ $orderWedding->slot == $slot?'selected':'' }}>{{ date('h:i A',strtotime($slot)) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('slot')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    @if ($orderWedding->reception_venue_id)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reception_date_start">@lang("messages.Reception Date") <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                    <input readonly name="reception_date_start" type="text" id="reception_date_start" class="form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" value="{{ dateFormat($orderWedding->reception_date_start) }}" required>
                                                </div>
                                                @error('reception_date_start')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reception_venue_slot">@lang('messages.Slot')</label>
                                                <input name="reception_venue_slot" type="time" class="form-control @error('reception_venue_slot') is-invalid @enderror" value="{{ date('H:i',strtotime($orderWedding->reception_date_start)) }}" required>
                                                @error('reception_venue_slot')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="updateWeddingOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>