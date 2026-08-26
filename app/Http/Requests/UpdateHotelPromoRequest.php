<?php

namespace App\Http\Requests;

use App\Models\HotelRoom;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateHotelPromoRequest extends FormRequest
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
            'rooms_id' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), $this->roomBelongsToHotelRule()],
            'name' => ['required', 'string', 'max:255'],
            'promotion_type' => ['nullable', 'string', Rule::in(['Hot Deal', 'Best Choice', 'Best Price', 'Special Offer'])],
            'booking_code' => ['nullable', 'string', 'max:255'],
            'book_periode_start' => ['required', 'date'],
            'book_periode_end' => ['required', 'date', 'after_or_equal:book_periode_start'],
            'periode_start' => ['required', 'date'],
            'periode_end' => ['required', 'date', 'after_or_equal:periode_start'],
            'minimum_stay' => ['required', 'integer', 'min:1'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
            'markup' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Active', 'Draft'])],
            'quotes' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'benefits_traditional' => ['nullable', 'string'],
            'benefits_simplified' => ['nullable', 'string'],
            'include' => ['nullable', 'string'],
            'include_traditional' => ['nullable', 'string'],
            'include_simplified' => ['nullable', 'string'],
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
                $validator->errors()->add('hotels_id', 'The selected Hotel does not match this promo form context.');
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
