<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNavbarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.label' => ['required', 'string'],
            'items.*.href' => ['required', 'string'],
            'items.*.children' => ['array'],
            'items.*.children.*.id' => ['nullable', 'integer'],
            'items.*.children.*.label' => ['required', 'string'],
            'items.*.children.*.href' => ['required', 'string'],
        ];
    }
}
