<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    </head>
    <body>
        <button id="checkout-button">Checkout Now</button>

        <script type="text/javascript">
        var checkoutButton = document.getElementById('checkout-button');
        checkoutButton.addEventListener('click', function () {
            loadJokulCheckout('https://jokul.doku.com/checkout/link/SU5WFDferd561dfasfasdfae123c20200510090550775');
        });
        </script>
    </body>
</html>