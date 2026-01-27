<table class="data-table table stripe dataTable no-footer dtr-inline">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Booking Code</th>
            <th>Check-in & Check-out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reservation_archives as $index => $reservation)
            <tr>
                <td>{{ $index+1 }}</td>
                <td>{{ $reservation->rsv_no }}</td>
                <td>
                    @if (isset($reservation->checkin) && isset($reservation->checkout))
                        {{ date('d M Y',strtotime($reservation->checkin)) }} - {{ date('d M Y',strtotime($reservation->checkout)) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $reservation->status }}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal"
                            data-target="#reservationArchiveDetail{{ $reservation->id }}">
                        View Detail
                    </button>
                </td>
            </tr>

            <!-- Modal Detail -->
            <div class="modal fade" id="reservationArchiveDetail{{ $reservation->id }}" tabindex="-1"
                    aria-labelledby="reservationArchiveDetailLabel{{ $reservation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title" id="reservationArchiveDetailLabel{{ $reservation->id }}">
                                Reservation Detail - {{ $reservation->rsv_no }}
                            </div>
                            <div class="card-box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-6"><p><strong>Reservation Number</strong></p></div>
                                            <div class="col-6"><p>: {{ $reservation->rsv_no }}</p></div>
                                            <div class="col-6"><p><strong>Customer / Agent</strong></p></div>
                                            <div class="col-6"><p>: {{ $reservation->customer_name ?? '-' }}</p></div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-6"><p><strong>Checkin</strong></p></div>
                                            <div class="col-6"><p>: {{ date('l, d M Y',strtotime($reservation->checkin)) }}</p></div>
                                            <div class="col-6"><p><strong>Checkout</strong></p></div>
                                            <div class="col-6"><p>: {{ date('l, d M Y',strtotime($reservation->checkout)) }}</p></div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h6><strong>Surat Perintah Kerja (SPK)</strong></h6>
                                    @forelse ($reservation->spks as $no_spk=>$spk)
                                        <div class="bordered row">
                                            <div class="col-6">
                                                <p><b>SPK Number:</b> {{ $spk->spk_number }}</p>
                                                <p><b>Date:</b> {{ date('l, d F Y',strtotime($spk->spk_date)) }}</p>
                                                <p><b>Number of Guests:</b> {{ $spk->number_of_guests." guests" }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p><b>Vehicle:</b> {{ $spk->transport?->brand." ".$spk->transport?->name." (".$spk->transport?->number_plate.")" }}</p>
                                                <p><b>Driver:</b> {{ $spk->driver?->name }}</p>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12 p-l-27">
                                                <p><b>{{ $spk->type }} Destination</b></p>
                                            </div>
                                            @foreach ($spk->destinations as $dst)
                                                <div class="col-12 p-l-27">
                                                    <p>- ({{ date('H:i',strtotime($dst->date)) }}) <b>{{ $dst->destination_name }}</b> {{ $dst->description?"-> ".$dst->description:"" }} -> {{ $dst->status }} {{ isset($dst->visited_at) ? date("d M Y (H:i)",strtotime($dst->visited_at)):"" }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @empty
                                        
                                    @endforelse
                                {{-- <p>{{ date('l, d M Y',strtotime($spk->spk_date)) }}</p> --}}
                                {{-- <div class="row">
                                    <div class="col-sm-4">Booking Code</div>
                                    <div class="col-sm-8">{{ $reservation->rsv_no }}</div>

                                    <div class="col-sm-4">Guest Name</div>
                                    <div class="col-sm-8">{{ $reservation->guest_name }}</div>

                                    <div class="col-sm-4">Service</div>
                                    <div class="col-sm-8">{{ $reservation->service }}</div>

                                    <div class="col-sm-4">Check-in</div>
                                    <div class="col-sm-8">{{ $reservation->checkin }}</div>

                                    <div class="col-sm-4">Check-out</div>
                                    <div class="col-sm-8">{{ $reservation->checkout }}</div>

                                    <div class="col-sm-4">Guests</div>
                                    <div class="col-sm-8">
                                        @foreach($reservation->spks as $spk)
                                            • {{ $spk->number }} ({{ $spk->type }})<br>
                                        @endforeach
                                    </div>

                                    <div class="col-sm-4">Notes</div>
                                    <div class="col-sm-8">{{ $reservation->notes ?? '-' }}</div>
                                </div> --}}
                            </div>
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="5" class="text-center">No past reservations found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $reservation_archives->withQueryString()->links() }}
</div>