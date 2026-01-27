{{-- MODAL ADD ORDER PAYMENT CONFIRMATION --}}
<div class="modal fade" id="desktop-admin-add-order-receipt-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title text-left">
                    <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>Upload Receipt</div>
                </div>
                <form id="desktop-payment-confirm-wedding-{{ $order->id }}" action="{{ route('func.admin.add.receipt',$order->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row text-left">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row m-t-27">
                                        <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                        <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                        <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                        <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                        <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                        <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                        <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                        <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="dropzone">
                                                <div class="img-preview">
                                                    <img id="desktop-img-preview" src="#" alt="Your Image" style="max-width: 100%; display: none;" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 m-t-8">
                                            <div class="form-group">
                                                <label for="desktop_receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                <input type="file" name="desktop_receipt_name" id="desktop_receipt_name" class="custom-file-input @error('desktop_receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('desktop_receipt_name') }}" required>
                                                @error('desktop_receipt_name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                    </div>
                </form>
                <div class="card-box-footer">
                    <button type="submit" form="desktop-payment-confirm-wedding-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('desktop_receipt_name').addEventListener('change', function(event) {
        const imgPreview = document.getElementById('desktop-img-preview');
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imgPreview.src = '#';
            imgPreview.style.display = 'none';
        }
    });
</script>