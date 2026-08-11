<?php

namespace App\Http\Requests\Hotels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAccommodationOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ((int) $this->input('hotel_booking_version') !== 2) {
            return;
        }

        $adults = array_values((array) $this->input('room_adults', []));
        $children = array_values((array) $this->input('room_children', []));

        $this->merge([
            'number_of_guests' => collect($adults)
                ->map(fn ($adultCount, $index) => (int) $adultCount + (int) ($children[$index] ?? 0))
                ->values()
                ->all(),
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'terms_accepted' => ['accepted'],
        ];

        if ((int) $this->input('hotel_booking_version') !== 2) {
            return $rules;
        }

        return array_merge($rules, [
            'hotel_booking_version' => ['required', 'integer', 'in:2'],
            'room_adults' => ['required', 'array', 'min:1', 'max:30'],
            'room_adults.*' => ['required', 'integer', 'min:1', 'max:20'],
            'room_children' => ['required', 'array', 'size:' . count((array) $this->input('room_adults', []))],
            'room_children.*' => ['required', 'integer', 'min:0', 'max:20'],
            'room_child_ages' => ['nullable', 'array'],
            'room_child_ages.*' => ['nullable', 'array'],
            'room_child_ages.*.*' => ['required', 'integer', 'min:0', 'max:17'],
            'guest_name' => ['required', 'array', 'min:1', 'max:600'],
            'guest_name.*' => ['required', 'string', 'max:150'],
            'guest_room' => ['required', 'array'],
            'guest_room.*' => ['required', 'integer', 'min:1'],
            'guest_category' => ['required', 'array'],
            'guest_category.*' => ['required', 'in:Adult,Child'],
            'guest_phone' => ['nullable', 'array'],
            'guest_phone.*' => ['nullable', 'string', 'max:40'],
            'guest_sex' => ['required', 'array'],
            'guest_sex.*' => ['required', 'in:Male,Female'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        if ((int) $this->input('hotel_booking_version') !== 2) {
            return;
        }

        $validator->after(function (Validator $validator) {
            $adults = array_values((array) $this->input('room_adults', []));
            $children = array_values((array) $this->input('room_children', []));
            $names = array_values((array) $this->input('guest_name', []));
            $rooms = array_values((array) $this->input('guest_room', []));
            $categories = array_values((array) $this->input('guest_category', []));
            $phones = array_values((array) $this->input('guest_phone', []));
            $sexes = array_values((array) $this->input('guest_sex', []));
            $expectedGuests = collect($adults)->sum() + collect($children)->sum();

            if (count($names) !== $expectedGuests
                || count($rooms) !== $expectedGuests
                || count($categories) !== $expectedGuests
                || count($phones) !== $expectedGuests
                || count($sexes) !== $expectedGuests) {
                $validator->errors()->add('guest_name', __('messages.Guest details must match the selected room occupancy.'));
            }

            foreach ($adults as $index => $adultCount) {
                $roomNumber = $index + 1;
                $roomCategories = collect($categories)->filter(
                    fn ($category, $guestIndex) => (int) ($rooms[$guestIndex] ?? 0) === $roomNumber
                );
                $childAges = array_values((array) $this->input("room_child_ages.$index", []));

                if ($roomCategories->filter(fn ($category) => $category === 'Adult')->count() !== (int) $adultCount
                    || $roomCategories->filter(fn ($category) => $category === 'Child')->count() !== (int) ($children[$index] ?? 0)) {
                    $validator->errors()->add('guest_name', __('messages.Guest categories must match the adults and children selected for each room.'));
                }

                if (count($childAges) !== (int) ($children[$index] ?? 0)) {
                    $validator->errors()->add("room_child_ages.$index", __('messages.Enter the age of every child.'));
                }
            }
        });
    }
}
