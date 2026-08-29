<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventItemRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'body' => ['nullable', 'string'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
