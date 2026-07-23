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
    <div class="tour-location-repeater" data-tour-locations-repeater data-resolve-url="{{ route('tour-location.resolve-coordinates') }}" data-references-url="{{ route('tour-location.references') }}">
        <div class="d-flex justify-content-between align-items-start flex-wrap m-b-18">
            <div>
                <h5 class="m-b-5">Tour Route Map Locations</h5>
                <p class="text-muted m-b-0">Add planned stops for the OpenStreetMap route overview. Google Maps link is only used as an external reference.</p>
            </div>
            <button type="button" class="backend-button backend-button-secondary" data-add-tour-location>
                <i class="fa fa-plus"></i> Add Location
            </button>
        </div>

        <div data-tour-location-list>
            @foreach ($sourceLocations as $index => $location)
                <div class="tour-location-item border rounded p-3 m-b-18" data-tour-location-item>
                    <div class="d-flex justify-content-between align-items-center m-b-12">
                        <strong>Location <span data-tour-location-number>{{ $index + 1 }}</span></strong>
                        <button type="button" class="backend-icon-action is-danger" data-remove-tour-location>
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="backend-form-field">
                                <label>Destination Name</label>
                                <input type="hidden" name="locations[{{ $index }}][location_reference_id]" value="{{ $location['location_reference_id'] ?? '' }}" data-field-name="location_reference_id" data-tour-location-reference-id>
                                <div class="tour-location-suggest">
                                    <input type="text" name="locations[{{ $index }}][destination_name]" class="backend-form-control @error("locations.$index.destination_name") is-invalid @enderror" value="{{ $location['destination_name'] ?? '' }}" placeholder="e.g. Tanah Lot Temple" autocomplete="off" data-field-name="destination_name" data-tour-location-name>
                                    <div class="tour-location-suggest__menu" data-tour-location-suggestions></div>
                                </div>
                                @error("locations.$index.destination_name")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="backend-form-field">
                                <label>Location Type</label>
                                <select name="locations[{{ $index }}][location_type]" data-field-name="location_type" class="backend-form-control @error("locations.$index.location_type") is-invalid @enderror">
                                    @foreach ($tourLocationTypes as $type => $label)
                                        <option value="{{ $type }}" @selected(($location['location_type'] ?? 'Attraction') === $type)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error("locations.$index.location_type")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="backend-form-field">
                                <label>Day</label>
                                <input type="number" min="1" name="locations[{{ $index }}][day_number]" data-field-name="day_number" class="backend-form-control @error("locations.$index.day_number") is-invalid @enderror" value="{{ $location['day_number'] ?? 1 }}">
                                @error("locations.$index.day_number")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="backend-form-field">
                                <label>Order</label>
                                <input type="number" min="1" name="locations[{{ $index }}][visit_order]" data-field-name="visit_order" class="backend-form-control @error("locations.$index.visit_order") is-invalid @enderror" value="{{ $location['visit_order'] ?? ($index + 1) }}">
                                @error("locations.$index.visit_order")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="backend-form-field">
                                <label>Visit Time</label>
                                <input type="time" name="locations[{{ $index }}][visit_time]" data-field-name="visit_time" class="backend-form-control @error("locations.$index.visit_time") is-invalid @enderror" value="{{ $location['visit_time'] ?? '' }}">
                                @error("locations.$index.visit_time")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5 d-none">
                            <div class="backend-form-field">
                                <label>Latitude</label>
                                <input type="number" step="0.0000001" min="-90" max="90" name="locations[{{ $index }}][latitude]" data-field-name="latitude" class="backend-form-control @error("locations.$index.latitude") is-invalid @enderror" value="{{ $location['latitude'] ?? '' }}" placeholder="Auto from Google link or -8.6212130" data-tour-location-latitude>
                                @error("locations.$index.latitude")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 d-none">
                            <div class="backend-form-field">
                                <label>Longitude</label>
                                <input type="number" step="0.0000001" min="-180" max="180" name="locations[{{ $index }}][longitude]" data-field-name="longitude" class="backend-form-control @error("locations.$index.longitude") is-invalid @enderror" value="{{ $location['longitude'] ?? '' }}" placeholder="Auto from Google link or 115.0868060" data-tour-location-longitude>
                                @error("locations.$index.longitude")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="backend-form-field">
                                <label>Google Maps Short Link / Coordinate URL</label>
                                <input type="url" name="locations[{{ $index }}][google_maps_url]" data-field-name="google_maps_url" class="backend-form-control @error("locations.$index.google_maps_url") is-invalid @enderror" value="{{ $location['google_maps_url'] ?? '' }}" placeholder="https://maps.app.goo.gl/..." data-tour-location-map-url>
                                <small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled automatically from the selected reference or Google Maps URL.</small>
                                @error("locations.$index.google_maps_url")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="backend-form-field">
                                <label>Marker Cover Image</label>
                                <input type="hidden" name="locations[{{ $index }}][existing_marker_image]" value="{{ $location['marker_image'] ?? '' }}" data-field-name="existing_marker_image">
                                <input type="file" name="locations[{{ $index }}][marker_image]" data-field-name="marker_image" class="backend-form-control @error("locations.$index.marker_image") is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small>
                                <div class="m-t-8" data-tour-location-image-preview>
                                    @if (!empty($location['marker_image']))
                                        <img src="{{ asset('storage/tours/tour-location-markers/' . $location['marker_image']) }}" alt="Marker image" class="tour-location-marker-preview">
                                    @endif
                                </div>
                                @error("locations.$index.marker_image")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="backend-form-field">
                                <label>Description / Notes</label>
                                <textarea name="locations[{{ $index }}][description]" data-field-name="description" class="backend-form-control @error("locations.$index.description") is-invalid @enderror" data-backend-richtext="true" rows="2" placeholder="Short stop note for marker popup">{{ $location['description'] ?? '' }}</textarea>
                                @error("locations.$index.description")
                                    <div class="backend-form-error">{{ $message }}</div>
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
                    <button type="button" class="backend-icon-action is-danger" data-remove-tour-location>
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-5"><div class="backend-form-field"><label>Destination Name</label><input type="hidden" data-field-name="location_reference_id" data-tour-location-reference-id><div class="tour-location-suggest"><input type="text" data-field-name="destination_name" data-tour-location-name class="backend-form-control" placeholder="e.g. Tanah Lot Temple" autocomplete="off"><div class="tour-location-suggest__menu" data-tour-location-suggestions></div></div></div></div>
                    <div class="col-md-3"><div class="backend-form-field"><label>Location Type</label><select data-field-name="location_type" class="backend-form-control"><option value="Attraction">Attraction</option><option value="Activity">Activity</option><option value="F&B">F&amp;B</option><option value="Pickup/Dropoff">Pickup/Dropoff Location</option></select></div></div>
                    <div class="col-md-2"><div class="backend-form-field"><label>Day</label><input type="number" min="1" value="1" data-field-name="day_number" class="backend-form-control"></div></div>
                    <div class="col-md-2"><div class="backend-form-field"><label>Order</label><input type="number" min="1" value="1" data-field-name="visit_order" class="backend-form-control"></div></div>
                    <div class="col-md-3"><div class="backend-form-field"><label>Visit Time</label><input type="time" data-field-name="visit_time" class="backend-form-control"></div></div>
                    <div class="col-md-5 d-none"><div class="backend-form-field"><label>Latitude</label><input type="number" step="0.0000001" min="-90" max="90" data-field-name="latitude" data-tour-location-latitude class="backend-form-control" placeholder="Auto from Google link or -8.6212130"></div></div>
                    <div class="col-md-4 d-none"><div class="backend-form-field"><label>Longitude</label><input type="number" step="0.0000001" min="-180" max="180" data-field-name="longitude" data-tour-location-longitude class="backend-form-control" placeholder="Auto from Google link or 115.0868060"></div></div>
                    <div class="col-md-8"><div class="backend-form-field"><label>Google Maps Short Link / Coordinate URL</label><input type="url" data-field-name="google_maps_url" data-tour-location-map-url class="backend-form-control" placeholder="https://maps.app.goo.gl/..."><small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled automatically from the selected reference or Google Maps URL.</small></div></div>
                    <div class="col-md-12"><div class="backend-form-field"><label>Marker Cover Image</label><input type="hidden" data-field-name="existing_marker_image"><input type="file" data-field-name="marker_image" class="backend-form-control" accept="image/jpeg,image/png,image/jpg,image/webp"><small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small><div class="m-t-8" data-tour-location-image-preview></div></div></div>
                    <div class="col-md-12"><div class="backend-form-field"><label>Description / Notes</label><textarea data-field-name="description" class="backend-form-control" data-backend-richtext="true" rows="2" placeholder="Short stop note for marker popup"></textarea></div></div>
                </div>
            </div>
        </template>
    </div>
</div>
