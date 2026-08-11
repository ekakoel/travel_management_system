<?php

namespace App\Http\Requests\Backend\Operations\Reservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user
            && $user->type === 'admin'
            && in_array($user->position, ['developer', 'reservation', 'weddingRsv'], true);
    }

    public function rules(): array
    {
        return [
            'agn_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('type', 'user')
                    ->where('position', 'agent')
                    ->where('status', 'Active')
                    ->whereNotNull('code')),
            ],
            'checkin' => ['required', 'date_format:Y-m-d'],
            'checkout' => ['required', 'date_format:Y-m-d', 'after_or_equal:checkin'],
        ];
    }
}
