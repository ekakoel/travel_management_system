<!DOCTYPE html>
<html>
<head>
    <title>Print SPK {{ $spk->spk_number }}</title>
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
        }
        .destination-container{
            border: 1px solid grey;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 0px;
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
        .qrcode-container {
            width: fit-content;
            padding-right: 18px;
            justify-items: center;
        }
        .btn-modal-dismis{
            position: absolute;
            height: 30px;
            padding: 0 !important;
            border-radius: 50px;
            top: 13px;
            right: 14px;
            width: 30px;
            background-color: white;
            color: black;
            line-height: 1;
        }
        .qrcode {
            position: relative;
            width: fit-content;
            margin-bottom: 18px;
            border: 1px solid grey;
            padding: 18px 21px 13px 18px;
            border-radius: 9px;
        }
        .qrcode .overlay {
            border-radius: 8px;
            width: 120px;
            height: 120px;
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
        .caption .btn-checkin{
            width: 162px;
            height: 57px;
            margin: 18px 0;
            background-color: #1010ff;
            color: white;
            border-radius: 50px;
            border: 2px solid #000077;
        }
        
        .description{
            width: 100%;
        }
        .description-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px; /* jarak antar item */
        }
        .description-item title{
            align-items: center;
        }

        .description-item .icon {
            width: 28px;         /* ukuran konsisten */
            height: 28px;        /* tinggi sama */
            display: flex;
            align-items: center; 
            justify-content: center;
            font-size: 18px;     /* ukuran emoji/icon */
            margin-right: 8px;   /* jarak dengan teks */
            background: #f0f0f0; /* opsional, buat background kotak */
            border-radius: 50%;  /* opsional, bulat */
        }

        .status-spk{
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
        .description-item .desc {
            flex: 1;
            font-size: 14px;
            color: #333;
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
            align-content: start;
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
        .modal{
            background-color: #00000063;
            backdrop-filter: blur(2px);
        }
        .modal-header{
            background-color: #003481;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
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
        .body-padding{
            padding: 27px 18%;
        }
        label span{
            color: red;
        }

        @media (max-width: 1024px) {
            .body-padding {
                padding: 18px 10%;
            }
        }

        @media (max-width: 768px) {
            .body-padding {
                padding: 18px 5%;
            }
        }

        @media (max-width: 480px) {
            .body-padding {
                padding: 44px 27px;
            }

            .destination {
                flex-direction: column !important;
            }
            .qrcode-container {
                width: 100%;
                padding-right: 0 !important;
                justify-items: center;
            }
            .caption .btn-checkin {
                width: 100%;
                height: 57px;
                margin: 18px 0;
                background-color: #1010ff;
                color: white;
                border-radius: 50px;
                border: 2px solid #000077;
            }
        }

        @media print {
            .body-padding {
                padding: 0 !important;
            }
        }

    </style>
</head>
<body class="body-padding">
    @if ($spk->spk_date < $now)
        <div class="status-spk">
            <img src="{{ asset("/storage/icon/expired.png") }}" alt="SPK Expired">
        </div>
    @endif
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="spk-header">
        <h2>Surat Perintah Kerja (SPK)</h2>
        <p><strong>Nomor:</strong> {{ $spk->spk_number }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y') }}</p>
        <hr>
    </div>

    <h4 class="section-title">Pesanan (Order)</h4>
    <table>
        <tr>
            <th>Nomor Pesanan (Order):</th>
            <td>{{ $spk->order_number}}</td>
        </tr>
        <tr>
            <th>Jenis Pesanan (Type)</th>
            <td>{{ $spk->type }}</td>
        </tr>
        <tr>
            <th>Tanggal Pesanan</th>
            <td>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y')  }}</td>
        </tr>
    </table>
    @if (count($guests)>0)    
        <h4 class="section-title">Daftar Tamu</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 10% !important">#</th>
                    <th style="width: 40% !important">Nama</th>
                    <th style="width: 15% !important">Umur</th>
                    <th style="width: 35% !important">Kontak</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $no_guest=>$guest)
                    <tr>
                        <td>{{ ++$no_guest }}</td>
                        <td>{{ $guest->name ?? '-' }}</td>
                        <td>{{ $guest->age??"-" }}</td>
                        <td>{{ $guest->phone??"-" }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div class="row">
        <div class="col-md-6">
            <h4 class="section-title">Data Transport</h4>
            <table>
                <tr>
                    <th>Kendaraan</th>
                    <td>{{ $spk->transport->type ?? '' }}, {{ $spk->transport->brand." ". $spk->transport->name }}</td>
                </tr>
                <tr>
                    <th>Nomor Polisi</th>
                    <td>{{ $spk->transport->number_plate ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kapasitas Penumpang</th>
                    <td>{{ $spk->transport->capacity ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h4 class="section-title">Data Driver</h4>
            <table>
                <tr>
                    <th>Nama Driver</th>
                    <td>{{ $spk->driver->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>No. Telepon</th>
                    <td>{{ $spk->driver->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>SIM</th>
                    <td>{{ $spk->driver->license ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
    

    

    
    <div class="page-brake-avoid">
        @if (isset($spk->destinations) && count($spk->destinations) > 0)
            <h4 class="section-title">Destinasi / Tujuan</h4>
            @foreach($spk->destinations as $no=>$destination)
                <div class="destination-container page-brake-avoid">
                    <div class="destination">
                        <h6>{{ $destination->destination_name }}</h6>
                    </div>
                    <div class="caption">
                        @if ($destination->status == "Visited")
                            <p>
                                {{ $spk->driver->name }} melakukan Check-in pada: {{ \Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y (H:i)') }}
                            </p>
                        @else
                            <p>
                                <i>Laporan tidak ditemukan</i>
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="note">
        <h4 class="section-title">Informasi SPK Kedaluwarsa</h4>
        <ul>
            <li>SPK ini adalah laporan layanan transportasi yang sudah melewati tanggal pelaksanaan.</li>
            <li>Status SPK kedaluwarsa berarti tidak lagi aktif dan tidak bisa diubah atau dilakukan check-in.</li>
            <li>Data disimpan untuk arsip dan referensi laporan perjalanan.</li>
        </ul>
    </div>
</body>
</html>
