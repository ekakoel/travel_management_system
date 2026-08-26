<?php

namespace App\Http\Requests;

use App\Models\HotelRoom;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHotelNormalPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor']);
    }

    public function rules(): array
    {
        return [
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'hotel_context' => ['required', 'string'],
            'rooms_id' => ['required', 'array', 'min:1'],
            'rooms_id.*' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), $this->roomBelongsToHotelRule()],
            'start_date' => ['required', 'array', 'min:1'],
            'start_date.*' => ['required', 'date'],
            'end_date' => ['required', 'array', 'min:1'],
            'end_date.*' => ['required', 'date'],
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
            $hotelId = $this->resolvedHotelId();

            if (! $hotelId) {
                $validator->errors()->add('hotels_id', 'The selected Hotel context is invalid.');

                return;
            }

            if ((int) $this->input('hotels_id') !== $hotelId) {
                $validator->errors()->add('hotels_id', 'The selected Hotel does not match this price form context.');
            }

            foreach ($fields as $field) {
                if (count((array) $this->input($field, [])) !== $expected) {
                    $validator->errors()->add($field, 'Every room price row must contain a complete pricing value.');
                }
            }

            foreach ((array) $this->input('start_date', []) as $index => $startDate) {
                $endDate = $this->input("end_date.{$index}");

                if ($startDate && $endDate && strtotime($endDate) < strtotime($startDate)) {
                    $validator->errors()->add("end_date.{$index}", 'The end date must be a date after or equal to the start date.');
                }
            }
        });
    }

    public function resolvedHotelId(): ?int
    {
        try {
            $hotelId = (int) Crypt::decryptString((string) $this->input('hotel_context'));
        } catch (DecryptException) {
            return null;
        }

        return $hotelId > 0 ? $hotelId : null;
    }

    private function roomBelongsToHotelRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $hotelId = $this->resolvedHotelId() ?: (int) $this->input('hotels_id');

            if (! HotelRoom::where('id', $value)->where('hotels_id', $hotelId)->exists()) {
                $fail('The selected room does not belong to this hotel.');
            }
        };
    }
}
