<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLiturgiaTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'iconUrl' => ['nullable', 'string'],
            'title' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'visibleFrom' => ['nullable', 'date'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
