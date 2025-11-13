{{-- <div class="loading-icon hidden pre-loader">
    <div class="pre-loader-box">
        <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo" loading='lazy'></div>
        <div class="loading-text">
            @lang('messages.Processing')...
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#{{ $id ?? 'default' }}").submit(function() {
            $(".result").text("");
            $(".loading-icon").removeClass("hidden");
            $(".submit").attr("disabled", true);
            $(".btn-txt").text("Processing ...");
        });
    });
</script> --}}

<div class="loading-icon hidden pre-loader" id="loading-{{ $id }}">
    <div class="pre-loader-box">
        <div class="sys-loader-logo w3-center">
            <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Loading" loading="lazy">
        </div>
        <div class="loading-text">
            @lang('messages.Processing')...
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#{{ $id }}").on('submit', function() {
            $(".result").text("");
            $("#loading-{{ $id }}").removeClass("hidden"); // ⬅ hanya loading spesifik
            $(this).find(".submit").attr("disabled", true); // hanya tombol dalam form ini
            $(this).find(".btn-txt").text("Processing ...");
        });
    });
</script>
