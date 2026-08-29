<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsItemRequest extends FormRequest
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
            'image' => ['required', 'string'],
            'body' => ['nullable', 'string'],
            'showImageOnFullContent' => ['boolean'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
