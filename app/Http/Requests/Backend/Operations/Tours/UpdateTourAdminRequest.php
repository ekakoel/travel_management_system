<?php

namespace App\Http\Requests\Backend\Operations\Tours;

class UpdateTourAdminRequest extends StoreTourAdminRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|string|max:255',
        ]);
    }
}
