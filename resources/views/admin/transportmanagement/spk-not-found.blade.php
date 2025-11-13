<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Tidak Ditemukan</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (optional untuk ikon) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <div class="container d-flex flex-column justify-content-center align-items-center flex-grow-1 text-center">
        <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
            <div class="card-body">
                <div class="mb-3 text-danger">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
                <h1 class="h4 fw-bold mb-3">SPK Tidak Ditemukan</h1>
                <p class="text-muted">
                    Maaf, SPK yang Anda cari tidak tersedia, gunakan link yang dibagikan untuk membuka SPK anda!
                </p>
                {{-- <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-house-door"></i> Kembali ke Beranda
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
