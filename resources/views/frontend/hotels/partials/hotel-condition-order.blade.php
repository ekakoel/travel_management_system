<div class="condition-container">
    <div class="service-header">
        <i class="icon-copy dw dw-hotel"></i>
        <div class="service-detail">
            <div class="name">
                <a href="{{ $hotel->web }}" target="__blank">
                    {{ $hotel->name }}
                </a>
            </div>
            <p>
                <i class="icon-copy dw dw-map2"></i>
                <a href="{{ $hotel->map }}" target="__blank">
                    <i>
                        {{ $hotel->address }}
                    </i>
                </a>
            </p>
        </div>
    </div>
    @if (!session('booking_dates.duration') || session('booking_dates.duration') < $hotel->min_stay || !session('booking_dates.nor') || !session('booking_dates.nog') 
        || session('booking_dates.nor') < 1 || session('booking_dates.nog') < 1)
        @if (!session('booking_dates.nor') || !session('booking_dates.nog') || session('booking_dates.nor') < 1 || session('booking_dates.nog') < 1)
            <div class="notif-container m-t-8" id="notif-rooms-guests">
                <p><i class="icon-copy dw dw-information"></i>
                    @lang('messages.Please enter the number of rooms and guests before checking the room rates!')
                </p>
            </div>
        @endif
        @if (!session('booking_dates.duration') || session('booking_dates.duration') < $hotel->min_stay)
            <div class="notif-container m-t-8" id="notif-duration">
                <p><i class="icon-copy dw dw-information"></i>
                    @lang('messages.Set your check-in and check-out dates before accessing the available rates!')
                </p>
            </div>
        @endif
    @endif

    <div class="detail-condition">
        <div class="rooms-guests-box">
            <div class="summary" onclick="togglePopup()">
                <div class="form-title">@lang('messages.Rooms & Guests')</div>
                <span id="summaryText">{{ $number_of_rooms }} @lang('messages.Room'), {{ $number_of_guests }}
                    @lang('messages.Guests')</span>
                <span class="arrow"><i class="icon-copy dw dw-down-arrow-4"></i></span>
            </div>
            <div class="date-box">
                <input type="hidden" id="litepickerInput">
                <div class="summary" id="dateSummaryBox">
                    <div class="form-title" id="durationLabel">@lang('messages.Duration')
                        ({{ session('booking_dates.duration') }} @lang('messages.nights'))</div>
                    <span
                        id="durationDate">{{ date('m/d/Y', strtotime($checkin)) . ' - ' . date('m/d/Y', strtotime($checkout)) }}</span>
                    <span class="arrow"><i class="icon-copy dw dw-down-arrow-4"></i></span>
                </div>
            </div>
            <div class="popup-container" id="popupForm">
                <div class="popup-guests">
                    <h4>@lang('messages.Rooms & Guests')</h4>
                    <div class="line-group">
                        <label>@lang('messages.Rooms')</label>
                        <div class="counter">
                            <button onclick="changeCount('rooms', -1)">−</button>
                            <span id="roomsCount">{{ $number_of_rooms }}</span>
                            <button onclick="changeCount('rooms', 1)">+</button>
                        </div>
                    </div>

                    <div class="line-group">
                        <label>@lang('messages.Adults')</label>
                        <div class="counter">
                            <button onclick="changeCount('adults', -1)">−</button>
                            <span id="adultsCount">{{ $number_of_adults }}</span>
                            <button onclick="changeCount('adults', 1)">+</button>
                        </div>
                    </div>

                    <div class="line-group">
                        <label>@lang('messages.Children')</label>
                        <div class="counter">
                            <button onclick="changeCount('children', -1)">−</button>
                            <span id="childrenCount">{{ $number_of_childs }}</span>
                            <button onclick="changeCount('children', 1)">+</button>
                        </div>
                    </div>
                    <div id="childAges"></div>
                </div>
            </div>
        </div>

        <form id="checkPrice" action="{{ route('view.hotel-prices', $hotel->code) }}" method="POST" role="search">
            {{ csrf_field() }}
            <input type="hidden" name="checkincout"
                value="{{ date('m/d/Y', strtotime(session('booking_dates.checkin'))) . ' - ' . date('m/d/Y', strtotime(session('booking_dates.checkout'))) }}">
            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
            <input type="hidden" name="hotelcode" value="{{ $hotel->code }}">
            <input type="hidden" id="adultInput" name="adult_guests" value="2">
            <input type="hidden" id="childrenInput" name="children_guests" value="0">
            <input type="hidden" id="numberOfRoomInput" name="number_of_room" value="1">
            <input type="hidden" id="childAgesInput" name="children_ages" value="[]">
            <button type="submit"
                class="btn btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i>
                @lang('messages.Check Price')</button>
        </form>
    </div>

