<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class StoreHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor', 'posAdm']);
    }

    public function rules(): array
    {
        return [
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'hotel_context' => ['required', 'string'],
            'cover' => ['required', 'image', 'max:4096'],
            'rooms' => ['required', 'string', 'max:255'],
            'room_view' => ['required', 'string', 'max:100'],
            'custom_room_view' => ['required_if:room_view,custom', 'nullable', 'string', 'max:100'],
            'beds' => ['required', 'string', 'max:100'],
            'custom_beds' => ['required_if:beds,custom', 'nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'capacity_adult' => ['required', 'integer', 'min:1'],
            'capacity_child' => ['nullable', 'integer', 'min:0'],
            'inventory' => ['required', 'integer', 'min:1'],
            'include' => ['nullable', 'string'],
            'include_traditional' => ['nullable', 'string'],
            'include_simplified' => ['nullable', 'string'],
            'amenities' => ['nullable', 'string'],
            'amenities_traditional' => ['nullable', 'string'],
            'amenities_simplified' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'additional_info_traditional' => ['nullable', 'string'],
            'additional_info_simplified' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hotelId = $this->resolvedHotelId();

            if (! $hotelId) {
                $validator->errors()->add('hotels_id', 'The selected Hotel context is invalid.');

                return;
            }

            if ((int) $this->input('hotels_id') !== $hotelId) {
                $validator->errors()->add('hotels_id', 'The selected Hotel does not match this Room form context.');
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
}
