<!DOCTYPE html>
<html>
<head>
    <title>Print SPK {{ $spk->spk_number }}</title>
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
                padding: 18px 2%;
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
<body onload="window.print()" class="body-padding">
{{-- <body> --}}

    <!-- Header -->
    <div class="spk-header">
        <h2>Surat Perintah Kerja (SPK)</h2>
        <p><strong>Nomor:</strong> {{ $spk->spk_number }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y') }}</p>
        <hr>
    </div>

    <!-- Data Transport -->
    <h4 class="section-title">Data Reservasi</h4>
    <table>
        <tr>
            <th>Nomor Pesanan</th>
            <td>{{ $spk->order_number??"-" }}</td>
        </tr>
        
        <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y')  }}</td>
        </tr>
    </table>
    <!-- Data Airport Shuttle -->
    @if ($spk->type == "Airport Shuttle")
        <h4 class="section-title">Detail Penerbangan</h4>
        <table>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th>Tanggal</th>
                <th>Nomor Penerbangan</th>
                <th>Tipe</th>
            </tr>
            <tbody>
                @foreach ($spk->airport_shuttles as $airport_shuttle)
                    <tr>
                        <td style="text-align: center;" >{{ $loop->iteration }}</td>
                        <td>{{ date('d M Y (H:i)', strtotime($airport_shuttle->date)) }}</td>
                        <td>{{ $airport_shuttle->flight_number }}</td>
                        <td>{{ $airport_shuttle->nav == "In"?"Kedatangan (Arrival)":"Keberangkatan (Departure)" }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <!-- Data Tamu -->
    @if (count($spk->guests)>0)
        <h4 class="section-title">Daftar Tamu</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 5% !important; text-align: center;">#</th>
                    <th style="width: 45% !important">Nama</th>
                    <th style="width: 15% !important">Usia</th>
                    <th style="width: 35% !important">Kontak</th>
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
    <h4 class="section-title">Data Transport</h4>
    <table>
        <tr>
            <th>Kendaraan</th>
            <td>{{ $spk->transport->brand." ". $spk->transport->name }}</td>
        </tr>
        <tr>
            <th>Nomor Polisi</th>
            <td>{{ $spk->plate_number ?? '-' }}</td>
        </tr>
    </table>

    <!-- Data Driver -->
    <h4 class="section-title">Data Driver</h4>
    <table>
        <tr>
            <th>Nama Driver</th>
            <td>{{ $spk->driver->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>No. Telepon</th>
            <td>{{ '+'.$spk->driver->phone ?? '-' }}</td>
        </tr>
    </table>

   
    <!-- Data Destinasi -->
    <div class="page-brake-avoid">
        <h4 class="section-title">Destinasi</h4>
        @foreach($spk->destinations as $no=>$destination)
            <div class="destination-container page-brake-avoid">
                <div class="destination">
                    <div class="qrcode">
                        @if ($destination->status == "Visited")
                            <div class="overlay">
                                <div class="status">
                                    <div class="status-{{ $destination->status }}">☑</div>
                                </div>
                                <p>Visited on:</p>
                                <p>{{ \Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y') }}</p>
                            </div>
                        @else
                            {!! QrCode::size(120)->generate(route('spks.checkin.page', $destination->id)) !!}<br>
                        @endif
                    </div>
                    <div class="description">
                        <p><strong>{{ $destination->destination_name }}</strong></p>
                        <p>Google Map: <a target="__blank" href="{{ $destination->destination_address }}"> {{ $destination->destination_address }}</a></p>
                        <p>Jam Tujuan: {{ date('H:i a',strtotime($destination->date)) }}</p>
                        <p>Keterangan: <i>{!! $destination->description ?? '-' !!}</i></p>
                    </div>
                </div>
                <div class="caption">
                    @if ($destination->status == "Visited")
                        <p>
                            ✔ {{ $spk->driver->name }} melakukan Check-in pada tanggal {{ \Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y') }}, 
                            dari lokasi <a target="__blank" href="https://www.google.com/maps?q={{ $destination->checkin_latitude }},{{ $destination->checkin_longitude }}"> Lihat di Map</a>
                        </p>
                    @else
                        <i>Saat anda berada di {{ $destination->destination_name }}, scan QR code untuk melakukan checkin!</i>
                    @endif
                </div>
                <div class="number">
                    {{ ++$no }}
                </div>
                
            </div>
        @endforeach
    </div>

    <!-- Catatan & Himbauan -->
    <div class="note">
        <h4 class="section-title">Ketentuan & Himbauan untuk Driver</h4>
        <ul>
            <li>Pastikan kondisi kendaraan dalam keadaan bersih, aman, dan siap digunakan.</li>
            <li>Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan.</li>
            <li>Selalu mengemudi dengan baik, hati-hati, dan mematuhi rambu lalu lintas.</li>
            <li>Pastikan kenyamanan penumpang selalu menjadi prioritas utama.</li>
            <li>Lakukan Check-in di lokasi destinasi sesuai ketentuan yang tertera pada QR Code.</li>
            <li>Jaga sikap profesional, ramah, dan menjaga nama baik perusahaan.</li>
        </ul>
    </div>
</body>
</html>
