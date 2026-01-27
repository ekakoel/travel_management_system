{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Virtual Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Pembayaran Virtual Account</h2>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Invoice: {{ $va->invoice_number }}</h5>
            <p class="card-text"><strong>Virtual Account:</strong> {{ $va->virtual_account_number }}</p>
            <p class="card-text"><strong>Jumlah:</strong> Rp {{ number_format($va->amount, 2, ',', '.') }}</p>
            <p class="card-text"><strong>Expired:</strong> {{ $va->expired_date }}</p>
            <p class="card-text"><strong>Status:</strong> <span class="badge bg-{{ $va->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($va->status) }}</span></p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                Bayar Sekarang
            </button>
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">Pembayaran Kartu Kredit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <iframe id="paymentIframe" src="" width="100%" height="500px" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="{{ $va->how_to_pay_page }}" class="btn btn-primary" target="_blank">Cara Pembayaran</a>
        </div>
    </div>
</div>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var paymentUrl = "{{ $va->how_to_pay_page ?? '' }}"; // Ambil dari controller
    
        var modal = document.getElementById("paymentModal");
        modal.addEventListener("show.bs.modal", function () {
            document.getElementById("paymentIframe").src = paymentUrl;
        });
    
        modal.addEventListener("hidden.bs.modal", function () {
            document.getElementById("paymentIframe").src = "";
        });
    });
    </script>
</html> --}}
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    </head>
    <body>
        <button id="checkout-button">Checkout Now</button>
        <script type="text/javascript">
        var checkoutButton = document.getElementById('checkout-button');
        // Example: the payment page will show when the button is clicked
        checkoutButton.addEventListener('click', function () {
            loadJokulCheckout('https://staging.doku.com/checkout-link-v2/bce4a6467e3d4e319ef1477e998e68f920253304143357951'); // Replace it with the response.payment.url you retrieved from the response
        });
        </script>
    </body>
</html>
