<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Wedding Order {{ $orderWedding->orderno }}</title>
    <style>
        .text{
            font-size: 0.8rem;
        }
        .email-container{
            width: 100vh;
            padding: 18px;
        }
        .email-title{
            text-transform: uppercase;
            font-weight: 700;
            padding-bottom: 4px;
        }
        .tb-title{
            font-size: 0.9rem;
            font-weight: 600;
            padding-left: 8px;
        }
        .email-table{
            border: none !important;
            width: 100%;
            padding-left: 26px;
        }
        .email-table td{
            font-size: 0.9rem !important;
        }
        .email-table td, .email-table th{
            padding: 0 !important;
            border: none !important;
        }
        .email-table .ftd-1{
            position: relative;
            width: 25%;
            vertical-align: baseline;
            padding-left: 26px;
        }
        .email-table .ftd-1::after{
            content: ": ";
            position: absolute;
            right: 0;
            padding-right: 4px;
        }
        .email-table .ftd-2{
            width: 75%;
            vertical-align: baseline;
        }
        .email-paragraft{
            padding: 0 26px;
        }
    </style>
</head>
<body>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <h1 style="padding: 0 !important; margin: 0 !important;">{{ 'New Wedding Order '.$orderWedding->orderno }}</h1>
    {{ 'Sales / Agent : '.$agent->name." (".$agent->office.")" }} - {{ dateFormat($orderWedding->created_at) }}<br>
    @if ($orderWedding->request_quotation == "Yes")
        <b style="padding: 0 !important; margin: 0 !important; font-size:1.2rem; color:red;">Book a room of more than 8 units</b><br>
    @endif
    ----------------------------------------------------------------------------------------------------------------------------<br>
    
    <div class="email-title">
        <b>Bride's</b><br>
    </div>
    <table class="table email-table">
        <tr>
            <td class="ftd-1">
                Groom
            </td>
            <td class="ftd-2">
                Mr. {{ $brides->groom }} 
                @if ($brides->groom_chinese)
                    {{ $brides->groom_chinese }}
                @endif
                @if ($brides->groom_pasport_id)
                    (ID: {{ $brides->groom_pasport_id }})
                @endif
            </td>
        </tr>
        <tr>
            <td class="ftd-1">
                Bride
            </td>
            <td class="ftd-2">
                Ms. {{ $brides->bride }} 
                @if ($brides->bride_chinese)
                    {{ $brides->bride_chinese }}
                @endif
                @if ($brides->bride_pasport_id)
                    (ID: {{ $brides->bride_pasport_id }})
                @endif
            </td>
        </tr>
    </table>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <div class="email-title">
        <b>Wedding details</b><br>
    </div>
    <table class="table email-table m-b-18">
        <tr>
            <td class="ftd-1">
                Wedding Venue
            </td>
            <td class="ftd-2">
                {{ $vendor->name }}
            </td>
        </tr>
        <tr>
            <td class="ftd-1">
                Wedding Date
            </td>
            <td class="ftd-2">
                {{ dateFormat($orderWedding->wedding_date) }}
            </td>
        </tr>
        <tr>
            <td class="ftd-1">
                Number of Invitations
            </td>
            <td class="ftd-2">
                {{ $orderWedding->number_of_invitation }} Invitations
            </td>
        </tr>
    </table>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    @php
        $no = 0;
    @endphp
    <div class="email-title">
        Order details
    </div>
    {{-- CEREMONY VENUE --}}
    @if ($orderWedding->ceremony_venue_id)
        <table class="table email-table m-b-18">
        <div class="tb-title">
            {{ ++$no.". " }}Ceremony Venue
        </div>
            <tr>
                <td class="ftd-1">
                    Service
                </td>
                <td class="ftd-2">
                    {{ $orderWedding->ceremony_venue->name }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1">
                    Date
                </td>
                <td class="ftd-2">
                    {{ dateFormat($orderWedding->wedding_date) }} {{ date('(H.i)',strtotime($orderWedding->slot)) }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1">
                    Decoration
                </td>
                <td class="ftd-2">
                    @if ($cv_decoration)
                        {{ $cv_decoration->service }}
                    @else
                        Basic Decoration
                    @endif
                </td>
            </tr>
        </table>
    @endif
    {{-- RECEPTION VENUE --}}
    @if ($receptionVenue)
        <table class="table email-table m-b-18">
        <div class="tb-title">
            {{ ++$no.". " }}Reception Venue
        </div>
            <tr>
                <td class="ftd-1">
                    Name
                </td>
                <td class="ftd-2">
                    {{ $receptionVenue->name }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1">
                    Venue
                </td>
                <td class="ftd-2">
                    {{ $receptionVenue->dinner_venue->name }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1">
                    Date
                </td>
                <td class="ftd-2">
                    {{ dateFormat($receptionVenue->wedding_date) }} {{ date('(H.i)',strtotime($receptionVenue->slot)) }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1">
                    Decoration
                </td>
                <td class="ftd-2">
                    @if ($rv_decoration)
                        {{ $rv_decoration->service }}
                    @else
                        Basic Decoration
                    @endif
                </td>
            </tr>
        </table>
    @endif
    {{-- ADDITIONAL SERVICES --}}
    @if ($additional_service_ids)
        <table class="table email-table m-b-18">
        <div class="tb-title">
            {{ ++$no.". " }}Additional Services
        </div>
        @foreach ($additional_service_ids as $ano=>$addser_id)
            @php
                $additional_service = $additionalServices->where('id',$addser_id)->first();
            @endphp
            @if ($additional_service)
                <tr>
                    <td class="ftd-1">
                        {{ "- ". $additional_service->service }}
                    </td>
                </tr>
            @endif
        @endforeach
            
        </table>
    @endif
    @if ($orderWedding->remark)
        ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="tb-title">
            Remark
        </div>
        <div class="email-paragraft">
            {!! $orderWedding->remark !!}
        </div>
    @endif
    ----------------------------------------------------------------------------------------------------------------------------<br>
    This email has been sent automatically, to confirm the order, please use the following link <a target="__blank" href="{{ $order_link }}"><button class="btn btn-primary" style="color: rgb(0, 60, 255); padding: 4px 18px;border-radius: 100px;">Order Detail</button></a><br>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <i>If an error occurs, please contact the administrator or IT staff, Bali Kami Tour!</i>
        <br>
        <br>
    <p style="text-decoration-line: underline;">Online booking system {{"V". config('app.app_version') }} | PT. Bali Kami | {{ date("Y",strtotime($now)) }}</p>
</body>
</html>