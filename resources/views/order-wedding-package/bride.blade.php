 {{-- BRIDE --}}
 <div id="bride" class="col-md-12">
    @if ($bride->groom_pasport_id)
        <div class="page-subtitle">
    @else
        <div class="page-subtitle empty-value">
    @endif
        @lang("messages.Bride's")
        <span>
            <a href="#" data-toggle="modal" data-target="#edit-wedding-bride-{{ $bride->id }}"> 
                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
   
    <div class="card-ptext-margin">
        <div class="row">
            <div class="col-sm-6">
                <table class="table tb-list {{ $bride->groom_pasport_id?"":"incomplate-form-table"; }}">
                    <tr>
                        <td class="htd-1">
                            @lang("messages.Groom")
                        </td>
                        <td class="htd-2">
                            {{ $bride->groom }}
                            @if ($bride->groom_chinese)
                                ({{ $bride->groom_chinese }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Passport') / @lang('messages.ID')
                        </td>
                        <td class="htd-2">
                            @if ($bride->groom_pasport_id)
                                {{ $bride->groom_pasport_id }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                @if ($bride->bride_pasport_id)
                    <table class="table tb-list">
                @else
                    <table class="table tb-list incomplate-form-table">
                @endif
                    <tr>
                        <td class="htd-1">
                            @lang("messages.Bride")
                        </td>
                        <td class="htd-2">
                            {{ $bride->bride }}
                            @if ($bride->bride_chinese)
                                ({{ $bride->bride_chinese }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Passport') / @lang('messages.ID')
                        </td>
                        <td class="htd-2">
                            @if ($bride->bride_pasport_id)
                                {{ $bride->bride_pasport_id }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    {{-- MODAL UPDATE BRIDE --}}
    <div class="modal fade" id="edit-wedding-bride-{{ $bride->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>@lang('messages.Bride')</div>
                    </div>
                    <form id="updateWeddingOrderBride" action="/fupdate-wedding-order-bride/{{ $bride->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="groom">@lang("messages.Groom") <span> *</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy fi-torso"></i></span>
                                        <input name="groom" type="text" value="{{ $bride->groom }}" class="form-control input-icon @error('groom') is-invalid @enderror" placeholder="@lang('messages.Groom Name')" required>
                                    </div>
                                    @error('groom')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="groom_chinese">@lang("messages.Chinese Name")</label>
                                    <input name="groom_chinese" type="text" value="{{ $bride->groom_chinese }}" class="form-control @error('groom_chinese') is-invalid @enderror" placeholder="@lang('messages.Groom Chinese Name')">
                                    @error('groom_chinese')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="groom_pasport_id">@lang("messages.Passport") / @lang('messages.ID') <span> *</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy fa fa-id-card-o" aria-hidden="true"></i></span>
                                        <input name="groom_pasport_id" type="text" value="{{ $bride->groom_pasport_id }}" class="form-control input-icon @error('groom_pasport_id') is-invalid @enderror" placeholder="@lang('messages.Passport') / @lang('messages.ID')" required>
                                    </div>
                                    @error('groom_pasport_id')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bride">@lang("messages.Bride's") <span> *</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy fi-torso-female"></i></span>
                                        <input name="bride" type="text" value="{{ $bride->bride }}" class="form-control input-icon @error('bride') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" required>
                                    </div>
                                    @error('bride')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bride_chinese">@lang("messages.Chinese Name")</label>
                                    <input name="bride_chinese" type="text" value="{{ $bride->bride_chinese }}" class="form-control @error('bride_chinese') is-invalid @enderror" placeholder="@lang("messages.Bride's Chinese Name")">
                                    @error('bride_chinese')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bride_pasport_id">@lang("messages.Passport") / @lang('messages.ID') <span> *</span></label>
                                    <div class="btn-icon">
                                        <span><i class="icon-copy fa fa-id-card-o" aria-hidden="true"></i></span>
                                        <input name="bride_pasport_id" type="text" value="{{ $bride->bride_pasport_id }}" class="form-control input-icon @error('bride_pasport_id') is-invalid @enderror" placeholder="@lang('messages.Passport') / @lang('messages.ID')" required>
                                    </div>
                                    @error('bride_pasport_id')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                    </form>
                    <div class="card-box-footer">
                        <button type="submit" form="updateWeddingOrderBride" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>