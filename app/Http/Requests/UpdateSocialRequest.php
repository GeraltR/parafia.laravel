<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facebook' => ['nullable', 'string', 'max:2048'],
            'youtube' => ['nullable', 'string', 'max:2048'],
            'x' => ['nullable', 'string', 'max:2048'],
            'instagram' => ['nullable', 'string', 'max:2048'],
            'tiktok' => ['nullable', 'string', 'max:2048'],
            'pinterest' => ['nullable', 'string', 'max:2048'],
            'linkedin' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
