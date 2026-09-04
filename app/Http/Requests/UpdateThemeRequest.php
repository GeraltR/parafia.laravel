<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'primaryColor' => ['required', 'string'],
            'secondaryColor' => ['required', 'string'],
            'fontHeading' => ['required', 'string'],
            'fontBody' => ['required', 'string'],
            'title' => ['required', 'string'],
            'subtitle' => ['required', 'string'],
            'privacyPolicy' => ['nullable', 'string'],
            'accessibilityStatement' => ['nullable', 'string'],
        ];
    }
}
