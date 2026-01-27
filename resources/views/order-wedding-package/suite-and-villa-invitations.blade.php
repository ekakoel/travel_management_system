{{-- SUITE OR VILLA INVITATIONS --}}
<div id="suiteAndVillaInvitations" class="col-md-6">
    @php
        $suiteVillaInvitations = $orderWedding->suite_villa_invitations;
    @endphp
    <div class="page-subtitle">
        @lang('messages.Suite')/@lang('messages.Villa') (@lang("messages.Invitations"))
        <span>
            <a href="#" data-toggle="modal" data-target="#detail-invitations-suite-villa-{{ $orderWedding->id }}"> 
                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
            </a>
        </span>
        <span>
            <a href="#" data-toggle="modal" data-target="#detail-invitations-suite-villa-{{ $orderWedding->id }}"> 
                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
            </a>
        </span>
    </div>
    {{-- MODAL DETAIL SUITE OR VILLA INVITATIONS --}}
    <div class="modal fade" id="detail-invitations-suite-villa-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-hotel"></i>@lang('messages.Suite') / @lang('messages.Villa') (@lang("messages.Invitations"))</div>
                    </div>
                    <div class="card-box-list">
                        <table class="table modal-tb-list">
                            <thead>

                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Date</th>
                                    <th>Suite / Villa</th>
                                    <th>Invitations</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                        @foreach ($suiteAndVillaInvitations as $s_n_v_no=>$suite_and_villa_invitation)
                            @php
                                $suite_and_villa = $suite_and_villa_invitation->room;
                                $anv_cin =Carbon::parse($suite_and_villa_invitation->checkin);
                                $anv_cut = Carbon::parse($suite_and_villa_invitation->checkout);
                                $suite_and_villa_duration = $anv_cin->diffInDays($anv_cut);
                            @endphp
                            <tr>
                                <td class="text-center">{{ ++$s_n_v_no }}</td>
                                <td>{{ date('m/d',strtotime($anv_cin)) }} - {{ date('m/d',strtotime($anv_cut)) }}</td>
                                <td>
                                    {{ $suite_and_villa->rooms }}
                                    @if ($suite_and_villa_invitation->extra_bed_id)
                                        {{ "+ ".$suite_and_villa_invitation->extra_bed->type." Extra bed " }}
                                    @endif 
                                </td>
                                <td>{!! $suite_and_villa_invitation->guest_detail !!}</td>
                                <td>{{ $suite_and_villa_duration." nights" }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                    </div>
                    
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table tb-list">
        @php
            $lastRoomId = 0;
            $number_of_invitations_room = 0;
        @endphp
        @foreach ($suiteVillaInvitations as $suite_villa_invitation)
            @php
                $suiteVillaId = $suite_villa_invitation->room->id;
                $c_s_v = count($suiteVillaInvitations->where('rooms_id',$suiteVillaId));
                $c_r_extra_bed = count($suiteVillaInvitations->where('rooms_id',$suiteVillaId)->where('extra_bed_id','!=',null));
                $roomNumberOfInvitations = $suiteVillaInvitations->where('rooms_id',$suiteVillaId);
                if ($lastRoomId == $suiteVillaId) {
                    $lastRoomId = $lastRoomId;
                    $number_of_invitations_room = 0;
                }else{
                    $lastRoomId = 0;
                    foreach ($roomNumberOfInvitations as $test) {
                        $number_of_invitations_room += $test->number_of_guests;
                    }
                }
            @endphp
            @if ($lastRoomId == 0)
                <tr>
                    <td class="htd-1">
                        {{ $suite_villa_invitation->room->rooms }}
                    </td>
                    <td class="htd-2">
                        {{ $c_s_v }} Unit
                        @if ($c_r_extra_bed > 0)
                            + {{ $c_r_extra_bed }} Extra bed
                        @endif
                        | {{ $number_of_invitations_room }} @lang('messages.Invitations')
                    </td>
                </tr>
                @php
                    $lastRoomId = $suiteVillaId;
                @endphp
            @endif
        @endforeach
    </table>
</div>