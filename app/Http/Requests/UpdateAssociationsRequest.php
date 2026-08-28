<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssociationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'config' => ['array'],
            'config.nameFont' => ['nullable', 'string'],
            'config.nameSize' => ['nullable', 'string'],
            'items' => ['array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string'],
            'items.*.imageUrl' => ['nullable', 'string'],
            'items.*.link' => ['required', 'string'],
        ];
    }
}
