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
        <p>{{ $spk->spk_number }}</p>
        <p>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y') }}</p>
        <hr>
    </div>

    <h4 class="section-title">Pesanan (Order)</h4>
    <table>
        <tr>
            <th>Nomor Pesanan (Order)</th>
            <td>{{ $spk->order_number}}</td>
        </tr>
        <tr>
            <th>Jenis Pesanan (Type)</th>
            <td>{{ $spk->type }}</td>
        </tr>
        <tr>
            <th>Tanggal Pesanan</th>
            {{-- <td>{{ \Carbon\Carbon::parse($spk->reservation?->checkin)->locale('id')->translatedFormat('l, d M Y')  }} -- {{ \Carbon\Carbon::parse($spk->reservation?->checkout)->locale('id')->translatedFormat('l, d M Y')." (".$duration." hari)" }}</td> --}}
            <td>{{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y')  }}</td>
        </tr>
        <tr>
            <th>Jumlah Tamu </th>
            <td>{{ $spk->number_of_guests }} guests</td>
        </tr>
    </table>
    <!-- Data Airport Shuttle -->
    @if ($spk->type == "Airport Shuttle")
        <h4 class="section-title">Detail Penerbangan</h4>
        <table>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 35% !important">Tanggal</th>
                <th style="width: 25% !important">Nomor Penerbangan</th>
                <th style="width: 35% !important">Tipe</th>
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
    <h4 class="section-title">Daftar Tamu</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 35% !important">Nama</th>
                <th style="width: 25% !important">Usia</th>
                <th style="width: 35% !important">Kontak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($guests as $no_guest=>$guest)
                <tr>
                    <td style="text-align: center;">{{ ++$no_guest }}</td>
                    <td>{{ $guest->name ?? '-' }} {{ $guest->name_mandarin ?? "" }}</td>
                    <td>{{ $guest->age??"-" }}</td>
                    <td>{{ $guest->phone??"-" }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-6">
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
            </table>
        </div>
    </div>
    

    

    
    <div class="page-brake-avoid">
        @if ($spk->type == "Daily Rent" || $spk->type == "Tour")
            <div class="alert alert-info" role="alert">
                <strong>Catatan:</strong> Untuk layanan Daily Rent dan Tour, pastikan untuk selalu melakukan check-in setiap kali mengunjungi destinasi, dengan menggunakan tombol "+ Tambah Destinasi" dibawah ini!.
            </div>
            <h4 class="section-title">Destinasi / Tujuan</h4>
            @forelse ($spk->destinations as $no=>$destination)
                <div class="destination-container page-brake-avoid">
                    <div class="destination">
                        <div class="description">
                            <div class="title">
                                <h5><strong>{{ $destination->destination_name }}</strong></h5>
                            </div>
                            <div class="description-item">
                                <div class="icon">🕒</div>
                                <div class="desc">Tanggal Checkin: {{ \Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y (H:i)') }}</div>
                            </div>
                            <div class="description-item">
                                <div class="icon">📍</div>
                                <div class="desc">
                                    Lokasi Checkin: 
                                    <a href="{{ $destination->checkin_map_link }}" target="_blank" class="btn btn-sm btn-success color-white">
                                        See on Map
                                    </a>
                                </div>
                            </div>
                            <div class="description-item">
                                <div class="icon">📋</div>
                                <div class="desc"><i>Catatan: {!! $destination->description !!}</i></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                
            @endforelse
            <div class="card shadow p-4">
                <button data-bs-toggle="modal" data-bs-target="#tambahDestinasi{{ $spk->id }}" class="btn btn-primary btn-checkin">✚ Tambah Destinasi</button>
                <div class="modal fade" id="tambahDestinasi{{ $spk->id }}" tabindex="-1" aria-labelledby="confirmSubmitLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="subtitle">
                                    Tambahkan Destinasi
                                </div>
                                <span><button class="btn btn-modal-dismis" data-bs-dismiss="modal">X</button></span>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li class="text-muted">Gunakan form ini untuk menambahkan destinasi wisata yang dikunjungi selama perjalanan.</li>
                                        <li class="text-muted">Lokasi Anda akan dicatat secara otomatis saat melakukan check-in.</li>
                                        <li class="text-muted">Silahkan lengkapi form dibawah ini untuk menambahkan destinasi baru.</li>
                                    </ul>
                                </p>
                                <hr>
                                <form id="driverCheckin" action="{{ route('driver.checkin',$spk->id) }}" method="POST" id="checkin-form">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="destinationName" class="form-label">Nama Lokasi / Nama Tempat / Destinasi <span>*</span></label>
                                        <input type="text" name="destination_name" id="destinationName" class="form-control" placeholder="ex: Monkey Forest" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="ex: Tamu mengunjungi Monkey Forest ..."></textarea>
                                    </div>
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <button type="submit" class="btn btn-primary w-100" id="checkin-btn">
                                        📍 Check-in
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @foreach($spk->destinations as $no=>$destination)
                <h4 class="section-title">Destinasi / Tujuan {{ ++$no }}</h4>
                <div class="destination-container page-brake-avoid">
                    <div class="destination">
                        <div class="qrcode-container">
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
                        </div>
                        <div class="description">
                            <div class="title">
                                <h3><strong>{{ $destination->destination_name }}</strong></h3>
                            </div>
                            <div class="description-item">
                                <div class="icon">📍</div>
                                <div class="desc"><a target="__blank" href="{{ $destination->destination_address }}"> {{ $destination->destination_address }}</a></div>
                            </div>
                            <div class="description-item">
                                <div class="icon">🕒</div>
                                <div class="desc">{{ date('H:i a',strtotime($destination->date)) }}</div>
                            </div>
                            <p><i>{!! $destination->description ?? '-' !!}</i></p>
                        </div>
                    </div>
                    <div class="caption">
                        @if ($destination->status == "Visited")
                            <p>
                                ✔ {{ $spk->driver->name }} melakukan Check-in pada tanggal {{ \Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y (H:i)') }}, 
                                dari lokasi <a target="__blank" href="https://www.google.com/maps?q={{ $destination->checkin_latitude }},{{ $destination->checkin_longitude }}"> Lihat di Map</a>
                            </p>
                        @else
                            <p>
                                <i>Saat anda berada di {{ $destination->destination_name }}, <br>
                                    Klik tombol Check-in atau <br>
                                    scan QR code untuk melakukan checkin!</i>
                            </p>
                            <button data-bs-toggle="modal" data-bs-target="#checkIn{{ $destination->id }}" class="btn btn-primary btn-checkin">📍 Check-in</button>
                            <div class="modal fade" id="checkIn{{ $destination->id }}" tabindex="-1" aria-labelledby="confirmSubmitLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="text-center">
                                            <h3 class="mb-3">Driver Check-in</h3>
                                            <hr>
                                            <h5 class="mb-3">🚨 Perhatian! 🚨</h5>
                                        </div>
                                        <ol>
                                            <li class="text-muted">Pastikan lokasi Anda saat ini berada di titik yang sesuai dengan destinasi yang ditentukan.</li>
                                            <li class="text-muted">Pastikan Anda sudah mengizinkan akses lokasi pada perangkat yang digunakan.</li>
                                            <li class="text-muted">Lokasi Anda akan dicatat sesuai dengan posisi saat melakukan check-in.</li>
                                            <li class="text-muted">Terima kasih atas perhatian dan kerjasamanya.</li>
                                        </ol>
                                        <i class="text-muted mb-3">Jangan lupa untuk selalu menjaga kondisi tubuh tetap prima, memastikan kebersihan transportasi, menjaga ketepatan waktu penjemputan, serta selalu mengemudi dengan baik dan penuh tanggung jawab.</i>
                                        <hr>
                                        <!-- Icon -->
                                        <div class="mb-3 text-center">
                                            <i class="bi bi-geo-alt-fill map-icon"></i><br>
                                            <strong>Silakan lakukan Check-in hanya saat Anda berada di<br>{{ $destination->destination_name }}</strong>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form id="checkinForm{{  $destination->id }}" method="POST" action="{{ route('spk.checkin', $destination->id) }}">
                                        @csrf
                                            <input type="hidden" name="latitude" id="latitude">
                                            <input type="hidden" name="longitude" id="longitude">
                                            <button type="submit" class="btn btn-primary" class="btn btn-success">Check-in</button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                        @endif
                    </div>
                    {{-- <div class="number">
                        {{ ++$no }}
                    </div> --}}
                    
                </div>
            @endforeach
        @endif
    </div>

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
<script>
    document.querySelectorAll("form[id^='checkinForm']").forEach(function(form) {
        form.addEventListener("submit", function(event) {
            if (navigator.geolocation) {
                event.preventDefault();
                navigator.geolocation.getCurrentPosition(function(position) {
                    form.querySelector("input[name='latitude']").value = position.coords.latitude;
                    form.querySelector("input[name='longitude']").value = position.coords.longitude;
                    form.submit();
                }, function(error) {
                    alert("Unable to get location. Please enable GPS.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('driverCheckin');
        const checkinBtn = document.getElementById('checkin-btn');

        form.addEventListener('submit', function(event) {
            event.preventDefault(); // cegah submit langsung

            if (navigator.geolocation) {
                // Nonaktifkan tombol sementara agar tidak double klik
                checkinBtn.disabled = true;
                checkinBtn.textContent = "📍 Getting location...";

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Isi nilai latitude dan longitude
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;

                        // Submit form setelah data siap
                        form.submit();
                    },
                    function(error) {
                        let message = "Terjadi kesalahan saat mengambil lokasi.";
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                message = "Akses lokasi ditolak. Mohon aktifkan GPS.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                message = "Gagal mendapatkan lokasi. Coba lagi.";
                                break;
                        }
                        alert(message);
                        checkinBtn.disabled = false;
                        checkinBtn.textContent = "📍 Check-in";
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Browser Anda tidak mendukung fitur geolocation.");
            }
        });
    });
</script>
</html>
