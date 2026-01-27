<div class="row">
    <div class="col-md-6">
        <p><strong>SPK Status:</strong>
            <span class="badge {{ $spk->status == "In Progress"?"status-in-progress":"status-".$spk->status }}">{{ $spk->status }}</span>
        </p>
        <p><strong>SPK Number:</strong> {{ $spk->spk_number }}</p>
        {{-- <p><strong>SPK Date:</strong> {{ date('d F Y',strtotime($spk->spk_date)) }}</p> --}}
        <p><strong>Customer / Agent:</strong> {{ $spk->reservation->customer_name ?? '-' }}</p>
        
    </div>
    <div class="col-md-6">
        <p><strong>Driver:</strong> {{ $spk->driver->name ?? 'Not Assigned' }}</p>
        <p>
            <strong>Vehicle:</strong>
            <span>{{ $spk->transport->brand." ".$spk->transport->name }}</span>
            <span class="{{ isset($spk->transport->number_plate)? $spk->transport->number_plate: "notification" }}">
                ({{ $spk->transport->number_plate??"-"; }})
            </span>
        </p>
    </div>
</div>
<hr>
<h5><strong>Vehicle Itinerary</strong></h5>
<p>{{ date('l, d M Y',strtotime($spk->spk_date)) }}</p>
<div class="row">
    @if ($spk->destinations->count())
        @php
            $grouped = $spk->destinations->sortBy('date')->groupBy(function($dest) {
                return \Carbon\Carbon::parse($dest->date)->format('l, d M Y');
            });
        @endphp
        @foreach($grouped as $day => $items)
            <div class="col-md-12">
                <div class="timeline-horizontal">
                    @foreach($items as $i=>$dest)
                        @if($i < $items->count() - 1)
                            @if(isset($items[$i+1]))
                                @php
                                    $next = $items[$i+1];
                                    $distance = app('App\Http\Controllers\MapController')->getDistance(
                                        $dest->latitude,
                                        $dest->longitude,
                                        $next->latitude,
                                        $next->longitude
                                    );
                                @endphp
                            @endif
                        @endif
                        <div class="timeline-item {{ $loop->last ? 'last' : '' }}">
                            <div class="timeline-icon">
                                {{ ++$i }}
                            </div>
                            <div class="timeline-content">
                                <span class="fw-bold text-primary">
                                    <a href="{{ $dest->destination_address }}" target="_blank">
                                        {{ $dest->destination_name }}
                                    </a>
                                </span>
                                <div class="text-small">
                                    <p>
                                        <b>
                                            ({{ strtoupper(\Carbon\Carbon::parse($dest->date)->format('H:i a')) }})
                                        </b>
                                    </p>
                                    @if($i < $items->count())
                                        <small>
                                            To {{ $next->destination_name }}: {{ $distance['distance'] ?? '-' }}<br>
                                            Duration: {{ $distance['duration'] ?? '-' }}
                                        </small>
                                    @else
                                    <small>
                                        Trips end at {{ $dest->destination_name }}
                                    </small>
                                    @endif
                                    
                                    @if($dest->notes)
                                        <br><em>{{ $dest->notes }}</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
             <div class="col-md-12">
                <hr>
             </div>
        @endforeach
    @else
        <div class="col-md-12">
            <p>
                <i class="notification">
                    {{-- Destination / Tourist Attraction not available, please add one! --}}
                    Destinasi wisata / Tempat Wisata belum tersedia, silahkan tambahkan satu atau lebih!
                </i>
            </p>
            
        </div>
    @endif
</div>
