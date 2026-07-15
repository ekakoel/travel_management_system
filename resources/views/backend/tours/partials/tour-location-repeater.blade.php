@php
    $tourLocationTypes = [
        'Attraction' => 'Attraction',
        'Activity' => 'Activity',
        'F&B' => 'F&B',
        'Pickup/Dropoff' => 'Pickup/Dropoff Location',
    ];
    $sourceLocations = old('locations');

    if (is_null($sourceLocations) && isset($tour)) {
        $sourceLocations = $tour->locations
            ->sortBy([['day_number', 'asc'], ['visit_order', 'asc']])
            ->map(fn ($location) => [
                'destination_name' => $location->destination_name,
                'location_reference_id' => $location->location_reference_id,
                'location_type' => $location->location_type ?: 'Attraction',
                'google_maps_url' => $location->google_maps_url,
                'marker_image' => $location->marker_image,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'day_number' => $location->day_number,
                'visit_order' => $location->visit_order,
                'visit_time' => optional($location->visit_time)->format('H:i'),
                'description' => $location->description,
                'is_active' => $location->is_active ? '1' : '0',
            ])
            ->values()
            ->all();
    }

    $sourceLocations = is_array($sourceLocations) && count($sourceLocations)
        ? array_values($sourceLocations)
        : [[
            'destination_name' => '',
            'location_reference_id' => '',
            'location_type' => 'Attraction',
            'google_maps_url' => '',
            'marker_image' => '',
            'latitude' => '',
            'longitude' => '',
            'day_number' => 1,
            'visit_order' => 1,
            'visit_time' => '',
            'description' => '',
            'is_active' => '1',
        ]];
@endphp

