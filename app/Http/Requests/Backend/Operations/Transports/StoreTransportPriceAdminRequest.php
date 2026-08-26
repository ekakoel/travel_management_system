<?php

namespace App\Http\Requests\Backend\Operations\Transports;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransportPriceAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->can('isAdmin') && ($user->can('posDev') || $user->can('posAuthor'));
    }

    public function rules(): array
    {
        return [
            'transports_id' => 'required|integer|exists:transports,id',
            'type' => 'required|string|in:Daily Rent,Airport Shuttle,Transfers',
            'src' => 'nullable|string|max:255',
            'dst' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:1',
            'contract_rate' => 'required|numeric|min:0',
            'markup' => 'required|numeric|min:0',
            'extra_time' => 'required|numeric|min:0',
            'additional_info' => 'nullable|string',
        ];
    }
}
