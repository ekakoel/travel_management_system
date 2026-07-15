<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Tours;
use Carbon\Carbon;

trait BuildsTourLocationItinerary
{
    protected function buildTourLocationItineraryHtml(Tours $tour, ?string $fallback = null): string
    {
        $locations = $tour->relationLoaded('activeLocations')
            ? $tour->activeLocations
            : $tour->activeLocations()
                ->get([
                    'id',
                    'day_number',
                    'visit_order',
                    'visit_time',
                    'destination_name',
                    'location_type',
                    'description',
                ]);

        if ($locations->isEmpty()) {
            return trim((string) $fallback);
        }

        $dayLabel = __('tour-map.day');
        $dayPrefix = $dayLabel !== 'tour-map.day' ? $dayLabel : 'Day';

        return $locations
            ->groupBy(fn ($location) => (int) $location->day_number)
            ->sortKeys()
            ->map(function ($dayLocations, $dayNumber) use ($dayPrefix) {
                $items = $dayLocations
                    ->sortBy([
                        ['visit_order', 'asc'],
                        ['id', 'asc'],
                    ])
                    ->values()
                    ->map(function ($location) {
                        $timeLabel = $location->visit_time
                            ? Carbon::parse($location->visit_time)->format('H:i')
                            : null;
                        $titleParts = array_filter([
                            $timeLabel ? e($timeLabel) : null,
                            filled($location->destination_name) ? e($location->destination_name) : null,
                            filled($location->location_type) ? '<small>(' . e($location->location_type) . ')</small>' : null,
                        ]);
                        $description = trim(strip_tags((string) $location->description));
                        $html = '<li><p><strong>' . implode(' - ', $titleParts) . '</strong></p>';

                        if ($description !== '') {
                            $html .= '<p>' . nl2br(e($description)) . '</p>';
                        }

                        $html .= '</li>';

                        return $html;
                    })
                    ->implode('');

                return '<section><p><strong>' . e($dayPrefix) . ' ' . e($dayNumber) . '</strong></p><ol>' . $items . '</ol></section>';
            })
            ->implode('');
    }
}
