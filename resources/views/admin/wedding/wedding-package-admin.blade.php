{{-- WEDDING PACKAGE --}}
@if (count($ceremonyVenues)>0 && count($receptionVenues)>0)
    <div id="weddingPackages" class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="icon-copy fa fa-cubes" aria-hidden="true"></i> Wedding Packages {{ count($weddingPackages)>0 }}</div>
        </div>
        @if (count($ceremonyVenues)>0 && count($receptionVenues)>0)
            <div class="card-box-content">
                @foreach ($weddingPackages as $wedding_package)
                    @php
                        $weddingVenue = $ceremonyVenues->where('id',$wedding_package->ceremony_venue_id)->first();
                        $reception_package = $wedding_package->reception_package;
                        if ($wedding_package->slot) {
                                $wdt = json_decode($wedding_package->slot);
                                $arrangements = json_decode($wedding_package->arrangement_price);
                                $basics = json_decode($wedding_package->basic_price);
                        }else{
                                $wdt = NULL;
                                $weddingTimes = NULL;
                        }
                        if ($wdt) {
                                $slot = implode(' | ',$wdt);
                        }else {
                                $slot = NULL;
                        }
                                                
                    @endphp
                        <div class="card">
                            <a href="#" data-toggle="modal" data-target="#detail-wedding-package-{{ $wedding_package->id }}">
                                <div class="card-image-container">
                                    <div class="card-status">
                                        @if ($wedding_package->status == "Rejected")
                                            <div class="status-rejected"></div>
                                        @elseif ($wedding_package->status == "Invalid")
                                            <div class="status-invalid"></div>
                                        @elseif ($wedding_package->status == "Active")
                                            <div class="status-active"></div>
                                        @elseif ($wedding_package->status == "Waiting")
                                            <div class="status-waiting"></div>
                                        @elseif ($wedding_package->status == "Draft")
                                            <div class="status-draft"></div>
                                        @elseif ($wedding_package->status == "Archived")
                                            <div class="status-archived"></div>
                                        @else
                                        @endif
                                    </div>
                                    
                                    @if ($wedding_package->status == "Draft")
                                        <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}">
                                    @else
                                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}">
                                    @endif
                                    
                                    <div class="card-price-container">
                                        <div class="card-price-full">
                                            <i class="icon-copy fa fa-user" aria-hidden="true"></i> {{ $wedding_package->capacity }} Invitations
                                        </div>
                                    </div>
                                    
                                    <div class="name-card">
                                        <b>
                                            {{ $wedding_package->name }}
                                        </b>
                                    </div>
                                </div>
                            </a>
                            @canany(['posDev','posAuthor'])
                                <div class="card-btn-container">
                                    @if ($wedding_package->status == "Draft")
                                        <a href="/edit-wedding-package-{{ $wedding_package->id }}">
                                            <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button><br>
                                            {{-- <button type="button" class="btn btn-update"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button> --}}
                                        </a>
                                    @endif
                                    <form action="/fdelete-wedding-package/{{ $wedding_package->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                </div>
                            @endcanany
                        </div>
                        {{-- MODAL WEDDING PACKAGE DETAIL --}}
                        <div class="modal fade" id="detail-wedding-package-{{ $wedding_package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-institution" aria-hidden="true"></i> {{ $wedding_package->name }}</div>
                                            <div class="status-card">
                                                @if ($wedding_package->status == "Rejected")
                                                    <div class="status-rejected"></div>
                                                @elseif ($wedding_package->status == "Invalid")
                                                    <div class="status-invalid"></div>
                                                @elseif ($wedding_package->status == "Active")
                                                    <div class="status-active"></div>
                                                @elseif ($wedding_package->status == "Waiting")
                                                    <div class="status-waiting"></div>
                                                @elseif ($wedding_package->status == "Draft")
                                                    <div class="status-draft"></div>
                                                @elseif ($wedding_package->status == "Archived")
                                                    <div class="status-archived"></div>
                                                @else
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            @php
                                                $weekDayPrice = floatval($wedding_package->week_day_price);
                                                $holidayPrice = floatval($wedding_package->holiday_price);
                                            @endphp
                                            <div class="col-md-12">
                                                <div class="modal-img-container">
                                                    <img src="{{ asset ('storage/weddings/wedding-cover/' . $wedding_package->cover) }}" alt="{{ $wedding_package->name }}" loading="lazy">
                                                </div>
                                                <div class="card-text">
                                                    <div class="card-ptext-margin">
                                                        <div class="row ">
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Package</div>
                                                            <p>{{ $wedding_package->name }}</p>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Slot</div>
                                                            <p>{{ $slot }}</p>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Duration</div>
                                                            <p>{{ $wedding_package->duration }} nights</p>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Capacity</div>
                                                            <p>{{ $wedding_package->capacity. " Invitations" }}</p>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Period Start</div>
                                                            <p>{{ dateFormat($wedding_package->period_start) }}</p>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Period End</div>
                                                            <p>{{ dateFormat($wedding_package->period_end) }}</p>
                                                        </div>
                                                        
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Week Day Price</div>
                                                            <div class="usd-rate">{{ '$ ' . number_format($weekDayPrice, 0, ',', '.') }}</div>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Holiday Price</div>
                                                            <div class="usd-rate">{{ '$ ' . number_format($holidayPrice, 0, ',', '.') }}</div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="row ">
                                                    @if ($wedding_package->hotels or $wedding_package->suite_and_villa or $weddingVenue or $wedding_package->ceremony_venue_decoration or $wedding_package->reception_package->reception_venue or $wedding_package->reception_venue_decoration or $wedding_package->bride_transport_id)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Services
                                                            </div>
                                                            <div class="row">
                                                                @if ($wedding_package->hotels)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Wedding Venue</div>
                                                                            <p>{{ $wedding_package->hotels->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->suite_and_villa)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Suite / Villa</div>
                                                                            <p>{{ $wedding_package->suite_and_villa->rooms }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($weddingVenue)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Ceremony Venue</div>
                                                                            <p>{{ $weddingVenue->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->ceremony_venue_decoration)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Ceremony Venue Decoration</div>
                                                                            <p>{{ $wedding_package->ceremony_venue_decoration->service }}</p>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Ceremony Venue Decoration</div>
                                                                            <p>Standar Decoration</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($receptionVenue)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Reception Venue</div>
                                                                            <p>{{ $receptionVenue->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->reception_venue_decoration)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Reception Venue Decoration</div>
                                                                            <p>{{ $wedding_package->reception_venue_decoration->service }}</p>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Reception Venue Decoration</div>
                                                                            <p>Standar Decoration</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->lunch_venue)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Lunch Venue</div>
                                                                            <p>{{ $wedding_package->lunch_venue->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->dinner_venue)
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="card-ptext-margin">
                                                                            <div class="card-subtitle">Dinner Venue</div>
                                                                            <p>{{ $wedding_package->dinner_venue->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if ($wedding_package->transport_id)
                                                                    @php
                                                                        $wedding_transport = $transports->where('id',$wedding_package->transport_id)->first();
                                                                    @endphp
                                                                    @if ($wedding_transport)
                                                                        <div class="col-6 col-md-6">
                                                                            <div class="card-ptext-margin">
                                                                                <div class="card-subtitle">Transports</div>
                                                                                <p>
                                                                                    {{ $wedding_transport->brand." - ".$wedding_transport->name }}<br>
                                                                                    <i>Arrival & Departure</i>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($wedding_package->additional_service_id)
                                                        @php
                                                            $addser_id = json_decode($wedding_package->additional_service_id);
                                                            if ($addser_id) {
                                                                $c_addser = count($addser_id);
                                                            }else{
                                                                $c_addser = 0;
                                                            }
                                                        @endphp
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Additional Services
                                                            </div>
                                                            <div class="card-ptext-margin">
                                                                <ul>
                                                                    @for ($j = 0; $j < $c_addser; $j++)
                                                                        @php
                                                                            $additional_service = $additionalServices->where('id',$addser_id[$j])->first();
                                                                        @endphp
                                                                        @if ($additional_service)
                                                                            <li>{{ $additional_service->service }}</li>
                                                                        @endif
                                                                    @endfor
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($wedding_package->include)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Include
                                                            </div>
                                                            <div class="card-ptext-margin">
                                                                <p>{!! $wedding_package->include !!}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($wedding_package->payment_process)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Payment Proccess
                                                            </div>
                                                            <div class="card-ptext-margin">
                                                                <p>{!! $wedding_package->payment_process !!}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($wedding_package->cancellation_policy)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Cancellation Policy
                                                            </div>
                                                                <div class="card-ptext-margin">
                                                                    <p>{!! $wedding_package->cancellation_policy !!}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @if ($wedding_package->additional_info)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">Additional Information</div>
                                                            <div class="card-ptext-margin">
                                                                <p>{!! $wedding_package->additional_info !!}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($wedding_package->terms_and_conditions)
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">Terms and Conditions</div>
                                                            <div class="card-ptext-margin">
                                                                <p>{!! $wedding_package->terms_and_conditions !!}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            @if ($wedding_package->status == "Draft")
                                                @canany(['posDev','posAuthor'])
                                                    <a href="/edit-wedding-package-{{ $wedding_package->id }}">
                                                        <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                    </a>
                                                @endcanany
                                            @elseif ($wedding_package->status == "Active")
                                                <form id="draftWeddingPackage-{{ $wedding_package->id }}" action="/fdraft-wedding-package/{{ $wedding_package->id }}" method="post" enctype="multipart/form-data" >
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <button type="submit" form="draftWeddingPackage-{{ $wedding_package->id }}" class="btn btn-dark"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save to Draft</button>
                                            @endif
                                            
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
        @endif
        <div class="card-box-footer">
            <a href="/add-wedding-package-{{ $hotel->id }}">
                <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> Add</button>
            </a>
        </div>
    </div>
@endif