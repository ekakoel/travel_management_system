 {{-- CEREMONY VENUE --}}
 <div id="ceremonyVenueDecorations" class="card-box">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy dw dw-flower"></i> Ceremony Venue Decorations</div>
    </div>
    @if (count($ceremonyVenueDecorations)>0)
        <div class="card-box-content m-b-8">
            @foreach ($ceremonyVenueDecorations as $ceremony_venue_decoration)
            
                <div class="card">
                    <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $ceremony_venue_decoration->id }}">
                        <div class="card-image-container">
                            <div class="card-status">
                                @if ($ceremony_venue_decoration->status == "Rejected")
                                    <div class="status-rejected"></div>
                                @elseif ($ceremony_venue_decoration->status == "Invalid")
                                    <div class="status-invalid"></div>
                                @elseif ($ceremony_venue_decoration->status == "Active")
                                    <div class="status-active"></div>
                                @elseif ($ceremony_venue_decoration->status == "Waiting")
                                    <div class="status-waiting"></div>
                                @elseif ($ceremony_venue_decoration->status == "Draft")
                                    <div class="status-draft"></div>
                                @elseif ($ceremony_venue_decoration->status == "Archived")
                                    <div class="status-archived"></div>
                                @else
                                @endif
                            </div>
                            @if ($ceremony_venue_decoration->status == "Draft")
                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/hotels/weddings/decorations/' . $ceremony_venue_decoration->cover) }}" alt="{{ $ceremony_venue_decoration->name }}">
                            @else
                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/weddings/decorations/' . $ceremony_venue_decoration->cover) }}" alt="{{ $ceremony_venue_decoration->name }}">
                            @endif
                            <div class="card-price-container">
                                <div class="card-price-full">
                                    {{ $ceremony_venue_decoration->name }}
                                </div>
                            </div>
                            <div class="name-card">
                                <p>
                                    <b>{{ $ceremony_venue_decoration->name }}</b><br>
                                    {{ $ceremony_venue_decoration->capacity." guests" }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @canany(['posDev','posAuthor'])
                        <div class="card-btn-container">
                            @if ($ceremony_venue_decoration->status == "Draft")
                                <a href="/edit-decoration-ceremony-venue-{{ $ceremony_venue_decoration->id }}">
                                    <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button><br>
                                </a>
                            @endif
                            <form action="/fdelete-decoration-ceremony-venue/{{ $ceremony_venue_decoration->id }}" method="post">
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
                <div class="modal fade" id="detail-wedding-venue-{{ $ceremony_venue_decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy dw dw-flower"></i> Wedding Venue Decoration</div>
                                    <div class="status-card">
                                        @if ($ceremony_venue_decoration->status == "Rejected")
                                            <div class="status-rejected"></div>
                                        @elseif ($ceremony_venue_decoration->status == "Invalid")
                                            <div class="status-invalid"></div>
                                        @elseif ($ceremony_venue_decoration->status == "Active")
                                            <div class="status-active"></div>
                                        @elseif ($ceremony_venue_decoration->status == "Waiting")
                                            <div class="status-waiting"></div>
                                        @elseif ($ceremony_venue_decoration->status == "Draft")
                                            <div class="status-draft"></div>
                                        @elseif ($ceremony_venue_decoration->status == "Archived")
                                            <div class="status-archived"></div>
                                        @else
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-banner">
                                            <img src="{{ asset ('storage/hotels/weddings/decorations/'.$ceremony_venue_decoration->cover) }}" alt="{{ $ceremony_venue_decoration->name }}" loading="lazy">
                                        </div>
                                        <div class="card-text">
                                            <div class="card-ptext-margin">
                                                <div class="row ">
                                                    <div class="col-6 col-sm-6">
                                                        <div class="card-subtitle">Name</div>
                                                        <p>{{ $ceremony_venue_decoration->name }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-6">
                                                        <div class="card-subtitle">Capacity</div>
                                                        <p>{{ $ceremony_venue_decoration->capacity. " Guest" }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-6">
                                                        <div class="card-subtitle">Duration</div>
                                                        <p>{{ $ceremony_venue_decoration->duration. " hours" }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-6">
                                                        <div class="card-subtitle">Price</div>
                                                        <div class="usd-rate">
                                                            {{ '$ ' . number_format( $ceremony_venue_decoration->price, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($ceremony_venue_decoration->description != "")
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-12 col-sm-12">
                                                        <div class="tab-inner-title-light">
                                                            Description
                                                        </div>
                                                        <p>{!! $ceremony_venue_decoration->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($ceremony_venue_decoration->terms_and_conditions)
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-12 col-sm-12">
                                                        <div class="tab-inner-title-light">
                                                            Terms and Conditions
                                                        </div>
                                                        <p>{!! $ceremony_venue_decoration->terms_and_conditions !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    @canany(['posDev','posAuthor'])
                                        <form id="activateDecorationCeremonyVenue-{{ $ceremony_venue_decoration->id }}" action="/fsave-to-active-decoration-ceremony-venue-{{ $ceremony_venue_decoration->id }}" method="post" enctype="multipart/form-data" >
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <form id="deactivateDecorationCeremonyVenue-{{ $ceremony_venue_decoration->id }}" action="/fsave-to-draft-decoration-ceremony-venue-{{ $ceremony_venue_decoration->id }}" method="post" enctype="multipart/form-data" >
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        @if ($ceremony_venue_decoration->status == "Draft")
                                            <button type="submit" form="activateDecorationCeremonyVenue-{{ $ceremony_venue_decoration->id }}" class="btn btn-info"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Activate</button>
                                            <a href="/edit-decoration-ceremony-venue-{{ $ceremony_venue_decoration->id }}">
                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                {{-- <button type="button" class="btn btn-update"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button> --}}
                                            </a>
                                        @elseif ($ceremony_venue_decoration->status == "Active")
                                            <button type="submit" form="deactivateDecorationCeremonyVenue-{{ $ceremony_venue_decoration->id }}" class="btn btn-dark"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save to Draft</button>
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
        <p>Ceremony venue decoration not found, please add one!</p>
    </div>
    @endif
    <div class="card-box-footer">
        <a href="add-decoration-ceremony-venue-{{ $hotel->id }}">
            <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> Add</button>
        </a>
    </div>
</div>