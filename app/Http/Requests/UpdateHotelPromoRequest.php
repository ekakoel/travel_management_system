<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HotelRoom;
use Illuminate\Validation\Rule;

class UpdateHotelPromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'rooms_id' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), $this->roomBelongsToHotelRule()],
            'name' => ['required', 'string', 'max:255'],
            'promotion_type' => ['nullable', 'string', 'max:255'],
            'booking_code' => ['nullable', 'string', 'max:255'],
            'book_periode_start' => ['required', 'date'],
            'book_periode_end' => ['required', 'date', 'after_or_equal:book_periode_start'],
            'periode_start' => ['required', 'date'],
            'periode_end' => ['required', 'date', 'after_or_equal:periode_start'],
            'minimum_stay' => ['required', 'integer', 'min:1'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
            'markup' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Active', 'Draft'])],
        ];
    }

    private function roomBelongsToHotelRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! HotelRoom::where('id', $value)->where('hotels_id', $this->input('hotels_id'))->exists()) {
                $fail('The selected room does not belong to this hotel.');
            }
        };
    }
}
