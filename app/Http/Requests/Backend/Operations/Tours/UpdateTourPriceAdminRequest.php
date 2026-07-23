<?php

namespace App\Http\Requests\Backend\Operations\Tours;

class UpdateTourPriceAdminRequest extends StoreTourPriceAdminRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status' => 'required|string|in:Draft,Active',
        ]);
    }
}
