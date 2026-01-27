{{-- BRIDE --}}
<div class="col-md-6">
    <div class="page-subtitle">
        @lang('messages.Bride')
        @if ($wedding_planner->status == "Draft")
            <span>
                <a href="#" data-toggle="modal" data-target="#update-bride-{{ $bride->id }}"> 
                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Bride')" aria-hidden="true"></i>
                </a>
            </span>
        @endif
    </div>
    {{-- MODAL UPDATE BRIDE  --}}
    <div class="modal fade" id="update-bride-{{ $bride->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Bride')</div>
                    </div>
                    <form id="updateBride" action="/fupdate-wedding-planner-bride/{{ $bride->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="groom_name">@lang("messages.Groom's Name")</label>
                                    <input type="text" name="groom_name" class="form-control @error('groom_name') is-invalid @enderror"  placeholder="@lang("messages.Insert Groom's Name")" value="{{ $bride->groom }}" required>
                                    @error('groom_name')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="groom_chinese">@lang("messages.Groom's Chinese Name")</label>
                                    <input type="text" name="groom_chinese" class="form-control @error('groom_chinese') is-invalid @enderror"  placeholder="@lang("messages.Chinese Name")" value="{{ $bride->groom_chinese }}">
                                    @error('groom_chinese')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="groom_contact">@lang("messages.Groom's Contact")</label>
                                    <input type="text" name="groom_contact" maxlength="19" class="form-control phone @error('groom_contact') is-invalid @enderror"  placeholder="@lang("messages.Contact")" value="{{ $bride->groom_contact }}">
                                    @error('groom_contact')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="bride_name">@lang("messages.Bride's Name")</label>
                                    <input type="text" name="bride_name" class="form-control @error('bride_name') is-invalid @enderror"  placeholder="@lang("messages.Insert Bride's Name")" value="{{ $bride->bride }}" required>
                                    @error('bride_name')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="bride_chinese">@lang("messages.Bride's Chinese Name")</label>
                                    <input type="text" name="bride_chinese" class="form-control @error('bride_chinese') is-invalid @enderror"  placeholder="@lang("messages.Chinese Name")" value="{{ $bride->bride_chinese }}">
                                    @error('bride_chinese')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="bride_contact">@lang("messages.Bride's Contact")</label>
                                    <input type="text" name="bride_contact" maxlength="19" class="form-control phone @error('bride_contact') is-invalid @enderror" placeholder="@lang("messages.Contact")" value="{{ $bride->bride_contact }}">
                                    @error('bride_contact')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-box-footer">
                        <button type="submit" form="updateBride" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <table class="table tb-list" >
        <tr>
            <td class="htd-1">
                @lang("messages.Groom's")
            </td>
            <td class="htd-2">
                {{ "Mr. ".$bride->groom }}
                @if ($bride->groom_chinese)
                    {{ "(".$bride->groom_chinese.")" }}
                @endif
            </td>
        </tr>

        <tr>
            <td class="htd-1">
                @lang('messages.Contact')
            </td>
            <td class="htd-2">
                @if ($bride->groom_contact)
                    {{ $bride->groom_contact }}
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td class="htd-1">
                @lang("messages.Bride's")
            </td>
            <td class="htd-2">
                {{ "Ms. ".$bride->bride }}
                @if ($bride->bride_chinese)
                    {{ "(".$bride->bride_chinese.")" }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="htd-1">
                @lang('messages.Contact')
            </td>
            <td class="htd-2">
                @if ($bride->bride_contact)
                    {{ $bride->bride_contact }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>
</div>