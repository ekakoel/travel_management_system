<?php

namespace App\Http\Requests\Activities;

use App\Services\Activities\ActivityGuestListService;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pickup_location' => is_string($this->input('pickup_location')) ? trim($this->input('pickup_location')) : $this->input('pickup_location'),
            'dropoff_location' => is_string($this->input('dropoff_location')) ? trim($this->input('dropoff_location')) : $this->input('dropoff_location'),
        ]);
    }

    public function rules(): array
    {
        return [
            'submission_token' => ['required', 'string', 'max:120'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:200'],
            'travel_date' => ['required', 'date', 'after_or_equal:now'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'guests' => ['nullable', 'array', 'max:10'],
            'guests.*.name' => ['nullable', 'string', 'max:255'],
            'guests.*.phone' => ['nullable', 'string', 'max:50'],
            'guests.*.age' => ['nullable', 'in:Adult,Child'],
            'guests.*.sex' => ['nullable', 'in:Male,Female'],
            'guests.*.date_of_birth' => ['nullable', 'date'],
            'guests.*.identification_type' => ['nullable', 'string', 'max:50'],
            'guests.*.identification_no' => ['nullable', 'string', 'max:100'],
            'guest_list' => [
                'nullable',
                'file',
                'mimes:csv,txt,xlsx',
                'max:'.ActivityGuestListService::MAX_UPLOAD_KILOBYTES,
            ],
            'note' => ['nullable', 'string'],
            'activity_order_source' => ['nullable', 'string'],
            'terms_accepted' => ['accepted'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $guestCount = (int) $this->input('number_of_guests');

            if ($guestCount > ActivityGuestListService::MANUAL_THRESHOLD) {
                if (! $this->hasFile('guest_list')) {
                    $validator->errors()->add(
                        'guest_list',
                        __('activities.detail.order.guest_list_required')
                    );
                }

                return;
            }

            $manualGuests = collect($this->input('guests', []))
                ->filter(fn ($guest) => collect((array) $guest)->contains(fn ($value) => trim((string) $value) !== ''));

            if ($manualGuests->isEmpty() || $manualGuests->count() > $guestCount) {
                $validator->errors()->add(
                    'guests',
                    __('activities.detail.order.guest_count_mismatch')
                );

                return;
            }

            foreach ($manualGuests as $index => $guest) {
                $row = ((int) $index) + 1;

                if (blank($guest['name'] ?? null)) {
                    $validator->errors()->add(
                        'guests',
                        __('activities.detail.order.guest_name_required', ['row' => $row])
                    );

                    return;
                }

                if (blank($guest['age'] ?? null)) {
                    $validator->errors()->add(
                        'guests',
                        __('activities.detail.order.guest_age_category_invalid', ['row' => $row])
                    );

                    return;
                }

                if (blank($guest['sex'] ?? null)) {
                    $validator->errors()->add(
                        'guests',
                        __('activities.detail.order.guest_sex_invalid', ['row' => $row])
                    );

                    return;
                }
            }
        });
    }
}
