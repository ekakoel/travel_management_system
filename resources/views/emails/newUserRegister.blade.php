<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New User Registration {{ $user->email }}</title>
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
    <h1 style="padding: 0 !important; margin: 0 !important;">New Registration</h1>
    ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <b>User Detail</b><br>
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
    This email has been sent automatically if any registration on Online System of PT.Bali Kami<br>
    To activate user, please use the following link <a target="__blank" href="https://online.balikamitour.com/user-manager"><button class="btn btn-primary" style="color: rgb(0, 60, 255); padding: 4px 18px;border-radius: 100px;">User Manager</button></a><br>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <i>If an error occurs, please contact the administrator or IT staff, Bali Kami Tour!</i>
        <br>
        <br>
    <p style="text-decoration-line: underline;">Online booking system {{"V". config('app.app_version') }} | PT. Bali Kami | {{ date("Y",strtotime($now)) }}</p>
</body>
</html>