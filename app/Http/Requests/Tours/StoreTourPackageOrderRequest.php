<?php

namespace App\Http\Requests\Tours;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourPackageOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $guests = $this->input('guests', []);

        $this->merge([
            // Pax is authoritative from the submitted manifest, never from a separate UI field.
            'number_of_guests' => is_array($guests) ? count($guests) : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'submission_token' => ['required', 'string', 'max:120'],
            'number_of_guests' => ['required', 'integer', 'min:2', 'max:200'],
            'tour_price_id' => ['nullable', 'integer', 'exists:tour_prices,id'],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
            'booking_code' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'guest_detail' => ['nullable', 'string'],
            'special_request' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'guests' => ['required', 'array', 'min:2', 'max:200'],
            'guests.*.name' => ['required', 'string', 'max:255'],
            'guests.*.phone' => ['nullable', 'string', 'max:50'],
            'guests.*.age' => ['required', 'in:Adult,Child'],
            'guests.*.sex' => ['required', 'in:Male,Female'],
            'terms_accepted' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'submission_token.required' => __('tour-detail.validation.submission_token_required'),
            'number_of_guests.min' => __('tour-detail.validation.guest_min', ['min' => 2]),
            'number_of_guests.max' => __('tour-detail.validation.guest_max', ['max' => 200]),
            'travel_date.required' => __('tour-detail.validation.travel_date_required'),
            'travel_date.date' => __('tour-detail.validation.travel_date_invalid'),
            'travel_date.after_or_equal' => __('tour-detail.validation.travel_date_future'),
            'pickup_location.required' => __('tour-detail.validation.pickup_required'),
            'dropoff_location.required' => __('tour-detail.validation.dropoff_required'),
            'guests.required' => __('tour-detail.validation.guests_required'),
            'guests.array' => __('tour-detail.validation.guests_invalid'),
            'guests.min' => __('tour-detail.validation.guest_min', ['min' => 2]),
            'guests.max' => __('tour-detail.validation.guest_max', ['max' => 200]),
            'guests.*.name.required' => __('tour-detail.validation.guest_name_required'),
            'guests.*.age.required' => __('tour-detail.validation.guest_age_required'),
            'guests.*.age.in' => __('tour-detail.validation.guest_age_invalid'),
            'guests.*.sex.required' => __('tour-detail.validation.guest_gender_required'),
            'guests.*.sex.in' => __('tour-detail.validation.guest_gender_invalid'),
            'terms_accepted.accepted' => __('tour-detail.validation.terms_required'),
        ];
    }
}
