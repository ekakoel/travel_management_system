<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Driver Check-in</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .checkin-card {
      max-width: 500px;
      margin: 30px auto;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .map-icon {
      font-size: 60px;
      color: #0d6efd;
    }
    .btn-checkin {
      border-radius: 30px;
      padding: 12px;
      font-size: 18px;
    }
  </style>
</head>
<body>

<div class="container">
    <div class="checkin-card card p-4">
        @if ($spkDestination->status == "Pending")
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
                <strong>Silakan lakukan Check-in hanya saat Anda berada di<br>{{ $spkDestination->destination_name }}</strong>
            </div>
        @else
            <div class="text-center">
                <h3 class="mb-3">Checked-in</h3>
            </div>
        @endif
        @if ($spkDestination->status == "Visited")
            <hr>
            <!-- Icon -->
            <div class="text-center">
                <p>Anda sudah melakukan check-in di {{ $spkDestination->destination_name }}</p>
                <p>Untuk melihat lokasi anda check-in, silahkan melihatnya menggunakan tombol "View on Google Maps" di bawah ini!</p>
            </div>
        @endif

        <!-- Alert success -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert error -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        @if ($spkDestination->status == "Pending")
            <form id="checkinForm" method="POST" action="{{ route('spk.checkin', $spkDestination->id) }}">
                @csrf
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <button type="submit" class="btn btn-primary w-100 btn-checkin">📍 Check-in</button>
            </form>
        @endif

        <hr>

        <!-- Google Maps link (optional preview) -->
        @if($spkDestination->checkin_map_link)
        <p class="mt-2">Checked-in location:</p>
        <a href="{{ $spkDestination->checkin_map_link }}" target="_blank" class="btn btn-outline-success w-100">
            View on Google Maps
        </a>
        @endif
    </div>
</div>

<script>
  document.getElementById("checkinForm").addEventListener("submit", function(event) {
    if (navigator.geolocation) {
      event.preventDefault();
      navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById("latitude").value = position.coords.latitude;
        document.getElementById("longitude").value = position.coords.longitude;
        event.target.submit();
      }, function(error) {
        alert("Unable to get location. Please enable GPS.");
      });
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  });
</script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</body>
</html>
