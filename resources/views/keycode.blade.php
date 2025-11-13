{{--  keycode viewer ============================================================================================================================================ --}}
{{-- <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keycode Tester</title>
<script>
    function displayKeyCode(event) {

        var e = event || window.event;
        var keycode = e.keyCode || e.which;
        

        alert("Key pressed: " + String.fromCharCode(keycode) + " | Keycode: " + keycode);
    }

    document.addEventListener('keydown', displayKeyCode);
</script>
</head>
<body>
    <h1>Keycode Tester</h1>
    <p>Press any key to display its keycode.</p>
</body>
</html> --}}


{{--  DISABLE RIGHT CLICK ============================================================================================================================================ --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Disable Right-Click</title>
<script>
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
</script>
</head>
<body>
    <h1>Right-Click is Disabled</h1>
    <p>Try right-clicking on this page.</p>
</body>
</html>