<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HotelRoom;
use Illuminate\Validation\Rule;

class UpdateHotelPackageRequest extends FormRequest
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
            'duration' => ['required', 'integer', 'min:1'],
            'stay_period_start' => ['required', 'date'],
            'stay_period_end' => ['required', 'date', 'after_or_equal:stay_period_start'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
            'markup' => ['required', 'numeric', 'min:0'],
            'booking_code' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
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
