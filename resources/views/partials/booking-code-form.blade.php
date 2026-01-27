<div class="card-box">
    <div class="card-box-title">
        <div class="subtitle">
            <i class="icon-copy fa fa-qrcode" aria-hidden="true"></i> @lang('messages.Booking Code')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="bookingcode-form" style="padding:0px;">
                @csrf
                <div class="form-group">
                    <input type="text" 
                           id="bookingcode-input"
                           style="text-transform: uppercase;" 
                           class="form-control" 
                           name="bookingcode" 
                           placeholder="@lang('messages.Enter Booking Code')">
                </div>
            </form>
            <button type="submit" form="bookingcode-form" class="btn btn-primary" style="float: right;">
                <i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')
            </button>
            <!-- Menampilkan informasi booking code setelah submit -->
            <div id="bookingcode-info">
                <!-- Booking code dan discounts akan tampil di sini setelah form disubmit -->
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bookingcode-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            let bookingcode = $('#bookingcode-input').val();
            let _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('bookingcode.check') }}", // Route untuk menyimpan booking code
                method: "POST",
                data: {
                    bookingcode: bookingcode,
                    _token: _token
                },
                success: function(response) {
                    if (response.success) {
                        // Tampilkan pesan jika booking code berhasil disimpan
                        alert(response.message);

                        // Tampilkan data session di halaman tanpa refresh
                        $('#bookingcode-info').html(`
                            <div>
                                <p>Booking Code: ${response.bookingcode}</p>
                                <p>Discounts: ${response.bookingcode}</p>  <!-- Sesuaikan data yang akan ditampilkan -->
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
    });

</script>