</div>
<div id="condition-placeholder"></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<script>
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar');
    const condition = document.querySelector('.condition-container');
    const placeholder = document.getElementById('condition-placeholder');
    function smoothHide(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.classList.add("notif-hide");

        // setelah animasi selesai → benar-benar hilang dari layout
        setTimeout(() => {
            el.style.display = "none";
        }, 400); // harus sama dengan transition CSS
    }
    function setPlaceholder() {
        placeholder.style.height = 135 + 'px';
    }
    window.addEventListener('scroll', function() {
        let current = window.scrollY;
        if (current > lastScroll && current > 80) {
            navbar.classList.add('navbar-hide');
            setPlaceholder();
            condition.classList.add('condition-fixed');
            setTimeout(() => {
                condition.classList.add('condition-show');
            }, 10);
        }
        if (current < lastScroll && current > 70) {}
        if (current === 0) {
            navbar.classList.remove('navbar-hide');
            condition.classList.remove('condition-show');
            condition.classList.remove('condition-fixed');
            placeholder.style.height = '0px';
        }

        lastScroll = current;
    });
</script>
<script>
    let state = {
        rooms: {{ session('booking_dates.nor', 0) }},
        adults: {{ session('booking_dates.noa', 0) }},
        children: {{ session('booking_dates.noc', 0) }},
    };
    let childAges = @json($childAges);
    const lang = {
        room: "{{ __('messages.Room') }}",
        rooms: "{{ __('messages.Rooms') }}",
        guest: "{{ __('messages.Guest') }}",
        guests: "{{ __('messages.Guests') }}",
        age: "{{ __('messages.Age') }}",
        child: "{{ __('messages.Child') }}",
        children: "{{ __('messages.Children') }}",
    };

    function togglePopup() {
        const p = document.getElementById("popupForm");
        p.style.display = p.style.display === "block" ? "none" : "block";
    }

    document.addEventListener("click", e => {
        const box = document.querySelector(".rooms-guests-box");
        if (!box.contains(e.target)) {
            document.getElementById("popupForm").style.display = "none";
        }
    });

    function updateChildAge(index, value) {
        document.getElementById(`ageVal${index}`).innerText = value;
        childAges[index] = parseInt(value);
        updateChildAgesInput();
    }

    function updateChildAgesInput() {
        document.getElementById("childAgesInput").value = JSON.stringify(childAges);
        if (!childAges) {
            $.ajax({
                url: '{{ route('update.booking.children-ages') }}',
                method: 'POST',
                data: {
                    children_ages: childAges,
                    _token: document.querySelector(
                        'meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                success: function(response) {
                    console.log(response.message);
                },
                error: function(xhr) {
                    console.error("Gagal menyimpan umur anak:", xhr
                        .responseText);
                }
            });
        }
    }

    function changeCount(type, step) {
        if (type === "rooms") {
            if (step === -1 && state.rooms <= 1) return;
            if (step === 1 && state.rooms >= 9) return;
        }
        if (type === "adults") {
            if (step === -1 && state.adults <= 1) return;
        }

        state[type] += step;
        if (state[type] < 0) state[type] = 0;

        document.getElementById(type + "Count").innerText = state[type];

        if (type === "children") renderChildAges();
        updateSummary();
    }

    function renderChildAges() {
        const container = document.getElementById("childAges");
        container.innerHTML = "";
        Object.keys(childAges).forEach(k => {
            if (k > state.children) delete childAges[k];
        });

        for (let i = 0; i < state.children; i++) {

            const card = document.createElement("div");
            card.className = "child-card";

            const defaultValue = childAges[i] ?? 4;

            card.innerHTML = `
                <label>${lang.child} ${i+1} ${lang.age}:
                    <span id="ageVal${i}">${defaultValue}</span>
                </label>

                <input type="range" min="2" max="11" value="${defaultValue}"
                    oninput="updateChildAge(${i}, this.value)">
            `;

            container.appendChild(card);
            childAges[i] = defaultValue;
        }
        updateChildAgesInput();
    }

    function updateSummary() {
        const totalRooms = state.rooms;
        const totalAdults = state.adults;
        const totalChildrens = state.children;
        const totalGuests = totalAdults + totalChildrens;
        const roomLabel = totalRooms > 1 ? lang.rooms : lang.room;
        const guestLabel = totalGuests > 1 ? lang.guests : lang.guest;
        document.getElementById("summaryText").innerText = `${totalRooms} ${roomLabel}, ${totalGuests} ${guestLabel}`;
        document.getElementById("adultInput").value = totalAdults;
        document.getElementById("childrenInput").value = totalChildrens;
        document.getElementById("numberOfRoomInput").value = totalRooms;
        $.ajax({
            url: '{{ route('update.booking.room-guests') }}',
            method: 'POST',
            data: {
                nor: totalRooms,
                nog: totalGuests,
                noa: totalAdults,
                noc: totalChildrens,
                _token: document.querySelector(
                    'meta[name="csrf-token"]').getAttribute(
                    'content')
            },
            success: function(response) {
                console.log(response.message);
                if (totalGuests > 0 && totalRooms > 0) {
                    smoothHide("notif-rooms-guests");
                }
            },
            error: function(xhr) {
                console.error("Gagal menyimpan jumlah tamu dan jumlah kamar:", xhr
                    .responseText);
            }
        });
    }
    renderChildAges();
    updateSummary();
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const minDate = new Date();
        const lang = {
            duration: "{{ __('messages.Duration') }}",
            night: "{{ __('messages.Night') }}",
            nights: "{{ __('messages.Nights') }}",
        };
        minDate.setDate(minDate.getDate() + 7);
        const minDateString = minDate.toISOString().split('T')[0];
        const sessionCheckin = "{{ session('booking_dates.checkin') }}";
        const sessionCheckout = "{{ session('booking_dates.checkout') }}";
        const dateBoxEl = document.querySelector('.date-box');
        const picker = new Litepicker({
            element: document.getElementById('litepickerInput'),
            singleMode: false,
            numberOfMonths: 2,
            numberOfColumns: 2,
            minDate: minDateString,
            format: 'MM/DD/YYYY',
            parentEl: dateBoxEl,
            position: 'bottom',
            startDate: sessionCheckin ? sessionCheckin : null,
            endDate: sessionCheckout ? sessionCheckout : null,
            setup: (picker) => {
                if (sessionCheckin && sessionCheckout) {
                    const d1 = moment(sessionCheckin);
                    const d2 = moment(sessionCheckout);
                    const diff = d2.diff(d1, 'days');

                    document.getElementById("durationDate").textContent =
                        `${d1.format('MM/DD/YYYY')} - ${d2.format('MM/DD/YYYY')}`;

                    document.getElementById("durationLabel").textContent =
                        `{{ __('messages.Duration') }} (${diff} {{ __('messages.Nights') }})`;
                }
                picker.on('selected', (date1, date2) => {
                    if (date1 && date2) {
                        const diffTime = Math.abs(date2.getTime() - date1.getTime());
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        const checkInDisplay = date1.format('MM/DD/YYYY');
                        const checkOutDisplay = date2.format('MM/DD/YYYY');

                        document.getElementById('durationDate').textContent =
                            `${checkInDisplay} - ${checkOutDisplay}`;
                        document.getElementById('durationLabel').textContent =
                            `${lang.duration} (${diffDays} ${lang.nights})`;

                        const hiddenInput = document.querySelector(
                            'input[name="checkincout"]');
                        if (hiddenInput) {
                            hiddenInput.value = `${checkInDisplay} - ${checkOutDisplay}`;
                        }
                        const checkInLaravelFormat = date1.format('YYYY-MM-DD');
                        const checkOutLaravelFormat = date2.format('YYYY-MM-DD');
                        $.ajax({
                            url: '{{ route('update.booking.date') }}',
                            method: 'POST',
                            data: {
                                checkin: checkInLaravelFormat,
                                checkout: checkOutLaravelFormat,
                                _token: document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            success: function(response) {
                                console.log(response.message);
                                if (diffDays >= {{ $hotel->min_stay }}) {
                                    smoothHide("notif-duration");
                                }
                            },
                            error: function(xhr) {
                                console.error("Gagal menyimpan tanggal:", xhr
                                    .responseText);
                            }
                        });
                    }
                });
            },
        });
        document.getElementById('dateSummaryBox').addEventListener('click', function() {
            picker.show();
        });
    });
</script>
