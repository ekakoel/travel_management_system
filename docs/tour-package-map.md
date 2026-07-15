# Tour Package Map Display

## Purpose

Tour Package Map Display adds structured destination points to every tour package so the frontend detail page can show a clear route overview. The map uses Leaflet with OpenStreetMap tiles and does not call Google Maps APIs. Google Maps URLs are stored only as optional external reference links.

## Data Model

Locations are stored in `tour_package_locations` and linked to `tours.id`.

Key fields:

- `tour_id`: owner tour package.
- `itinerary_id`: optional future link to structured itinerary rows.
- `day_number`: itinerary day shown to the user.
- `visit_order`: stop order within the day.
- `visit_time`: optional planned visit time shown on the marker legend and popup.
- `destination_name`: public stop label.
- `location_type`: marker category. Supported values are `Attraction`, `Activity`, and `F&B`.
- `latitude` and `longitude`: required map coordinates.
- `google_maps_url`: optional trusted Google Maps reference URL.
- `marker_image`: optional image for the circular map marker.
- `description`: optional public note for the stop.
- `is_active`: controls frontend visibility.

## Admin Usage

The tour create and edit pages include a Map Locations repeater. Admin users can add multiple stops, reorder them by day and visit order, and disable individual stops without deleting the tour.

Validation rules:

- Empty repeater rows are ignored.
- Destination name is required when a row has any value.
- Latitude must be between `-90` and `90`.
- Longitude must be between `-180` and `180`.
- Day number and visit order must be positive integers.
- Visit time uses `HH:mm`.
- Google Maps links must use trusted Google Maps hosts.
- If latitude and longitude are empty, the system attempts to extract coordinates from the Google Maps URL. Full coordinate URLs such as `?q=lat,lng`, `@lat,lng`, or `!3dlat!4dlng` are the most reliable. Short links such as `maps.app.goo.gl` are resolved with a short timeout when the server can access Google redirects; if resolution fails, latitude and longitude must be entered manually.
- Marker image is optional. If empty, the frontend uses the tour package cover image as the marker image.
- Location type controls the frontend marker icon and color. Existing rows default to `Attraction` until an admin changes the type.

Tour data and map locations are saved in one database transaction to avoid partial saves.

## Frontend Behavior

The tour detail page loads active locations through the `activeLocations` relation. The controller filters invalid coordinates and sends a safe JSON payload to the Blade view.

The view renders:

- A Leaflet map only when valid locations exist.
- Numbered markers for each stop.
- Circular icon markers based on location type: attraction, activity, or food and beverage.
- Visit time in the marker popup and planned stop list when available.
- A dashed polyline showing planned visit sequence.
- A responsive legend with day, stop order, description, and optional Google Maps link.

The polyline is an itinerary sequence guide, not a live road navigation route.

## Files

- `database/migrations/2026_07_07_000001_create_tour_package_locations_table.php`
- `app/Models/TourPackageLocation.php`
- `app/Models/Tours.php`
- `app/Http/Controllers/ToursAdminController.php`
- `app/Http/Controllers/ToursController.php`
- `resources/views/backend/tours/partials/tour-location-repeater.blade.php`
- `resources/views/backend/tours/create-tour.blade.php`
- `resources/views/backend/tours/update-tour.blade.php`
- `resources/views/frontend/tours/detail.blade.php`
- `resources/lang/en/tour-map.php`
- `resources/lang/zh/tour-map.php`
- `resources/lang/zh-CN/tour-map.php`

## Future Improvements

- Connect locations to structured itinerary rows when the itinerary module is standardized.
- Add optional route calculation through a routing service such as OSRM if real road distance is required.
- Move inline map assets into a dedicated frontend bundle when the tour detail page is migrated away from the legacy panel layout.
