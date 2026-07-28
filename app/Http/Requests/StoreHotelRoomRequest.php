<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'cover' => ['required', 'image', 'max:4096'],
            'rooms' => ['required', 'string', 'max:255'],
            'room_view' => ['required', 'string', 'max:100'],
            'custom_room_view' => ['required_if:room_view,custom', 'nullable', 'string', 'max:100'],
            'beds' => ['required', 'string', 'max:100'],
            'custom_beds' => ['required_if:beds,custom', 'nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'capacity_adult' => ['nullable', 'integer', 'min:0'],
            'capacity_child' => ['nullable', 'integer', 'min:0'],
            'inventory' => ['required', 'integer', 'min:1'],
        ];
    }
}
