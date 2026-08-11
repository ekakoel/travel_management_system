<?php

namespace App\Http\Requests\Tours;

use Illuminate\Foundation\Http\FormRequest;

class QuoteTourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number_of_guests' => ['required', 'integer', 'min:2', 'max:200'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'tour_price_id' => ['nullable', 'integer'],
            'promotion_id' => ['nullable', 'integer'],
            'booking_code' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'number_of_guests.required' => __('tour-detail.validation.guests_required'),
            'number_of_guests.min' => __('tour-detail.validation.guest_min', ['min' => 2]),
            'number_of_guests.max' => __('tour-detail.validation.guest_max', ['max' => 200]),
            'travel_date.required' => __('tour-detail.validation.travel_date_required'),
            'travel_date.date' => __('tour-detail.validation.travel_date_invalid'),
            'travel_date.after_or_equal' => __('tour-detail.validation.travel_date_future'),
        ];
    }
}
