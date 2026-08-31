<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'validFrom' => ['required', 'date'],
            'validTo' => ['required', 'date', 'after_or_equal:validFrom'],
            'title' => ['required', 'string'],
            'shortInfo' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'progressValue' => ['required', 'integer', 'min:0', 'max:100'],
            'progressDescription' => ['required', 'string'],
            'information' => ['nullable', 'string'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
