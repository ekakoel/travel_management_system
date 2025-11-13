<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approval Account {{ $user->email }}</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <style>
        .text{
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    @php
        $now = date('Y-m-d', time());
    @endphp
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <h1 style="padding: 0 !important; margin: 0 !important;">Account Approval</h1>
    ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <p>Your account has been approved and can be used to access all services provided by PT. Bali Kami Tour</p><br>
        </div>
        <table class="table email-table">
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                   Name
                </td>
                <td class="ftd-2">
                    {{ $user->name }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                   User Name
                </td>
                <td class="ftd-2">
                    {{ $user->username }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    E-mail
                 </td>
                 <td class="ftd-2">
                     {{ $user->email }}
                 </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Telephone
                 </td>
                 <td class="ftd-2">
                     {{ $user->phone }}
                 </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Office
                 </td>
                 <td class="ftd-2">
                     {{ $user->office }}
                 </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Address
                 </td>
                 <td class="ftd-2">
                     {{ $user->address }}
                 </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Country
                 </td>
                 <td class="ftd-2">
                     {{ $user->country }}
                 </td>
            </tr>
        </table>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    Do not replay!, This email is sent because you have registered on https://online.balikamitour.com on {{ dateTimeFormat($user->created_at) }}.<br>
    Please disregard this message if you did not do so<br>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <i>If an error occurs, please contact the administrator or IT staff, Bali Kami Tour!</i>
        <br>
        <br>
    <p style="text-decoration-line: underline;">Online booking system {{"V". config('app.app_version') }} | PT. Bali Kami | {{ date("Y",strtotime($now)) }}</p>
</body>
</html>