<?php

namespace App\Http\Requests\Activities;

use Illuminate\Foundation\Http\FormRequest;

class QuoteActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:200'],
            'travel_date' => ['required', 'date', 'after_or_equal:now'],
        ];
    }
}
