<?php

namespace App\Http\Requests\Backend\Operations\Tours;

use Illuminate\Validation\Rule;

class UpdateTourAdminRequest extends StoreTourAdminRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => ['required', Rule::in(['Active', 'Draft'])],
        ]);
    }
}
