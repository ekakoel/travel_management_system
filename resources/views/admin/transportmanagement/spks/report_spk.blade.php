<!DOCTYPE html>
<html>
<head>
    <title>SPK Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 14px;
            line-height: 1.5;
        }
        h2{
            text-transform: uppercase;
            font-family: fangsong;
            margin-bottom: 0px;
        }
        h4{
            margin: 18px 0 4px 0;
            text-decoration: none;
        }
        .spk-header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
            /* text-decoration: underline; */
        }
        .destination-container{
            border: 1px solid grey;
            padding: 18px 8px 18px 69px;
            border-radius: 8px;
            margin-bottom: 27px;
            position: relative;
        }
        .number{
            position: absolute;
            top: 0px;
            left: 19px;
            font-size: 3rem;
            font-weight: 700;
            color: grey;
        }
        .destination {
            display: flex;
            align-items: flex-start;
        }
        
        .qrcode {
            position: relative;
            width: fit-content;
            margin-right: 18px;
            margin-bottom: 8px;
            border: 1px solid grey;
            padding: 18px 21px 13px 18px;
            border-radius: 9px;
        }
        .qrcode .overlay {
            border-radius: 8px;
            width: 120px;
            height: 120px;
            /* background-color: rgb(255 255 255 / 44%);
            backdrop-filter: blur(4px); */
            align-content: center;
        }
        .qrcode .overlay p{
            font-size: 11px;
            color: #555;
        }
        .qrcode .overlay a{
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0px);
            width: max-content;
        }
        .caption{
            width: 100%;
        }
        .caption i{
            font-size: 0.7rem;
            color: grey;
        }
        .caption p{
            font-size: 12px;
            color: rgb(46, 46, 46);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10px 0;
        }
        th{
            width: 30%;
        }
        table {
            overflow: hidden;
            border: 1px solid grey;
        }
        th, td {
            border: 1px solid grey;
            padding: 6px;
            text-align: left;
        }
        .note {
            margin-top: 25px;
            font-size: 13px;
            border-top: 1px dashed #555;
            padding-top: 10px;
        }
        p{
            padding: 0 !important;
            margin: 0 !important;
        }
        .status-Pending{
            font-size: 2rem;
            color: grey;
        }
        .status-Visited{
            font-size: 2rem;
            color: green;
        }
        .btn{
            padding: 4px 18px;
        }
        .page-brake-avoid{
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .btn-checkin{
            width: 161px;
            background-color: #1010ff;
            color: white;
            border-radius: 6px;
            border: 2px solid #000077;
        }
        .body-padding{
            padding: 27px 18%;
        }
        
        .destination-status{
            position: absolute;
            top: 0px;
            right: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 700;
            opacity: 0.5;
            font-style: italic;
        }
        .Pending{
            color: grey;
        }
        .Visited{
            color: blue;
        }
        .status-spk{
            position: absolute;
            top: 32px;
            rotate: 342deg;
            width: auto;
            height: 50px;
            vertical-align: middle;
            opacity: .5;
            z-index: 10;
        }
        .status-spk img{
            width: auto;
            height: 50px;
            vertical-align: middle;
            opacity: .5;
            z-index: 10;
        }
        /* Tablet (max-width: 1024px) */
        @media (max-width: 1024px) {
            .body-padding {
                padding: 18px 10%;
            }
        }

        /* HP landscape (max-width: 768px) */
        @media (max-width: 768px) {
            .body-padding {
                padding: 18px 5%;
            }
        }

        /* HP kecil (max-width: 480px) */
        @media (max-width: 480px) {
            .body-padding {
                padding: 18px 4%;
            }
        }

        /* Saat print: hilangkan padding */
        @media print {
            .body-padding {
                padding: 0 !important;
            }
        }

    </style>
</head>
<body class="body-padding">
    @if ($spk->spk_date < $expired_date)
        <div class="status-spk">
            <img src="{{ asset("/storage/icon/expired.png") }}" alt="SPK Expired">
        </div>
    @endif
    <!-- Header -->
    <div class="spk-header">
        <h2>Surat Perintah Kerja (SPK)</h2>
        <p>{{ $spk->spk_number }}</p>
        <p>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('en')->translatedFormat('l, d M Y') }}</p>
        <hr>
    </div>

    <!-- Data Transport -->
    <h4 class="section-title">Reservation</h4>
    <table>
        <tr>
            <th>Order Number</th>
            <td>{{ $spk->order_number??"-" }}</td>
        </tr>
        
        <tr>
            <th>Service Date</th>
            <td>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('en')->translatedFormat('l, d M Y')  }}</td>
        </tr>
        <tr>
            <th>Service Type</th>
            <td>{{ $spk->type  }}</td>
        </tr>
        <tr>
            <th>Number of Guests</th>
            <td>{{ $spk->number_of_guests  }} guests</td>
        </tr>
    </table>
    <!-- Data Airport Shuttle -->
    @if ($spk->type == "Airport Shuttle")
        <h4 class="section-title">Flight Detail</h4>
        <table>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th>Date</th>
                <th>Flight Number</th>
                <th>Type</th>
            </tr>
            <tbody>
                @foreach ($spk->airport_shuttles as $airport_shuttle)
                    <tr>
                        <td style="text-align: center;" >{{ $loop->iteration }}</td>
                        <td>{{ date('d M Y (H:i)', strtotime($airport_shuttle->date)) }}</td>
                        <td>{{ $airport_shuttle->flight_number }}</td>
                        <td>{{ $airport_shuttle->nav == "In"?"Arrival":"Departure" }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <!-- Data Tamu -->
    @if (count($spk->guests)>0)
        <h4 class="section-title">Guest list</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 5% !important; text-align: center;">#</th>
                    <th style="width: 45% !important">Name</th>
                    <th style="width: 15% !important">Age</th>
                    <th style="width: 35% !important">Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($spk->guests as $no_guest=>$guest)
                    <tr>
                        <td style="text-align: center;">{{ ++$no_guest }}</td>
                        <td>{{ $guest->name }}{{ $guest->name_mandarin?" (".$guest->name_mandarin.")":""; }}</td>
                        <td>{{ $guest->age??"-" }}</td>
                        <td>{{ $guest->phone??"-" }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <!-- Data Transport -->
    <h4 class="section-title">Vehicle Detail</h4>
    <table>
        <tr>
            <th>Vehicle</th>
            <td>{{ $spk->transport->type ?? '' }}, {{ $spk->transport->brand." ". $spk->transport->name }}</td>
        </tr>
        <tr>
            <th>Police Number</th>
            <td>{{ $spk->plate_number ?? '-' }}</td>
        </tr>
    </table>

    <!-- Data Driver -->
    <h4 class="section-title">Driver Detail</h4>
    <table>
        <tr>
            <th>Driver Name</th>
            <td>{{ $spk->driver->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>+{{ $spk->driver->phone ?? '-' }}</td>
        </tr>
    </table>

    <!-- Data Destinasi -->
    <div class="page-brake-avoid">
        <h4 class="section-title">Destinations</h4>
        @foreach($spk->destinations as $no=>$destination)
            <div class="destination-container page-brake-avoid">
                <div class="destination">
                    <div class="description">
                        <p><strong>{{ $destination->destination_name }}</strong></p>
                        @if ($destination->destination_address)
                            <p>Google Map: <a target="__blank" href="{{ $destination->destination_address }}"> {{ $destination->destination_address }}</a></p>
                        @endif
                        <p>Destination hour: {{ date('H:i',strtotime($destination->date)) }}</p>
                        <p>Note: <i>{!! $destination->description ?? '-' !!}</i></p>
                    </div>
                </div>
                <div class="caption">
                    @if ($destination->status == "Visited")
                        <p>
                            ✔ {{ $spk->driver->name }} checkin on {{ \Carbon\Carbon::parse($destination->visited_at)->locale('en')->translatedFormat('l, d M Y (H:i)') }}, 
                            from location <a target="__blank" href="https://www.google.com/maps?q={{ $destination->checkin_latitude }},{{ $destination->checkin_longitude }}"> Check on Map</a>
                        </p>
                    @endif
                </div>
                <div class="number">
                    {{ ++$no }}
                </div>
                <div class="destination-status {{ $destination->status }}">{{ $destination->status }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>
