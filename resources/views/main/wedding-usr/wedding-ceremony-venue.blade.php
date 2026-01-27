 {{-- CEREMONY VENUE --}}
 <div id="ceremonyVenue" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="fa fa-bank" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
    </div>
    @if (count($weddingVenues)>0)
    <div class="card-box-content">
        @foreach ($weddingVenues as $index => $ceremony_venue)
            @php
                $wvs = json_decode($ceremony_venue->slot);
                $weddingCeremonySlots = implode(' | ',$wvs);
                $slots = json_decode($ceremony_venue->slot);
                $basic_price = json_decode($ceremony_venue->basic_price);
                $arrangement_price = json_decode($ceremony_venue->arrangement_price);
                $cslot = count($slots);
            @endphp
            <div class="card" role="tabpanel">
                <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $ceremony_venue->id }}">
                    <div class="card-image-container">
                        <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}">
                        <div class="card-lable-right">
                            <div class="meta-box">
                                <p>
                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $ceremony_venue->capacity }}
                                </p>
                            </div>
                        </div>
                        <div class="card-price-container">
                            <div class="card-price-full">
                                {{ $weddingCeremonySlots }}
                            </div>
                        </div>
                        <div class="name-card">
                            <b>{{ $ceremony_venue->name }}</b>
                        </div>
                    </div>
                </a>
                <div class="card-button-container text-right">
                    <a href="#" data-toggle="modal" data-target="#order-wedding-venue-{{ $ceremony_venue->id }}">
                        <button class="btn card-button"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang("messages.Order")</button>
                    </a>
                </div>
            </div>
            {{-- MODAL DETAIL CEREMONY VENUE --}}
            <div class="modal fade" id="detail-wedding-venue-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title"><i class="icon-copy fa fa-bank" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="modal-img-container">
                                        <img src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremony_venue->cover) }}" alt="{{ $ceremony_venue->name }}">
                                        <div class="modal-service-name">
                                            {{ $ceremony_venue->name }}
                                            <p>{{ $ceremony_venue->capacity }} @lang('messages.Invitations')</p>
                                        </div>
                                    </div>
                                </div>
                                    
                                @if ($ceremony_venue->description)
                                    <div class="col-sm-12">
                                        <div class="tab-inner-title-light">@lang('messages.Description')</div>
                                        <div class="card-ptext-margin m-b-8">
                                            {!! $ceremony_venue->description !!}
                                        </div>
                                    </div>
                                @endif
                                
                                @if ($ceremony_venue->term_and_condition)
                                    <div class="col-sm-12">
                                        <div class="tab-inner-title-light">@lang('messages.Terms and Conditions')</div>
                                        <div class="card-ptext-margin m-b-8">
                                            {!! $ceremony_venue->term_and_condition !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    
                                    <div class="tab-inner-title-light">@lang('messages.Rate')</div>
                                    <div class="card-ptext-margin">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="card-subtitle">@lang('messages.Slot')</div>
                                                <hr class="form-hr">
                                                <div class="rate-text">
                                                    @for ($sl = 0; $sl < $cslot; $sl++)
                                                       {{ date("H.i",strtotime($slots[$sl])) }}<br>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="col-4 text-right">
                                                <div class="card-subtitle">@lang('messages.Basic')</div>
                                                <hr class="form-hr">
                                                <div class="usd-rate">
                                                    @for ($prs = 0; $prs < $cslot; $prs++)
                                                    {{ currencyFormatUsd($basic_price[$prs]) }} <br>
                                                    @endfor
                                                    
                                                </div>
                                            </div>
                                            <div class="col-4 text-right">
                                                <div class="card-subtitle">@lang('messages.Arrangement')</div>
                                                <hr class="form-hr">
                                                <div class="usd-rate">
                                                    @for ($arpr = 0; $arpr < $cslot; $arpr++)
                                                    {{ currencyFormatUsd($arrangement_price[$arpr]) }} <br>
                                                    @endfor
                                                    
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL ORDER CEREMONY VENUE --}}
            <div class="modal fade" id="order-wedding-venue-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title"><i class="icon-copy fa fa-bank" aria-hidden="true"></i> {{ $ceremony_venue->name }}</div>
                            </div>
                            <form id="orderWeddingVenue-{{ $ceremony_venue->id }}" action="/forder-wedding-venue-{{ $ceremony_venue->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    {{-- Admin create order ================================================================= --}}
                                    @canany(['weddingRsv','weddingDvl','weddingSls','weddingAuthor'])
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="user_id">Select Agent <span>*</span></label>
                                                <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                    <option selected value="">Select Agent</option>
                                                    @foreach ($agents as $agent)
                                                        <option class="option-list" value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endcanany
                                    {{-- Admin create order ================================================================= --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="groom_name">@lang("messages.Groom's Name") <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-torso"></i></span>
                                                <input type="text" name="groom_name" id="groom_name" class="form-control input-icon @error('groom_name') is-invalid @enderror" placeholder="@lang("messages.Groom's Name")" value="{{ old('groom_name') }}" required>
                                            </div>
                                            @error('groom_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="groom_id">@lang("messages.ID") / @lang('messages.Passport') <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i></span>
                                                <input type="text" name="groom_id" class="form-control input-icon @error('groom_id') is-invalid @enderror" placeholder="@lang("messages.ID") / @lang("messages.Passport") @lang("messages.number")"  value="{{ old('groom_id') }}" required>
                                            </div>
                                            @error('groom_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bride_name">@lang("messages.Bride's Name") <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-torso-female"></i></span>
                                                <input type="text" name="bride_name" id="bride_name" class="form-control input-icon @error('bride_name') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" value="{{ old('bride_name') }}" required>
                                            </div>
                                            @error('bride_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brides_id">@lang("messages.ID") / @lang('messages.Passport') <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i></span>
                                                <input type="text" name="brides_id" class="form-control input-icon @error('brides_id') is-invalid @enderror" placeholder="@lang("messages.ID") / @lang("messages.Passport") @lang("messages.number")"  value="{{ old('brides_id') }}" required>
                                            </div>
                                            @error('brides_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                <input readonly name="wedding_date" type="text" id="wedding-date-{{ $index }}" class="wedding-date form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('wedding_date') }}" required>
                                            </div>
                                            @error('wedding_date')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="slot">@lang("messages.Select Slot") <span>*</span></label>
                                            <select class="custom-select input-icon selectInput @error('slot') is-invalid @enderror"
                                                id="slot_{{ $index }}"
                                                data-price-id="price_{{ $index }}"
                                                data-hidden-input-id="hidden_price_{{ $index }}"
                                                name="slot" 
                                                required>
                                                <option selected value="">@lang('messages.Select Slot')</option>
                                                @for ($i = 0; $i < $cslot; $i++)
                                                    <option value="{{ $slots[$i] }}" data-basic-price={{ $basic_price[$i] }}> {{ date('H:i',strtotime($slots[$i])) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="number_of_invitations">@lang('messages.Number of Invitations') <span> *</span></label>
                                            <div class="btn-icon">
                                                <span><i class="icon-copy fi-torsos-all"></i></span>
                                                <input name="number_of_invitations" type="number" min="1" max="{{ $ceremony_venue->capacity }}" id="number_of_invitations" class="form-control input-icon @error('number_of_invitations') is-invalid @enderror" placeholder="@lang('messages.Maximum') {{ $ceremony_venue->capacity }} @lang('messages.Invitations')" type="text" value="{{ old('number_of_invitations') }}" required>
                                            </div>
                                            @error('number_of_invitations')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <hr class="form-hr">
                                        <div class="modal-show-price-container">
                                            <div class="modal-show-price">
                                                @lang('messages.Price'): <span class="outputBasicSpan" id="price_{{ $index }}">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="ceremony_price" id="hidden_price_{{ $index }}" value="0">
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="orderWeddingVenue-{{ $ceremony_venue->id }}" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket"></i> @lang("messages.Order")</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL ADD CEREMONY VENUE TO WEDDING PLANNER --}}
            @if (count($wedding_planner_ceremonial_venue_none)>0)
                <div class="modal fade" id="add-ceremonial-venue-to-wedding-planner-{{ $ceremony_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-bank" aria-hidden="true"></i> {{ $ceremony_venue->name }}</div>
                                </div>
                                <form id="addCeremonialVenueToWeddingPlanner-{{ $ceremony_venue->id }}" action="/fadd-ceremonial-venue-to-wedding-planner/{{ $ceremony_venue->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label for="wedding_planner_id">@lang("messages.Select Wedding Planner") <span>*</span></label>
                                                <select class="custom-select input-icon @error('wedding_planner') is-invalid @enderror" name="wedding_planner_id" required>
                                                    <option selected value="">@lang('messages.Wedding Planner')</option>
                                                    @foreach ($wedding_planner_ceremonial_venue_none as $wpcvn)
                                                    @php
                                                        $bride = $brides->where('id',$wpcvn->bride_id)->first();
                                                    @endphp
                                                        <option value="{{ $wpcvn->id }}">
                                                            {{ "Mr.".$bride->groom }}
                                                            @if ($bride->groom_chinese)
                                                                {{ " (".$bride->groom_chinese.")" }}
                                                            @endif
                                                            & 
                                                            {{ "Mrs.".$bride->bride }}
                                                            @if ($bride->bride_chinese)
                                                                {{ " (".$bride->bride_chinese.") " }}
                                                            @endif
                                                            @if ($wpcvn->wedding_date > $now)
                                                                - @lang('messages.Wedding Date'): {{ date('Y-m-d',strtotime($wpcvn->wedding_date)) }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="slot">@lang('messages.Slot') <span> *</span></label>
                                                <select name="slot" id="slot" class="form-control custom-select @error('slot') is-invalid @enderror" required>
                                                    @if ($ceremony_venue->slot)
                                                        <option selected value="">@lang('messages.Select one')</option>
                                                        @if ($ceremony_venue)
                                                            @php
                                                                $cv_slots = json_decode($ceremony_venue->slot);
                                                            @endphp
                                                            @foreach ($cv_slots as $slt)
                                                                @if ($slt != $ceremony_venue->slot)
                                                                    <option value="{{ $slt }}">{{ $slt }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                                @error('slot')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addCeremonialVenueToWeddingPlanner-{{ $ceremony_venue->id }}" class="btn btn-primary">@lang("messages.Add To Wedding Planner")</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <script>
                var formId = "orderWeddingVenue-{{ $ceremony_venue->id }}"
                $(document).ready(function() {
                    $("#"+ formId).submit(function() {
                        $(".result").text("");
                        $(".loading-icon").removeClass("hidden");
                        $(".submit").attr("disabled", true);
                        $(".btn-txt").text("Processing ...");
                    });
                });
            </script>
        @endforeach
    </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.selectInput').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                // Mendapatkan ID dari elemen harga terkait dan input tersembunyi
                var priceId = selectElement.getAttribute('data-price-id');
                var hiddenInputId = selectElement.getAttribute('data-hidden-input-id');
                
                // Mendapatkan opsi yang dipilih
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                
                // Mendapatkan harga dari atribut data-basic-price
                var price = selectedOption.getAttribute('data-basic-price');
                
                var priceString = price ? price.toString() : '0';
                // Memperbarui harga di elemen terkait
                document.getElementById(priceId).textContent = price ? price : '0';
                
                // Memperbarui nilai input tersembunyi
                document.getElementById(hiddenInputId).value = priceString;
            });
        });
    });
    </script>