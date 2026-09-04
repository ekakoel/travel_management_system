@php
    $tourLocationTypes = [
        'Attraction' => 'Attraction',
        'Activity' => 'Activity',
        'F&B' => 'F&B',
        'Pickup/Dropoff' => 'Pickup/Dropoff Location',
    ];
    $allowEmptyLocations = $allowEmptyLocations ?? false;
    $compactLocationCards = $compactLocationCards ?? false;
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
                'description_traditional' => $location->description_traditional,
                'description_simplified' => $location->description_simplified,
                'is_active' => $location->is_active ? '1' : '0',
            ])
            ->values()
            ->all();
    }

    $emptyLocation = [
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
            'description_traditional' => '',
            'description_simplified' => '',
            'is_active' => '1',
        ];

    $sourceLocations = is_array($sourceLocations) && count($sourceLocations)
        ? array_values($sourceLocations)
        : ($allowEmptyLocations ? [] : [$emptyLocation]);
@endphp

<div class="col-12">
    <div class="tour-location-repeater @if($compactLocationCards) tour-location-repeater--compact @endif" data-tour-locations-repeater data-allow-empty="{{ $allowEmptyLocations ? 'true' : 'false' }}" data-resolve-url="{{ route('admin.tour-location.resolve-coordinates') }}" data-references-url="{{ route('admin.tour-location.references') }}">
        <div class="d-flex justify-content-between align-items-start flex-wrap m-b-18">
            <div>
                <h5 class="m-b-5">Tour Route Map Locations</h5>
                <p class="text-muted m-b-0">Add planned stops for the OpenStreetMap route overview. This step can be left empty and completed later.</p>
            </div>
            <button type="button" class="backend-button backend-button-secondary" data-add-tour-location>
                <i class="fa fa-plus"></i> Add Tour Stop
            </button>
        </div>

        <div class="tour-location-empty @if(count($sourceLocations)) d-none @endif" data-tour-location-empty>
            <strong>No tour stops added yet.</strong>
            <span>Add stops only when the route map is ready. Zero stops is valid for a draft Tour Package.</span>
        </div>

        <div data-tour-location-list>
            @foreach ($sourceLocations as $index => $location)
                @php
                    $hasLocationError = $errors->has("locations.$index.destination_name")
                        || $errors->has("locations.$index.location_type")
                        || $errors->has("locations.$index.day_number")
                        || $errors->has("locations.$index.visit_order")
                        || $errors->has("locations.$index.visit_time")
                        || $errors->has("locations.$index.latitude")
                        || $errors->has("locations.$index.longitude")
                        || $errors->has("locations.$index.google_maps_url")
                        || $errors->has("locations.$index.marker_image")
                        || $errors->has("locations.$index.description")
                        || $errors->has("locations.$index.description_traditional")
                        || $errors->has("locations.$index.description_simplified");
                @endphp
                <div class="tour-location-item @if($compactLocationCards && ! $hasLocationError) is-collapsed @endif" data-tour-location-item>
                    <div class="tour-location-item__summary">
                        <div>
                            <span class="tour-location-day-label">Day <span data-tour-location-day-label>{{ $location['day_number'] ?? 1 }}</span></span>
                            <strong><span data-tour-location-number>{{ $index + 1 }}</span>. <span data-tour-location-title>{{ $location['destination_name'] ?? 'Untitled stop' }}</span></strong>
                            <small><span data-tour-location-time-label>{{ $location['visit_time'] ?? 'No time' }}</span> | <span data-tour-location-type-label>{{ $location['location_type'] ?? 'Attraction' }}</span> | <span data-tour-location-coordinate-label>{{ filled($location['latitude'] ?? null) && filled($location['longitude'] ?? null) ? 'Coordinates available' : 'Coordinates missing' }}</span></small>
                        </div>
                        <div class="tour-location-item__actions">
                            <button type="button" class="backend-icon-action tour-location-drag-handle" data-tour-location-drag-handle draggable="true" aria-label="Move destination">
                                <i class="fa fa-grip-vertical"></i>
                            </button>
                            @if($compactLocationCards)
                                <button type="button" class="backend-button backend-button-secondary" data-toggle-tour-location-editor>
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                            @endif
                            <button type="button" class="backend-icon-action is-danger" data-remove-tour-location>
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row tour-location-editor" data-tour-location-editor>
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
                        <div class="col-md-5 d-none" data-tour-manual-coordinate-field>
                            <div class="backend-form-field">
                                <label>Latitude</label>
                                <input type="number" step="0.0000001" min="-90" max="90" name="locations[{{ $index }}][latitude]" data-field-name="latitude" class="backend-form-control @error("locations.$index.latitude") is-invalid @enderror" value="{{ $location['latitude'] ?? '' }}" placeholder="Auto from Google link or -8.6212130" data-tour-location-latitude>
                                @error("locations.$index.latitude")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 d-none" data-tour-manual-coordinate-field>
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
                                <small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled from selected reference or by resolving a Google Maps URL.</small>
                                @error("locations.$index.google_maps_url")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="backend-form-field">
                                <label>Marker Cover Image</label>
                                <input type="hidden" name="locations[{{ $index }}][existing_marker_image]" value="{{ $location['marker_image'] ?? '' }}" data-field-name="existing_marker_image">
                                <div class="tour-location-marker-control">
                                    <div class="tour-location-marker-control__preview" data-tour-location-image-preview>
                                        @if (!empty($location['marker_image']))
                                            <img src="{{ asset('storage/tours/tour-location-markers/' . $location['marker_image']) }}" alt="Marker image" class="tour-location-marker-preview">
                                        @else
                                            <span>No marker cover selected</span>
                                        @endif
                                    </div>
                                    <div class="tour-location-marker-control__input">
                                        <input type="file" name="locations[{{ $index }}][marker_image]" data-field-name="marker_image" class="backend-form-control @error("locations.$index.marker_image") is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                                        <small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small>
                                    </div>
                                </div>
                                @error("locations.$index.marker_image")
                                    <div class="backend-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <section class="backend-translation-group">
                                <div class="backend-translation-group__header">
                                    <h3 class="backend-translation-group__title">Destination Description</h3>
                                    <p class="backend-translation-group__description">Optional customer-facing stop notes for the map marker and generated itinerary.</p>
                                </div>
                                <div class="backend-translation-grid">
                                    <div class="backend-translation-field">
                                        <label class="backend-form-label">English</label>
                                        <textarea name="locations[{{ $index }}][description]" data-field-name="description" class="backend-form-control @error("locations.$index.description") is-invalid @enderror" data-backend-richtext="true" rows="2" placeholder="Short stop note for marker popup">{{ $location['description'] ?? '' }}</textarea>
                                        @error("locations.$index.description")
                                            <div class="backend-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="backend-translation-field">
                                        <label class="backend-form-label">Traditional Chinese</label>
                                        <textarea name="locations[{{ $index }}][description_traditional]" data-field-name="description_traditional" class="backend-form-control @error("locations.$index.description_traditional") is-invalid @enderror" data-backend-richtext="true" rows="2" placeholder="Insert traditional destination description">{{ $location['description_traditional'] ?? '' }}</textarea>
                                        @error("locations.$index.description_traditional")
                                            <div class="backend-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="backend-translation-field">
                                        <label class="backend-form-label">Simplified Chinese</label>
                                        <textarea name="locations[{{ $index }}][description_simplified]" data-field-name="description_simplified" class="backend-form-control @error("locations.$index.description_simplified") is-invalid @enderror" data-backend-richtext="true" rows="2" placeholder="Insert simplified destination description">{{ $location['description_simplified'] ?? '' }}</textarea>
                                        @error("locations.$index.description_simplified")
                                            <div class="backend-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-tour-location-template>
            <div class="tour-location-item" data-tour-location-item>
                <div class="tour-location-item__summary">
                    <div>
                        <span class="tour-location-day-label">Day <span data-tour-location-day-label>1</span></span>
                        <strong><span data-tour-location-number></span>. <span data-tour-location-title>Untitled stop</span></strong>
                        <small><span data-tour-location-time-label>No time</span> | <span data-tour-location-type-label>Attraction</span> | <span data-tour-location-coordinate-label>Coordinates missing</span></small>
                    </div>
                    <div class="tour-location-item__actions">
                        <button type="button" class="backend-icon-action tour-location-drag-handle" data-tour-location-drag-handle draggable="true" aria-label="Move destination">
                            <i class="fa fa-grip-vertical"></i>
                        </button>
                        @if($compactLocationCards)
                            <button type="button" class="backend-icon-action" data-toggle-tour-location-editor>
                                <i class="fa fa-pencil-alt"></i>
                            </button>
                        @endif
                        <button type="button" class="backend-icon-action is-danger" data-remove-tour-location>
                            <i class="fa fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="row tour-location-editor" data-tour-location-editor>
                    <div class="col-md-5"><div class="backend-form-field"><label>Destination Name</label><input type="hidden" data-field-name="location_reference_id" data-tour-location-reference-id><div class="tour-location-suggest"><input type="text" data-field-name="destination_name" data-tour-location-name class="backend-form-control" placeholder="e.g. Tanah Lot Temple" autocomplete="off"><div class="tour-location-suggest__menu" data-tour-location-suggestions></div></div></div></div>
                    <div class="col-md-3"><div class="backend-form-field"><label>Location Type</label><select data-field-name="location_type" class="backend-form-control"><option value="Attraction">Attraction</option><option value="Activity">Activity</option><option value="F&B">F&amp;B</option><option value="Pickup/Dropoff">Pickup/Dropoff Location</option></select></div></div>
                    <div class="col-md-2"><div class="backend-form-field"><label>Day</label><input type="number" min="1" value="1" data-field-name="day_number" class="backend-form-control"></div></div>
                    <div class="col-md-2"><div class="backend-form-field"><label>Order</label><input type="number" min="1" value="1" data-field-name="visit_order" class="backend-form-control"></div></div>
                    <div class="col-md-3"><div class="backend-form-field"><label>Visit Time</label><input type="time" data-field-name="visit_time" class="backend-form-control"></div></div>
                    <div class="col-md-5 d-none" data-tour-manual-coordinate-field><div class="backend-form-field"><label>Latitude</label><input type="number" step="0.0000001" min="-90" max="90" data-field-name="latitude" data-tour-location-latitude class="backend-form-control" placeholder="Auto from Google link or -8.6212130"></div></div>
                    <div class="col-md-4 d-none" data-tour-manual-coordinate-field><div class="backend-form-field"><label>Longitude</label><input type="number" step="0.0000001" min="-180" max="180" data-field-name="longitude" data-tour-location-longitude class="backend-form-control" placeholder="Auto from Google link or 115.0868060"></div></div>
                    <div class="col-md-8"><div class="backend-form-field"><label>Google Maps Short Link / Coordinate URL</label><input type="url" data-field-name="google_maps_url" data-tour-location-map-url class="backend-form-control" placeholder="https://maps.app.goo.gl/..."><small class="form-text text-muted" data-tour-coordinate-status>Coordinates will be filled from selected reference or by resolving a Google Maps URL.</small></div></div>
                    <div class="col-md-12"><div class="backend-form-field"><label>Marker Cover Image</label><input type="hidden" data-field-name="existing_marker_image"><div class="tour-location-marker-control"><div class="tour-location-marker-control__preview" data-tour-location-image-preview><span>No marker cover selected</span></div><div class="tour-location-marker-control__input"><input type="file" data-field-name="marker_image" class="backend-form-control" accept="image/jpeg,image/png,image/jpg,image/webp"><small class="form-text text-muted">Optional. If empty, marker will use this tour package cover image.</small></div></div></div></div>
                    <div class="col-md-12"><section class="backend-translation-group"><div class="backend-translation-group__header"><h3 class="backend-translation-group__title">Destination Description</h3><p class="backend-translation-group__description">Optional customer-facing stop notes for the map marker and generated itinerary.</p></div><div class="backend-translation-grid"><div class="backend-translation-field"><label class="backend-form-label">English</label><textarea data-field-name="description" class="backend-form-control" data-backend-richtext="true" rows="2" placeholder="Short stop note for marker popup"></textarea></div><div class="backend-translation-field"><label class="backend-form-label">Traditional Chinese</label><textarea data-field-name="description_traditional" class="backend-form-control" data-backend-richtext="true" rows="2" placeholder="Insert traditional destination description"></textarea></div><div class="backend-translation-field"><label class="backend-form-label">Simplified Chinese</label><textarea data-field-name="description_simplified" class="backend-form-control" data-backend-richtext="true" rows="2" placeholder="Insert simplified destination description"></textarea></div></div></section></div>
                </div>
            </div>
        </template>
    </div>
</div>
