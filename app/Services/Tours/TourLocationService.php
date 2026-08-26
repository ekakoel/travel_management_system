<?php

namespace App\Services\Tours;

use App\Models\TourLocationReference;
use App\Models\Tours;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TourLocationService
{
    public function __construct(
        private readonly TourAssetService $assets,
    ) {
    }

    public function resolveCoordinates(string $url): ?array
    {
        foreach ($this->candidateMapUrls($url) as $candidateUrl) {
            $coordinates = $this->parseCoordinatesFromMapUrl($candidateUrl);

            if ($coordinates) {
                return $coordinates;
            }
        }

        return null;
    }

    public function allowedGoogleMapsUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, [
            'google.com',
            'www.google.com',
            'maps.google.com',
            'maps.app.goo.gl',
            'goo.gl',
        ], true);
    }

    public function searchReferences(string $query)
    {
        if (Str::length($query) < 2) {
            return collect();
        }

        return TourLocationReference::query()
            ->where('destination_name', 'like', '%' . $query . '%')
            ->orderBy('destination_name')
            ->limit(12)
            ->get()
            ->map(fn (TourLocationReference $location) => [
                'id' => $location->id,
                'destination_name' => $location->destination_name,
                'location_type' => $location->location_type,
                'google_maps_url' => $location->google_maps_url,
                'marker_image' => $location->marker_image,
                'marker_image_url' => $location->marker_image ? asset('storage/tours/tour-location-markers/' . $location->marker_image) : null,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'description' => $location->description,
                'description_traditional' => $location->description_traditional,
                'description_simplified' => $location->description_simplified,
            ]);
    }

    public function validateLocations(Request $request): array
    {
        $locations = (array) $request->input('locations', []);
        $errors = [];
        $normalized = [];

        foreach ($locations as $inputIndex => $location) {
            if (! is_array($location)) {
                $errors["locations.{$inputIndex}"] = 'Each map location must be submitted as a structured location row.';
                continue;
            }

            if ($this->isBlankLocation($location, $request->hasFile("locations.{$inputIndex}.marker_image"))) {
                continue;
            }

            $prefix = "locations.{$inputIndex}";
            $locationReferenceId = $location['location_reference_id'] ?? null;
            $name = trim((string) ($location['destination_name'] ?? ''));
            $locationType = trim((string) ($location['location_type'] ?? 'Attraction'));
            $googleMapsUrl = trim((string) ($location['google_maps_url'] ?? ''));
            $existingMarkerImage = trim((string) ($location['existing_marker_image'] ?? ''));
            $latitude = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;
            $dayNumber = $location['day_number'] ?? null;
            $visitOrder = $location['visit_order'] ?? null;
            $visitTime = $location['visit_time'] ?? null;
            $markerImage = $existingMarkerImage ?: null;
            $markerImageFile = $request->file("locations.{$inputIndex}.marker_image");

            if ($name === '') {
                $errors["{$prefix}.destination_name"] = 'Destination name is required when adding a map location.';
            }

            if (! in_array($locationType, ['Attraction', 'Activity', 'F&B', 'Pickup/Dropoff'], true)) {
                $errors["{$prefix}.location_type"] = 'Location type must be Attraction, Activity, F&B, or Pickup/Dropoff.';
            }

            if (! filter_var($dayNumber, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                $errors["{$prefix}.day_number"] = 'Day number must be a positive integer.';
            }

            if (! filter_var($visitOrder, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                $errors["{$prefix}.visit_order"] = 'Visit order must be a positive integer.';
            }

            if (filled($visitTime) && ! preg_match('/^\d{2}:\d{2}$/', (string) $visitTime)) {
                $errors["{$prefix}.visit_time"] = 'Visit time must use HH:mm format.';
            }

            if ((! is_numeric($latitude) || ! is_numeric($longitude)) && $googleMapsUrl !== '' && $this->allowedGoogleMapsUrl($googleMapsUrl)) {
                $coordinates = $this->resolveCoordinates($googleMapsUrl);

                if ($coordinates) {
                    $latitude = $coordinates['latitude'];
                    $longitude = $coordinates['longitude'];
                }
            }

            if (! is_numeric($latitude) || (float) $latitude < -90 || (float) $latitude > 90) {
                $errors["{$prefix}.latitude"] = 'Latitude must be a number between -90 and 90, or use a Google Maps URL that exposes coordinates.';
            }

            if (! is_numeric($longitude) || (float) $longitude < -180 || (float) $longitude > 180) {
                $errors["{$prefix}.longitude"] = 'Longitude must be a number between -180 and 180, or use a Google Maps URL that exposes coordinates.';
            }

            if ($googleMapsUrl !== '' && ! $this->allowedGoogleMapsUrl($googleMapsUrl)) {
                $errors["{$prefix}.google_maps_url"] = 'Google Maps link must be a valid Google Maps URL.';
            }

            if ($markerImageFile) {
                if (! $markerImageFile->isValid() || ! in_array($markerImageFile->extension(), ['jpg', 'jpeg', 'png', 'webp'], true) || $markerImageFile->getSize() > 2048 * 1024) {
                    $errors["{$prefix}.marker_image"] = 'Marker image must be a valid JPG, PNG, or WEBP image with maximum size 2MB.';
                }
            }

            $normalized[] = [
                'location_reference_id' => $locationReferenceId ? (int) $locationReferenceId : null,
                'destination_name' => $name,
                'location_type' => $locationType,
                'google_maps_url' => $googleMapsUrl ?: null,
                'marker_image' => $markerImage,
                'latitude' => is_numeric($latitude) ? round((float) $latitude, 7) : null,
                'longitude' => is_numeric($longitude) ? round((float) $longitude, 7) : null,
                'day_number' => (int) $dayNumber,
                'visit_order' => (int) $visitOrder,
                'visit_time' => filled($visitTime) ? $visitTime : null,
                'description' => filled($location['description'] ?? null) ? trim((string) $location['description']) : null,
                'description_traditional' => filled($location['description_traditional'] ?? null) ? trim((string) $location['description_traditional']) : null,
                'description_simplified' => filled($location['description_simplified'] ?? null) ? trim((string) $location['description_simplified']) : null,
                'is_active' => true,
                '_marker_image_file' => $markerImageFile instanceof UploadedFile ? $markerImageFile : null,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $normalized;
    }

    public function sync(Tours $tour, array $locations): void
    {
        $uploadedMarkers = [];
        $oldMarkers = $tour->locations()
            ->whereNotNull('marker_image')
            ->pluck('marker_image')
            ->filter()
            ->values()
            ->all();
        $retainedMarkers = collect($locations)
            ->pluck('marker_image')
            ->filter()
            ->values()
            ->all();

        $tour->locations()->delete();

        try {
            foreach ($locations as $location) {
                $markerImageFile = $location['_marker_image_file'] ?? null;
                unset($location['_marker_image_file']);

                if ($markerImageFile instanceof UploadedFile) {
                    $location['marker_image'] = $this->assets->uploadMarker($markerImageFile);
                    $uploadedMarkers[] = $location['marker_image'];
                }

                $location['location_reference_id'] = $this->syncReference($location);
                $tour->locations()->create($location);
            }

            $markersToDelete = array_values(array_diff($oldMarkers, $retainedMarkers, $uploadedMarkers));

            if ($markersToDelete) {
                DB::afterCommit(function () use ($markersToDelete): void {
                    $referencedMarkers = TourLocationReference::query()
                        ->whereIn('marker_image', $markersToDelete)
                        ->pluck('marker_image')
                        ->all();

                    foreach ($markersToDelete as $marker) {
                        if (in_array($marker, $referencedMarkers, true)) {
                            continue;
                        }

                        $this->assets->deleteMarker($marker);
                    }
                });
            }
        } catch (\Throwable $exception) {
            foreach ($uploadedMarkers as $marker) {
                $this->assets->deleteMarker($marker);
            }

            throw $exception;
        }
    }

    private function isBlankLocation(array $location, bool $hasMarkerFile): bool
    {
        if ($hasMarkerFile) {
            return false;
        }

        $meaningfulFields = [
            'location_reference_id',
            'destination_name',
            'google_maps_url',
            'marker_image',
            'existing_marker_image',
            'latitude',
            'longitude',
            'visit_time',
            'description',
            'description_traditional',
            'description_simplified',
        ];

        foreach ($meaningfulFields as $field) {
            if (filled($location[$field] ?? null)) {
                return false;
            }
        }

        $locationType = trim((string) ($location['location_type'] ?? ''));

        return $locationType === '' || $locationType === 'Attraction';
    }

    private function syncReference(array $location): int
    {
        if (! empty($location['location_reference_id'])) {
            $reference = TourLocationReference::find($location['location_reference_id']);

            if ($reference
                && $reference->destination_name === $location['destination_name']
                && $reference->location_type === $location['location_type']
                && round((float) $reference->latitude, 7) === round((float) $location['latitude'], 7)
                && round((float) $reference->longitude, 7) === round((float) $location['longitude'], 7)
            ) {
                $reference->fill([
                    'description' => $location['description'],
                    'description_traditional' => $location['description_traditional'],
                    'description_simplified' => $location['description_simplified'],
                ])->save();

                return $reference->id;
            }
        }

        $lookupKey = TourLocationReference::lookupKey(
            $location['destination_name'],
            $location['location_type'],
            (float) $location['latitude'],
            (float) $location['longitude']
        );

        $reference = TourLocationReference::updateOrCreate(
            ['lookup_key' => $lookupKey],
            [
                'destination_name' => $location['destination_name'],
                'location_type' => $location['location_type'],
                'google_maps_url' => $location['google_maps_url'],
                'marker_image' => $location['marker_image'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'description' => $location['description'],
                'description_traditional' => $location['description_traditional'],
                'description_simplified' => $location['description_simplified'],
            ]
        );

        return $reference->id;
    }

    private function candidateMapUrls(string $url): array
    {
        $urls = [$url];

        try {
            $response = Http::timeout(4)
                ->connectTimeout(3)
                ->withHeaders(['User-Agent' => 'BaliKamiTour/1.0'])
                ->get($url);

            $effectiveUrl = method_exists($response, 'effectiveUri')
                ? (string) $response->effectiveUri()
                : null;

            if ($effectiveUrl) {
                $urls[] = $effectiveUrl;
            }

            if ($response->header('Location')) {
                $urls[] = $response->header('Location');
            }

            if ($response->body()) {
                $urls[] = $response->body();
            }
        } catch (\Throwable $exception) {
            // Short Google Maps URLs may require external redirect resolution.
        }

        return array_values(array_unique(array_filter($urls)));
    }

    private function parseCoordinatesFromMapUrl(string $value): ?array
    {
        $decoded = urldecode($value);

        foreach ([
            '/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/[?&](?:q|ll)=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/',
        ] as $pattern) {
            if (preg_match($pattern, $decoded, $matches)) {
                $latitude = (float) $matches[1];
                $longitude = (float) $matches[2];

                if ($latitude >= -90 && $latitude <= 90 && $longitude >= -180 && $longitude <= 180) {
                    return [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                }
            }
        }

        return null;
    }
}
