<?php

namespace App\Http\Requests;

use App\Models\HotelRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHotelNormalPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'rooms_id' => ['required', 'array', 'min:1'],
            'rooms_id.*' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), $this->roomBelongsToHotelRule()],
            'start_date' => ['required', 'array', 'min:1'],
            'start_date.*' => ['required', 'date'],
            'end_date' => ['required', 'array', 'min:1'],
            'end_date.*' => ['required', 'date', 'after_or_equal:start_date.*'],
            'contract_rate' => ['required', 'array', 'min:1'],
            'contract_rate.*' => ['required', 'numeric', 'min:0'],
            'markup' => ['required', 'array', 'min:1'],
            'markup.*' => ['required', 'numeric', 'min:0'],
            'kick_back' => ['nullable', 'array'],
            'kick_back.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $fields = ['start_date', 'end_date', 'contract_rate', 'markup'];
            $expected = count((array) $this->input('rooms_id', []));

            foreach ($fields as $field) {
                if (count((array) $this->input($field, [])) !== $expected) {
                    $validator->errors()->add($field, 'Every room price row must contain a complete pricing value.');
                }
            }
        });
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