<div class="col-12">
    <div class="tour-location-repeater" data-tour-locations-repeater>
        <div class="d-flex justify-content-between align-items-start flex-wrap m-b-18">
            <div>
                <h5 class="m-b-5">Tour Route Map Locations</h5>
                <p class="text-muted m-b-0">Add planned stops for the OpenStreetMap route overview. Google Maps link is only used as an external reference.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-add-tour-location>
                <i class="fa fa-plus"></i> Add Location
            </button>
        </div>

        <div data-tour-location-list>
            @foreach ($sourceLocations as $index => $location)
                <div class="tour-location-item border rounded p-3 m-b-18" data-tour-location-item>
                    <div class="d-flex justify-content-between align-items-center m-b-12">
                        <strong>Location <span data-tour-location-number>{{ $index + 1 }}</span></strong>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-remove-tour-location>
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Destination Name</label>
                                <input type="hidden" name="locations[{{ $index }}][location_reference_id]" value="{{ $location['location_reference_id'] ?? '' }}" data-field-name="location_reference_id" data-tour-location-reference-id>
                                <div class="tour-location-suggest">
                                    <input type="text" name="locations[{{ $index }}][destination_name]" class="form-control @error("locations.$index.destination_name") is-invalid @enderror" value="{{ $location['destination_name'] ?? '' }}" placeholder="e.g. Tanah Lot Temple" autocomplete="off" data-field-name="destination_name" data-tour-location-name>
                                    <div class="tour-location-suggest__menu" data-tour-location-suggestions></div>
                                </div>
                                @error("locations.$index.destination_name")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Location Type</label>
                                <select name="locations[{{ $index }}][location_type]" data-field-name="location_type" class="custom-select @error("locations.$index.location_type") is-invalid @enderror">
                                    @foreach ($tourLocationTypes as $type => $label)
                                        <option value="{{ $type }}" @selected(($location['location_type'] ?? 'Attraction') === $type)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error("locations.$index.location_type")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Day</label>
                                <input type="number" min="1" name="locations[{{ $index }}][day_number]" data-field-name="day_number" class="form-control @error("locations.$index.day_number") is-invalid @enderror" value="{{ $location['day_number'] ?? 1 }}">
                                @error("locations.$index.day_number")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" min="1" name="locations[{{ $index }}][visit_order]" data-field-name="visit_order" class="form-control @error("locations.$index.visit_order") is-invalid @enderror" value="{{ $location['visit_order'] ?? ($index + 1) }}">
                                @error("locations.$index.visit_order")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Visit Time</label>
                                <input type="time" name="locations[{{ $index }}][visit_time]" data-field-name="visit_time" class="form-control @error("locations.$index.visit_time") is-invalid @enderror" value="{{ $location['visit_time'] ?? '' }}">
                                @error("locations.$index.visit_time")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5 d-none">
                            <div class="form-group">
                                <label>Latitude</label>
                                <input type="number" step="0.0000001" min="-90" max="90" name="locations[{{ $index }}][latitude]" data-field-name="latitude" class="form-control @error("locations.$index.latitude") is-invalid @enderror" value="{{ $location['latitude'] ?? '' }}" placeholder="Auto from Google link or -8.6212130" data-tour-location-latitude>
                                @error("locations.$index.latitude")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 d-none">
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="number" step="0.0000001" min="-180" max="180" name="locations[{{ $index }}][longitude]" data-field-name="longitude" class="form-control @error("locations.$index.longitude") is-invalid @enderror" value="{{ $location['longitude'] ?? '' }}" placeholder="Auto from Google link or 115.0868060" data-tour-location-longitude>
                                @error("locations.$index.longitude")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Google Maps Short Link / Coordinate URL</label>
                                <input type="url" name="locations[{{ $index }}][google_maps_url]" data-field-name="google_maps_url" class="form-control @error("locations.$index.google_maps_url") is-invalid @enderror" value="{{ $location['google_maps_url'] ?? '' }}" placeholder="https://maps.app.goo.gl/..." data-tour-location-map-url>
                                <small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled automatically from the selected reference or Google Maps URL.</small>
                                @error("locations.$index.google_maps_url")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Marker Cover Image</label>
                                <input type="hidden" name="locations[{{ $index }}][existing_marker_image]" value="{{ $location['marker_image'] ?? '' }}" data-field-name="existing_marker_image">
                                <input type="file" name="locations[{{ $index }}][marker_image]" data-field-name="marker_image" class="form-control @error("locations.$index.marker_image") is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small>
                                <div class="m-t-8" data-tour-location-image-preview>
                                    @if (!empty($location['marker_image']))
                                        <img src="{{ asset('storage/tours/tour-location-markers/' . $location['marker_image']) }}" alt="Marker image" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">
                                    @endif
                                </div>
                                @error("locations.$index.marker_image")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description / Notes</label>
                                <textarea name="locations[{{ $index }}][description]" data-field-name="description" class="form-control @error("locations.$index.description") is-invalid @enderror" rows="2" placeholder="Short stop note for marker popup">{{ $location['description'] ?? '' }}</textarea>
                                @error("locations.$index.description")
                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-tour-location-template>
            <div class="tour-location-item border rounded p-3 m-b-18" data-tour-location-item>
                <div class="d-flex justify-content-between align-items-center m-b-12">
                    <strong>Location <span data-tour-location-number></span></strong>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove-tour-location>
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-5"><div class="form-group"><label>Destination Name</label><input type="hidden" data-field-name="location_reference_id" data-tour-location-reference-id><div class="tour-location-suggest"><input type="text" data-field-name="destination_name" data-tour-location-name class="form-control" placeholder="e.g. Tanah Lot Temple" autocomplete="off"><div class="tour-location-suggest__menu" data-tour-location-suggestions></div></div></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Location Type</label><select data-field-name="location_type" class="custom-select"><option value="Attraction">Attraction</option><option value="Activity">Activity</option><option value="F&B">F&amp;B</option><option value="Pickup/Dropoff">Pickup/Dropoff Location</option></select></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Day</label><input type="number" min="1" value="1" data-field-name="day_number" class="form-control"></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Order</label><input type="number" min="1" value="1" data-field-name="visit_order" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Visit Time</label><input type="time" data-field-name="visit_time" class="form-control"></div></div>
                    <div class="col-md-5 d-none"><div class="form-group"><label>Latitude</label><input type="number" step="0.0000001" min="-90" max="90" data-field-name="latitude" data-tour-location-latitude class="form-control" placeholder="Auto from Google link or -8.6212130"></div></div>
                    <div class="col-md-4 d-none"><div class="form-group"><label>Longitude</label><input type="number" step="0.0000001" min="-180" max="180" data-field-name="longitude" data-tour-location-longitude class="form-control" placeholder="Auto from Google link or 115.0868060"></div></div>
                    <div class="col-md-8"><div class="form-group"><label>Google Maps Short Link / Coordinate URL</label><input type="url" data-field-name="google_maps_url" data-tour-location-map-url class="form-control" placeholder="https://maps.app.goo.gl/..."><small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled automatically from the selected reference or Google Maps URL.</small></div></div>
                    <div class="col-md-12"><div class="form-group"><label>Marker Cover Image</label><input type="hidden" data-field-name="existing_marker_image"><input type="file" data-field-name="marker_image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp"><small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small><div class="m-t-8" data-tour-location-image-preview></div></div></div>
                    <div class="col-md-12"><div class="form-group"><label>Description / Notes</label><textarea data-field-name="description" class="form-control" rows="2" placeholder="Short stop note for marker popup"></textarea></div></div>
                </div>
            </div>
        </template>
    </div>
