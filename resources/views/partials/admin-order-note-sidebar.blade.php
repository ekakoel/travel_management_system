@if ($order->status != "Paid")
    <div class="col-md-12 m-b-18">
        <div class="card-box">
            <div class="card-box-title">
                <div class="title">Order Note</div>
            </div> 
            @foreach ($order_notes as $order_note)
                <div class="container-order-note">
                    @php
                        $operator = Auth::user()->where('id',$order_note->user_id)->first();
                    @endphp
                    <p><b>{{ dateTimeFormat($order_note->created_at)." - ".$operator->name }}</b> (<i>{{ $order_note->status }}</i>)</p>
                    <p class="m-l-18">{!! $order_note->note !!}</p>
                    
                    <hr class="form-hr">
                </div>
            @endforeach
            @if ($order->status !== "Paid")
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#add-order-note-{{ $device }}"><button type="button" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Add Note</button></a>
                </div>
            @endif
        </div>
        {{-- MODAL ORDER NOTE --}}
        <div class="modal fade" id="add-order-note-{{ $device }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="title"><i class="fa fa-plus" aria-hidden="true"></i> Add Note</div>
                        </div>
                        <form id="faddAddNote{{ $device }}" action="/fadd-order-note-{{ $order->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label for="status" class="col-sm-12">Type</label>
                                <div class="col-sm-12">
                                    <select name="status" class="custom-select @error('status') is-invalid @enderror" value="{{ old('status') }}">
                                        <option selected value="Urgent">Urgent</option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Error">Error</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Reject">Reject</option>
                                        <option value="Info">Info</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="order_note" class="col-sm-12">Note</label>
                                <div class="col-sm-12">
                                    <textarea name="order_note" placeholder="Insert order note" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                    @error('order_note')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="user_id" value="{{ Auth::User()->id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        </Form>
                        <div class="card-box-footer">
                            <div class="form-group">
                                <button type="submit" form="faddAddNote{{ $device }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Submit</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @if (count($order_notes)>0)
        <div class="col-md-12">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="title">Order Note</div>
                </div> 
                @foreach ($order_notes as $order_note)
                    <div class="container-order-note">
                        @php
                            $operator = Auth::user()->where('id',$order_note->user_id)->first();
                        @endphp
                        <p><b>{{ dateTimeFormat($order_note->created_at)." - ".$operator->name }}</b> (<i>{{ $order_note->status }}</i>)</p>
                        <p class="m-l-18">{!! $order_note->note !!}</p>
                        <hr class="form-hr">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif