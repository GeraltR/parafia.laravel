<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'title' => ['required', 'string'],
            'excerpt' => ['required', 'string'],
            'image' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'showImageOnFullContent' => ['boolean'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