</div>

@once
    @push('styles')
        <style>
            .tour-location-suggest {
                position: relative;
            }

            .tour-location-suggest__menu {
                position: absolute;
                top: calc(100% + 6px);
                right: 0;
                left: 0;
                z-index: 40;
                display: none;
                max-height: 280px;
                overflow-y: auto;
                border: 1px solid rgba(203, 213, 225, 0.95);
                border-radius: 14px;
                background: #ffffff;
                box-shadow: 0 20px 44px rgba(15, 23, 42, 0.16);
            }

            .tour-location-suggest__menu.is-open {
                display: block;
            }

            .tour-location-suggest__item {
                display: flex;
                width: 100%;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem;
                border: 0;
                border-bottom: 1px solid #edf2f7;
                background: #ffffff;
                color: #1f2937;
                cursor: pointer;
                text-align: left;
            }

            .tour-location-suggest__item:hover,
            .tour-location-suggest__item:focus {
                background: #f0fdfa;
                outline: none;
            }

            .tour-location-suggest__item img,
            .tour-location-suggest__avatar {
                width: 42px;
                height: 42px;
                flex: 0 0 42px;
                border-radius: 50%;
                object-fit: cover;
            }

            .tour-location-suggest__avatar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #0f766e;
                color: #ffffff;
                font-weight: 800;
            }

            .tour-location-suggest__item strong,
            .tour-location-suggest__item small {
                display: block;
            }

            .tour-location-suggest__item small,
            .tour-location-suggest__empty {
                color: #64748b;
                font-size: 0.82rem;
            }

            .tour-location-suggest__empty {
                padding: 0.85rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-tour-locations-repeater]').forEach(function (repeater) {
                    if (repeater.dataset.ready === 'true') {
                        return;
                    }

                    repeater.dataset.ready = 'true';

                    var list = repeater.querySelector('[data-tour-location-list]');
                    var template = repeater.querySelector('[data-tour-location-template]');
                    var addButton = repeater.querySelector('[data-add-tour-location]');
                    var resolveUrl = @json(route('tour-location.resolve-coordinates'));
                    var referencesUrl = @json(route('tour-location.references'));
                    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrfToken = csrfMeta ? csrfMeta.content : '';
                    var resolveTimers = new WeakMap();
                    var referenceTimers = new WeakMap();

                    function refreshIndexes() {
                        list.querySelectorAll('[data-tour-location-item]').forEach(function (item, index) {
                            var number = item.querySelector('[data-tour-location-number]');
                            if (number) {
                                number.textContent = index + 1;
                            }

                            item.querySelectorAll('[data-field-name]').forEach(function (field) {
                                field.name = 'locations[' + index + '][' + field.dataset.fieldName + ']';
                            });

                            var order = item.querySelector('[name="locations[' + index + '][visit_order]"]');
                            if (order && !order.value) {
                                order.value = index + 1;
                            }
                        });
                    }

                    function setStatus(item, message, state) {
                        var status = item.querySelector('[data-tour-coordinate-status]');

                        if (!status) {
                            return;
                        }

                        status.textContent = message;
                        status.classList.remove('text-muted', 'text-success', 'text-danger');
                        status.classList.add(state || 'text-muted');
                    }

                    function field(item, selector) {
                        return item.querySelector(selector);
                    }

                    function escapeHtml(value) {
                        return String(value || '').replace(/[&<>"']/g, function (character) {
                            return {
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                '"': '&quot;',
                                "'": '&#039;',
                            }[character];
                        });
                    }

                    function closeSuggestions(item) {
                        var menu = item.querySelector('[data-tour-location-suggestions]');

                        if (menu) {
                            menu.innerHTML = '';
                            menu.classList.remove('is-open');
                        }
                    }

                    function fillLocationFromReference(item, location) {
                        var referenceInput = field(item, '[data-tour-location-reference-id]');
                        var nameInput = field(item, '[data-tour-location-name]');
                        var typeInput = field(item, '[data-field-name="location_type"], select[name$="[location_type]"]');
                        var latitudeInput = field(item, '[data-tour-location-latitude]');
                        var longitudeInput = field(item, '[data-tour-location-longitude]');
                        var mapsInput = field(item, '[data-tour-location-map-url]');
                        var existingImageInput = field(item, '[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');
                        var descriptionInput = field(item, '[data-field-name="description"], textarea[name$="[description]"]');

                        if (referenceInput) referenceInput.value = location.id || '';
                        if (nameInput) nameInput.value = location.destination_name || '';
                        if (typeInput) typeInput.value = location.location_type || 'Attraction';
                        if (latitudeInput) latitudeInput.value = location.latitude || '';
                        if (longitudeInput) longitudeInput.value = location.longitude || '';
                        if (mapsInput) mapsInput.value = location.google_maps_url || '';
                        if (existingImageInput) existingImageInput.value = location.marker_image || '';
                        if (descriptionInput) descriptionInput.value = location.description || '';

                        var preview = item.querySelector('[data-tour-location-image-preview]');

                        if (preview && location.marker_image_url) {
                            preview.innerHTML = '<img src="' + location.marker_image_url + '" alt="Marker image" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">';
                        }

                        closeSuggestions(item);
                    }

                    function renderSuggestions(item, locations) {
                        var menu = item.querySelector('[data-tour-location-suggestions]');

                        if (!menu) {
                            return;
                        }

                        if (!locations.length) {
                            menu.innerHTML = '<div class="tour-location-suggest__empty">No saved location found.</div>';
                            menu.classList.add('is-open');
                            return;
                        }

                        menu.innerHTML = locations.map(function (location) {
                            var image = location.marker_image_url
                                ? '<img src="' + escapeHtml(location.marker_image_url) + '" alt="">'
                                : '<span class="tour-location-suggest__avatar">' + escapeHtml((location.destination_name || '?').charAt(0)) + '</span>';

                            return '<button type="button" class="tour-location-suggest__item" data-reference-id="' + location.id + '">' +
                                image +
                                '<span><strong>' + escapeHtml(location.destination_name) + '</strong><small>' + escapeHtml(location.location_type) + ' · ' + escapeHtml(location.latitude) + ', ' + escapeHtml(location.longitude) + '</small></span>' +
                                '</button>';
                        }).join('');

                        menu.querySelectorAll('[data-reference-id]').forEach(function (button, index) {
                            button.addEventListener('click', function () {
                                fillLocationFromReference(item, locations[index]);
                            });
                        });

                        menu.classList.add('is-open');
                    }

                    function searchLocationReferences(item) {
                        var nameInput = item.querySelector('[data-tour-location-name]');
                        var query = nameInput ? nameInput.value.trim() : '';

                        if (query.length < 2) {
                            closeSuggestions(item);
                            return;
                        }

                        fetch(referencesUrl + '?q=' + encodeURIComponent(query), {
                            headers: {
                                'Accept': 'application/json',
                            },
                        })
                            .then(function (response) {
                                return response.json();
                            })
                            .then(function (locations) {
                                renderSuggestions(item, Array.isArray(locations) ? locations : []);
                            })
                            .catch(function () {
                                closeSuggestions(item);
                            });
                    }

                    function queueLocationReferenceSearch(item) {
                        var existingTimer = referenceTimers.get(item);

                        if (existingTimer) {
                            clearTimeout(existingTimer);
                        }

                        referenceTimers.set(item, setTimeout(function () {
                            searchLocationReferences(item);
                        }, 260));
                    }

                    function resolveCoordinates(item) {
                        var urlInput = item.querySelector('[data-tour-location-map-url]');
                        var latitudeInput = item.querySelector('[data-tour-location-latitude]');
                        var longitudeInput = item.querySelector('[data-tour-location-longitude]');
                        var googleMapsUrl = urlInput ? urlInput.value.trim() : '';

                        if (!googleMapsUrl || !latitudeInput || !longitudeInput) {
                            return;
                        }

                        setStatus(item, 'Reading coordinates from Google Maps link...', 'text-muted');

                        fetch(resolveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ google_maps_url: googleMapsUrl }),
                        })
                            .then(function (response) {
                                return response.json().then(function (payload) {
                                    if (!response.ok) {
                                        throw payload;
                                    }

                                    return payload;
                                });
                            })
                            .then(function (payload) {
                                latitudeInput.value = payload.latitude;
                                longitudeInput.value = payload.longitude;
                                setStatus(item, 'Coordinates filled automatically from Google Maps link.', 'text-success');
                            })
                            .catch(function (error) {
                                setStatus(item, error.message || 'Coordinates could not be read. Please fill latitude and longitude manually.', 'text-danger');
                            });
                    }

                    function queueResolveCoordinates(item) {
                        var existingTimer = resolveTimers.get(item);

                        if (existingTimer) {
                            clearTimeout(existingTimer);
                        }

                        resolveTimers.set(item, setTimeout(function () {
                            resolveCoordinates(item);
                        }, 600));
                    }

                    if (addButton && template && list) {
                        addButton.addEventListener('click', function () {
                            var fragment = template.content.cloneNode(true);
                            list.appendChild(fragment);
                            refreshIndexes();
                        });
                    }

                    repeater.addEventListener('click', function (event) {
                        var removeButton = event.target.closest('[data-remove-tour-location]');
                        if (!removeButton) {
                            return;
                        }

                        var item = removeButton.closest('[data-tour-location-item]');
                        if (item && list.querySelectorAll('[data-tour-location-item]').length > 1) {
                            item.remove();
                            refreshIndexes();
                        }
                    });

                    repeater.addEventListener('input', function (event) {
                        if (event.target.matches('[data-tour-location-name]')) {
                            var nameItem = event.target.closest('[data-tour-location-item]');
                            var referenceInput = nameItem ? nameItem.querySelector('[data-tour-location-reference-id]') : null;

                            if (referenceInput) {
                                referenceInput.value = '';
                            }

                            if (nameItem) {
                                queueLocationReferenceSearch(nameItem);
                            }

                            return;
                        }

                        if (!event.target.matches('[data-tour-location-map-url]')) {
                            return;
                        }

                        var item = event.target.closest('[data-tour-location-item]');
                        if (item) {
                            queueResolveCoordinates(item);
                        }
                    });

                    repeater.addEventListener('change', function (event) {
                        if (!event.target.matches('[data-tour-location-map-url]')) {
                            return;
                        }

                        var item = event.target.closest('[data-tour-location-item]');
                        if (item) {
                            resolveCoordinates(item);
                        }
                    });

                    document.addEventListener('click', function (event) {
                        if (repeater.contains(event.target)) {
                            return;
                        }

                        repeater.querySelectorAll('[data-tour-location-item]').forEach(closeSuggestions);
                    });

                    refreshIndexes();
                });
            });
        </script>
    @endpush
@endonce
