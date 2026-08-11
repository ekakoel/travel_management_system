<?php

namespace App\Services\Reservations;

use App\Models\LogData;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationAdminService
{
    public function indexData(User $admin): array
    {
        $today = Carbon::today();
        $reservations = Reservation::query()
            ->with([
                'agent:id,name,office,code',
                'invoice:id,rsv_id,inv_no,due_date',
            ])
            ->withCount(['guests', 'orders', 'spks'])
            ->whereNull('deleted_at')
            ->where('adm_id', $admin->id)
            ->where('status', 'Active')
            ->latest('created_at')
            ->get();

        $rows = $reservations->map(function (Reservation $reservation) use ($today) {
            $invoice = $reservation->invoice;
            $dueDate = $this->date($invoice?->due_date);
            $startDate = $this->date($reservation->checkin);
            $endDate = $this->date($reservation->checkout);
            $status = 'Active';

            return [
                'id' => $reservation->id,
                'number' => $reservation->rsv_no ?: '#'.$reservation->id,
                'agent' => $reservation->agent?->name ?: 'Unassigned agent',
                'agent_office' => $reservation->agent?->office,
                'service' => $reservation->service ?: 'Reservation',
                'period' => $this->period($reservation->checkin, $reservation->checkout),
                'start_date' => $startDate?->format('Y-m-d'),
                'end_date' => $endDate?->format('Y-m-d'),
                'invoice' => $invoice?->inv_no,
                'due_date' => $dueDate?->format('d M Y'),
                'is_overdue' => $dueDate?->lt($today),
                'is_upcoming' => $startDate?->gt($today) ?? false,
                'is_in_service' => ($startDate?->lte($today) ?? false)
                    && ($endDate?->gte($today) ?? true),
                'status' => $status,
                'status_tone' => $this->statusTone($status),
                'guest_count' => (int) $reservation->guests_count,
                'spk_count' => (int) $reservation->spks_count,
                'calendar_note' => $this->calendarNote($reservation, $dueDate?->lt($today) ?? false, $startDate, $endDate, $today),
                'can_delete' => strtolower($status) === 'draft'
                    && ! $invoice
                    && (int) $reservation->orders_count === 0
                    && (int) $reservation->guests_count === 0,
                'search' => mb_strtolower(implode(' ', array_filter([
                    $reservation->rsv_no,
                    $reservation->agent?->name,
                    $reservation->agent?->office,
                    $reservation->service,
                    $invoice?->inv_no,
                    $status,
                ]))),
            ];
        });

        return [
            'reservationRows' => $rows,
            'reservationCalendarEvents' => $rows
                ->whereNotNull('start_date')
                ->map(fn (array $row) => [
                    'id' => (string) $row['id'],
                    'title' => $row['number'].' · '.$row['service'],
                    'start' => $row['start_date'],
                    // FullCalendar uses an exclusive end date for all-day events.
                    'end' => Carbon::parse($row['end_date'] ?: $row['start_date'])->addDay()->format('Y-m-d'),
                    'allDay' => true,
                    'tone' => $row['is_overdue'] ? 'overdue' : ($row['is_in_service'] ? 'in-service' : 'active'),
                    'search' => $row['search'],
                    'serviceKey' => mb_strtolower($row['service']),
                    'reference' => $row['number'],
                    'agent' => $row['agent'],
                    'service' => $row['service'],
                    'period' => $row['period'],
                    'guestCount' => $row['guest_count'],
                    'spkCount' => $row['spk_count'],
                    'invoice' => $row['invoice'] ?: __('reservations.not_generated'),
                    'dueDate' => $row['due_date'] ?: __('reservations.not_generated'),
                    'note' => $row['calendar_note'],
                    'detailUrl' => route('view.reservation.detail', $row['id']),
                ])
                ->values(),
            'reservationCalendarSettings' => [
                'today' => __('reservations.calendar_today'),
                'month' => __('reservations.calendar_month'),
                'week' => __('reservations.calendar_week'),
                'list' => __('reservations.calendar_list'),
                'allDay' => __('reservations.calendar_all_day'),
                'more' => __('reservations.calendar_more'),
                'empty' => __('reservations.calendar_empty'),
                'monthNames' => __('reservations.calendar_month_names'),
                'monthNamesShort' => __('reservations.calendar_month_names_short'),
                'dayNames' => __('reservations.calendar_day_names'),
                'dayNamesShort' => __('reservations.calendar_day_names_short'),
            ],
            'reservationStats' => [
                ['label' => __('reservations.active'), 'value' => $rows->where('status', 'Active')->count(), 'meta' => __('reservations.active_meta'), 'icon' => 'fa fa-check-circle', 'tone' => 'green'],
                ['label' => __('reservations.upcoming'), 'value' => $rows->where('is_upcoming', true)->count(), 'meta' => __('reservations.upcoming_meta'), 'icon' => 'fa fa-calendar', 'tone' => 'blue'],
                ['label' => __('reservations.in_service'), 'value' => $rows->where('is_in_service', true)->count(), 'meta' => __('reservations.in_service_meta'), 'icon' => 'fa fa-briefcase', 'tone' => 'teal'],
                ['label' => __('reservations.overdue'), 'value' => $rows->where('is_overdue', true)->count(), 'meta' => __('reservations.overdue_meta'), 'icon' => 'fa fa-exclamation-triangle', 'tone' => 'red'],
            ],
            'reservationServices' => $rows->pluck('service')->filter()->unique()->sort()->values(),
            'now' => Carbon::now(),
        ];
    }

    public function createManual(array $validated, User $admin): Reservation
    {
        return DB::transaction(function () use ($validated, $admin) {
            $agent = User::query()
                ->where('type', 'user')
                ->where('position', 'agent')
                ->where('status', 'Active')
                ->lockForUpdate()
                ->findOrFail($validated['agn_id']);

            $prefix = trim((string) $agent->code).now()->format('ymd');
            $number = $this->nextReservationNumber($prefix);
            $reservation = Reservation::create([
                'rsv_no' => $number,
                'service' => 'Reservation',
                'checkin' => $validated['checkin'],
                'checkout' => $validated['checkout'],
                'agn_id' => $agent->id,
                'adm_id' => $admin->id,
                'status' => 'Draft',
            ]);

            LogData::create([
                'service' => 'Reservation',
                'action' => 'Create Reservation',
                'user_id' => $admin->id,
            ]);

            return $reservation;
        });
    }

    public function deleteManualDraft(Reservation $reservation, User $admin): void
    {
        if ((int) $reservation->adm_id !== (int) $admin->id && $admin->position !== 'developer') {
            abort(403);
        }

        if (
            strtolower((string) $reservation->status) !== 'draft'
            || $reservation->service !== 'Reservation'
            || $reservation->invoice()->exists()
            || $reservation->orders()->exists()
            || $reservation->guests()->exists()
        ) {
            throw ValidationException::withMessages([
                'reservation' => 'Only an empty manual Draft reservation can be removed.',
            ]);
        }

        DB::transaction(function () use ($reservation, $admin) {
            LogData::create([
                'service' => 'Reservation',
                'action' => 'Remove Draft Reservation '.$reservation->rsv_no,
                'user_id' => $admin->id,
            ]);
            $reservation->delete();
        });
    }

    private function nextReservationNumber(string $prefix): string
    {
        $used = Reservation::query()
            ->where('rsv_no', 'like', $prefix.'%')
            ->lockForUpdate()
            ->pluck('rsv_no')
            ->map(fn ($number) => substr((string) $number, strlen($prefix)))
            ->filter()
            ->flip();

        for ($index = 1; ; $index++) {
            $suffix = $this->alphabeticSuffix($index);

            if (! $used->has($suffix)) {
                return $prefix.$suffix;
            }
        }
    }

    private function alphabeticSuffix(int $index): string
    {
        $suffix = '';

        while ($index > 0) {
            $index--;
            $suffix = chr(65 + ($index % 26)).$suffix;
            $index = intdiv($index, 26);
        }

        return $suffix;
    }

    private function period($checkin, $checkout): string
    {
        $start = $this->date($checkin);
        $end = $this->date($checkout);

        if (! $start && ! $end) {
            return 'Dates not set';
        }

        if (! $start || ! $end || $start->isSameDay($end)) {
            return ($start ?: $end)->format('d M Y');
        }

        return $start->format('d M Y').' – '.$end->format('d M Y');
    }

    private function date($value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function calendarNote(
        Reservation $reservation,
        bool $isOverdue,
        ?Carbon $startDate,
        ?Carbon $endDate,
        Carbon $today
    ): string {
        $storedNote = Str::squish(strip_tags(html_entity_decode((string) $reservation->additional_info)));

        if ($storedNote !== '') {
            return Str::limit($storedNote, 240);
        }

        if ($isOverdue) {
            return __('reservations.calendar_note_overdue');
        }

        if (($startDate?->lte($today) ?? false) && ($endDate?->gte($today) ?? true)) {
            return __('reservations.calendar_note_in_service');
        }

        if ($startDate?->gt($today)) {
            return __('reservations.calendar_note_upcoming');
        }

        return __('reservations.calendar_note_active');
    }

    private function statusTone(string $status): string
    {
        return match (strtolower($status)) {
            'active' => 'active',
            'pending', 'on progress' => 'pending',
            'completed' => 'completed',
            'canceled', 'cancelled' => 'canceled',
            'draft' => 'draft',
            default => 'muted',
        };
    }
}
