<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMassIntentionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'time' => ['required', 'string', 'regex:/^\d{1,2}:\d{2}$/'],
            'intention' => ['required', 'string'],
            'isHoliday' => ['boolean'],
            'dayDescription' => ['nullable', 'string'],
            'authorId' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
