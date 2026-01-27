<table id="spkArchived" class="table stripe dataTable no-footer">
    <thead class="table-light">
       <tr>
            <th>#</th>
            <th>Date</th>
            <th class="datatable-nosort" >Order Number</th>
            <th class="datatable-nosort">Vehicles - Driver</th>
            <th class="text-right datatable-nosort">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($spk_archives as $spk)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td data-order="{{ $spk->spk_date }}">
                    {{ date("m/d/y",strtotime($spk->spk_date)) }}
                </td>
                <td>
                    <b>{{ $spk->order_number }}</b>
                    <p><i>{{ $spk->type }} ({{ $spk->number_of_guests }} pax)</i></p>
                </td>
                <td>
                    {{ $spk->transport ? $spk->transport->brand." ".$spk->transport->name : 'N/A' }} - 
                    {{ $spk->driver ? $spk->driver->name : 'N/A' }}<br>
                    {{ $spk->plate_number ?? '-' }}
                </td>
                <td class="text-right">
                    <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#spkArchiveDetail{{ $spk->id }}"><i class="icon-copy dw dw-eye"></i> Detail</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No past spks found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div id="modalContainer">
    @foreach($spk_archives as $spk)
        <div class="modal fade" id="spkArchiveDetail{{ $spk->id }}" tabindex="-1" aria-labelledby="spkArchiveDetailLabel{{ $spk->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="card-box">
                        <div class="card-box-title" id="spkArchiveDetailLabel{{ $spk->id }}">
                            ({{ $spk->order_number }}) - {{ $spk->spk_number }}
                        </div>
                        <div class="card-box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-4"><p><strong>Date</strong></p></div>
                                        <div class="col-8"><p>: {{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y') }}</p></div>
                                        <div class="col-4"><p><strong>Type</strong></p></div>
                                        <div class="col-8"><p>: {{ $spk->type }}</p></div>
                                        <div class="col-4"><p><strong>Reserved by</strong></p></div>
                                        <div class="col-8"><p>: {{ $spk->operator?->name }}</p></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-4"><p><strong>Number of Guests</strong></p></div>
                                        <div class="col-8"><p>: {{ $spk->number_of_guests." guests" }}</p></div>
                                        <div class="col-4"><p><strong>Vehicle</strong></p></div>
                                        <div class="col-8"><p>: {{ $spk->transport?->brand." ".$spk->transport?->name }}</p></div>
                                        <div class="col-4"><p><strong>Driver</strong></p></div>
                                        <div class="col-8"><p>: {{ $spk->driver?->name }}</p></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @if ($spk->type == "Airport Shuttle")
                                <h6><strong>Airport Shuttle</strong></h6>
                                <ol>
                                    @forelse($spk->airport_shuttles as $airport_shuttle)
                                        <li>
                                            <p>
                                                {{ \Carbon\Carbon::parse($airport_shuttle->date)->locale('en')->translatedFormat('l, d M Y (H:i)') }} - 
                                                {{ $airport_shuttle->flight_number }} - 
                                                {{ $airport_shuttle->type == "In"?"Arrival":"Departure" }}
                                            </p>
                                        </li>
                                    @empty
                                        <p>No airport shuttle assigned.</p>
                                    @endforelse
                                </ol>
                                <hr>
                            @endif
                            <h6><strong>Guest Detail</strong></h6>
                            <ol>
                                @forelse($spk->guests as $guest)
                                    <li>
                                        <p>
                                            {{ $guest->name }} ({{ $guest->name_mandarin??"-" }})
                                        </p>
                                    </li>
                                @empty
                                    <p>No guest assigned.</p>
                                @endforelse
                            </ol>
                            <hr>
                            <h6><strong>Destinations</strong></h6>
                            <ol>
                                @forelse($spk->destinations as $destination)
                                    <li>
                                        <p>
                                            {{ \Carbon\Carbon::parse($destination->date)->locale('id')->translatedFormat('l, d M Y (H:i)') }} - 
                                            {{ $destination->destination_name }} - 
                                            @if ($destination->status == "Visited")
                                                <span style="color: rgb(18, 0, 180);">{{ $destination->status." on ".\Carbon\Carbon::parse($destination->visited_at)->locale('id')->translatedFormat('l, d M Y (H:i)') }}</span>
                                            @else
                                                <i style="color: red;">Expired</i>
                                            @endif
                                        </p>
                                    </li>
                                @empty
                                    <p>No destinations assigned.</p>
                                @endforelse
                            </ol>
                        </div>
                        <div class="card-box-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<!-- Pagination -->
{{-- <div class="d-flex justify-content-center">
    {{ $spk_archives->withQueryString()->links() }}
</div> --}}