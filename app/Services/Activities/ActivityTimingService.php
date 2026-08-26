<?php

namespace App\Services\Activities;

use App\Models\Activities;
use Carbon\Carbon;

class ActivityTimingService
{
    private const DEFAULT_DURATION_MINUTES = 60;

    public function resolve(Activities $activity, Carbon $activityStart): array
    {
        $start = $activityStart->copy();
        $end = $start->copy()->addMinutes($this->durationMinutes($activity->duration));

        return [
            'activity_start' => $start,
            'activity_end' => $end,
            'pickup_date' => $start->copy(),
            'dropoff_date' => $end->copy(),
        ];
    }

    public function durationMinutes(?string $duration): int
    {
        $value = trim((string) $duration);

        if ($value === '') {
            return self::DEFAULT_DURATION_MINUTES;
        }

        if (! preg_match('/(\d+(?:[.,]\d+)?)\s*([a-zA-Z]*)/', $value, $matches)) {
            return self::DEFAULT_DURATION_MINUTES;
        }

        $amount = (float) str_replace(',', '.', $matches[1]);
        $unit = strtolower($matches[2] ?: 'hour');

        if ($amount <= 0) {
            return self::DEFAULT_DURATION_MINUTES;
        }

        $minutes = match (true) {
            str_starts_with($unit, 'min') => $amount,
            str_starts_with($unit, 'hr'),
            str_starts_with($unit, 'hour') => $amount * 60,
            str_starts_with($unit, 'day') => $amount * 1440,
            default => $amount * 60,
        };

        return max((int) round($minutes), 1);
    }
}
