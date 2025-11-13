@section('title', __('messages.Reservation Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="row">
                    <div class="col-md-8">
                        @if($reviewLinks->count())
                            <div class="card-box mb-4">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-list" aria-hidden="true"></i> Review Links History</div>
                                </div>
                                <div class="search-container flex-end m-b-18">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchByAgent" type="text" onkeyup="searchByAgent()" class="form-control" name="search-by-agent" placeholder="Search by Agent...">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchByBookingCode" type="text" onkeyup="searchByBookingCode()" class="form-control" name="search-by-booking-code" placeholder="Search by Booking Code...">
                                    </div>
                                </div>
                                </form>
                                <div class="table-responsive">
                                    <table id="tbLinkReview" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Booking Code</th>
                                                <th>Expires At</th>
                                                <th>Link</th>
                                                <th>Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reviewLinks as $link)
                                                <tr class="review-item" data-bookingCode="{{ strtolower($link->booking_code) }}">
                                                    <td class="list-agent-name">{{ $link->agent }}</td>
                                                    <td>{{ $link->booking_code }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($link->expires_at)->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <a href="http://reviewyourtour.fwh.is/{{ $link->booking_code }}/{{ $link->jumlah_review }}" target="_blank">Open Link</a>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-toggle="modal" data-target="#shareModal{{ $link->id }}">
                                                            <button class="btn btn-sm btn-info">
                                                                Share
                                                            </button>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <!-- Modal -->
                                                <div class="modal fade" id="shareModal{{ $link->id }}" aria-labelledby="shareModalLabel{{ $link->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box text-center">
                                                                <div class="d-flex justify-content-between align-items-center px-3 py-2">
                                                                    <h5 class="modal-title">Share QR & Link</h5>
                                                                    <button type="button" class="btn btn-close" data-dismiss="modal">
                                                                        <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>

                                                                <div class="qrcode-container px-4 pb-3 m-b-18">
                                                                    <!-- QR Code -->
                                                                    <img src="{{ asset('storage/reviews/qrcodes/' . $link->qr_code_path) }}" alt="QR Code" class="qrcode-img mb-3">

                                                                    <!-- Link -->
                                                                    <p class="m-b-8">
                                                                        <a href="{{ $link->link }}" target="_blank">{{ $link->link }}</a>
                                                                    </p>

                                                                </div>
                                                                <!-- Share Buttons -->
                                                                <div class="d-flex justify-content-center gap-8 flex-wrap m-b-8">
                                                                    <!-- WhatsApp -->
                                                                    <a class="btn btn-success btn-sm" target="_blank"
                                                                    href="https://api.whatsapp.com/send?text={{ urlencode('We kindly invite you to share your valuable feedback on your tour experience by clicking the link below. ' . $link->link) }}">
                                                                        <i class="icon-copy fa fa-whatsapp" aria-hidden="true"></i> WhatsApp
                                                                    </a>

                                                                    <!-- Copy to Clipboard -->
                                                                    <button class="btn btn-secondary btn-sm" onclick="copyToClipboard('{{ 'We kindly invite you to share your valuable feedback on your tour experience by clicking the link below. ' . $link->link }}')">
                                                                        <i class="icon-copy fa fa-copy" aria-hidden="true"></i> Copy Link
                                                                    </button>

                                                                    <!-- WeChat & Others (suggestion) -->
                                                                    <button class="btn btn-info btn-sm" onclick="alert('You can share the QR code manually on WeChat or other apps, by screenshoot the QR kode.')">
                                                                        <i class="icon-copy fa fa-wechat" aria-hidden="true"></i> WeChat
                                                                    </button>
                                                                </div>

                                                                <!--<div class="text-end pb-3">-->
                                                                <!--    <button type="button" class="btn btn-danger" data-dismiss="modal">-->
                                                                <!--        <i class="icon-copy fa fa-close" aria-hidden="true"></i> Close-->
                                                                <!--    </button>-->
                                                                <!--</div>-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @endforeach
                                        </tbody>
                                    </table>
                                   
                                </div>
                                <div class="mt-2">
                                    {{ $reviewLinks->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-file-text-o" aria-hidden="true"></i> Generate Review Link</div>
                            </div>
                            @if(session('success'))
                                <div class="alert alert-success">{!! session('success') !!}</div>
                            @endif
                        
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form id="generateReviewLink" action="{{ route('generate.review-link') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{ csrf_field() }}
                                <div class="row p-b-18">                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="agentName">Agent Name</label>
                                            <input type="text" id="agentName" name="agent" class="form-control @error('agent') is-invalid @enderror" placeholder="Insert Agent Name" value="{{ old('agent') }}" required>
                                            @error('agent')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bookingCode">Booking Code</label>
                                            <input type="text" id="bookingCode" name="booking_code" style="text-transform: uppercase;" class="form-control @error('booking_code') is-invalid @enderror" placeholder="Insert Booking Code" value="{{ old('booking_code') }}" required>
                                            @error('booking_code')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="arrivalDate">Arrival Date </label>
                                            <input name="arrival_date" id="arrivalDate" wire:model="arrival_date" class="form-control date-picker @error('arrival_date') is-invalid @enderror" placeholder="Select Date and Time" type="text" required>
                                            @error('arrival_date')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="departureDate">Departure Date </label>
                                            <input name="departure_date" id="departureDate" wire:model="departure_date" class="form-control date-picker @error('departure_date') is-invalid @enderror" placeholder="Select Date and Time" type="text" required>
                                            @error('departure_date')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 m-b-18">
                                        <label for="jumlah_review" class="form-label">Number of Guests</label>
                                        <input type="number" class="form-control" id="jumlah_review" name="jumlah_review" value="{{ old('jumlah_review', 1) }}" min="1" required>
                                    </div>
                                </div>
                        
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="generateReviewLink" class="btn btn-primary"><i class="ion-plus-round"></i> Generate Link</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>
    @endcan
    <script>
        function searchByAgent() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchByAgent");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbLinkReview");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
    <script>
        function searchByBookingCode() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchByBookingCode");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbLinkReview");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link copied to clipboard!');
            }, function(err) {
                alert('Failed to copy link');
            });
        }
    </script>
@endsection
