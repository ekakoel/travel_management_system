 {{-- CEREMONY VENUE --}}
 <div id="ceremonyVenue" class="card-box">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fa fa-institution" aria-hidden="true"></i> Ceremony Venue</div>
    </div>
    @if (count($ceremonyVenues)>0)
        <div class="card-box-content m-b-8">
            @foreach ($ceremonyVenues as $ceremony_venue)
            @php
                $wvs = json_decode($ceremony_venue->slot);
                $wvbp = json_decode($ceremony_venue->basic_price);
                $wvap = json_decode($ceremony_venue->arrangement_price);
                $ceremony_venue_slot = implode(' | ',$wvs);
                $ceremony_venue_price = implode(' | ',$wvs);
            @endphp
                <div class="card">
                    <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $ceremony_venue->id }}">
                        <div class="card-image-container">
                            <div class="card-status">
                                @if ($ceremony_venue->status == "Rejected")
                                    <div class="status-rejected"></div>
                                @elseif ($ceremony_venue->status == "Invalid")
                                    <div class="status-invalid"></div>
                                @elseif ($ceremony_venue->status == "Active")
                                    <div class="status-active"></div>
                                @elseif ($ceremony_venue->status == "Waiting")
                                    <div class="status-waiting"></div>
                                @elseif ($ceremony_venue->status == "Draft")
                                    <div class="status-draft"></div>
                                @elseif ($ceremony_venue->status == "Archived")
                                    <div class="status-archived"></div>
                                @else
                                @endif
                            </div>
                            @if ($ceremony_venue->status == "Draft")
                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}">
                            @else
                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}">
                            @endif
                            <div class="card-price-container">
                                <div class="card-price-full">
                                    @foreach ($wvs as $slot_venue)
                                        | {{ date('H:i',strtotime($slot_venue)) }}
                                    @endforeach
                                    |
                                </div>
                            </div>
                            <div class="card-period-full">
                                {{ date('d M Y',strtotime($ceremony_venue->periode_start)) }} - {{ date('d M Y',strtotime($ceremony_venue->periode_end)) }}
                            </div> 
                            <div class="name-card">
                                <p>
                                    <b>{{ $ceremony_venue->name }}</b><br>
                                </p>
                            </div>
                        </div>
                    </a>
                    @canany(['posDev','posAuthor'])
                        <div class="card-btn-container">
                            @if ($ceremony_venue->status == "Draft")
                                <a href="/edit-wedding-venue-{{ $ceremony_venue->id }}">
                                    <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button><br>
                                    {{-- <button type="button" class="btn btn-update"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button> --}}
                                </a>
                            @endif
                            <form action="/fdelete-wedding-venue/{{ $ceremony_venue->id }}" method="post">
                                @csrf
                                @method('delete')
                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                            </form>
                        </div>
                    @endcanany
                </div>
                {{-- MODAL CEREMONY VENUE DETAIL --}}
                <div class="modal fade" id="detail-wedding-venue-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-institution" aria-hidden="true"></i> Wedding Venue</div>
                                    <div class="status-card">
                                        @if ($ceremony_venue->status == "Rejected")
                                            <div class="status-rejected"></div>
                                        @elseif ($ceremony_venue->status == "Invalid")
                                            <div class="status-invalid"></div>
                                        @elseif ($ceremony_venue->status == "Active")
                                            <div class="status-active"></div>
                                        @elseif ($ceremony_venue->status == "Waiting")
                                            <div class="status-waiting"></div>
                                        @elseif ($ceremony_venue->status == "Draft")
                                            <div class="status-draft"></div>
                                        @elseif ($ceremony_venue->status == "Archived")
                                            <div class="status-archived"></div>
                                        @else
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-banner">
                                            <img src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}" loading="lazy">
                                        </div>
                                        <div class="card-text">
                                            <div class="card-ptext-margin">
                                                <div class="row ">
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Venue</div>
                                                        <p>{{ $ceremony_venue->name }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Capacity</div>
                                                        <p>{{ $ceremony_venue->capacity. " Guest" }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Period</div>
                                                        <p>{{ date('d M Y',strtotime($ceremony_venue->periode_start)) }} - 
                                                        {{ date('d M Y',strtotime($ceremony_venue->periode_end)) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if ($ceremony_venue->description != "")
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-12 col-sm-12">
                                                        <div class="tab-inner-title-light">
                                                            Description
                                                        </div>
                                                        {!! $ceremony_venue->description !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($ceremony_venue->term_and_condition != "")
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-12 col-sm-12">
                                                        <div class="tab-inner-title-light">
                                                            Terms and Conditions
                                                        </div>
                                                        {!! $ceremony_venue->term_and_condition !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-12 col-sm-12">
                                                    <div class="tab-inner-title-light">
                                                        Price
                                                    </div>
                                                    <div class="card-ptext-margin">
                                                        <table class="w-100">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Slot</th>
                                                                    <th>Basic Price</th>
                                                                    <th>Arrangement Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if ($wvs)
                                                                    @php
                                                                        $cs = count($wvs);
                                                                    @endphp
                                                                    @for ($s = 0; $s < $cs; $s++)
                                                                        <tr>
                                                                            <td>{{ $s+1 }}</td>
                                                                            <td>
                                                                                {{ date('H:i',strtotime($wvs[$s])) }}
                                                                            </td>
                                                                            <td>
                                                                                {{ '$ ' . number_format(($wvbp[$s]), 0, ',', '.') }}
                                                                            </td>
                                                                            <td>
                                                                                {{ '$ ' . number_format(($wvap[$s]), 0, ',', '.') }}
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    @canany(['posDev','posAuthor'])
                                        <form id="activateCeremonyVenue-{{ $ceremony_venue->id }}" action="/factivate-ceremony-venue/{{ $ceremony_venue->id }}" method="post" enctype="multipart/form-data" >
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <form id="deactivateCeremonyVenue-{{ $ceremony_venue->id }}" action="/fdeactivate-ceremony-venue/{{ $ceremony_venue->id }}" method="post" enctype="multipart/form-data" >
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        @if ($ceremony_venue->status == "Draft")
                                            <button type="submit" form="activateCeremonyVenue-{{ $ceremony_venue->id }}" class="btn btn-info"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Activate</button>
                                            <a href="/edit-wedding-venue-{{ $ceremony_venue->id }}">
                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                {{-- <button type="button" class="btn btn-update"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button> --}}
                                            </a>
                                        @elseif ($ceremony_venue->status == "Active")
                                            <button type="submit" form="deactivateCeremonyVenue-{{ $ceremony_venue->id }}" class="btn btn-dark"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save to Draft</button>
                                        @endif
                                        
                                    @endcanany
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
    <div class="notification">
        <p>Wedding venue not found, please add one, first so you can add wedding package!</p>
    </div>
    @endif
    <div class="card-box-footer">
        <a href="add-ceremony-venue-{{ $hotel->id }}">
            <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> Add</button>
        </a>
    </div>
</div>