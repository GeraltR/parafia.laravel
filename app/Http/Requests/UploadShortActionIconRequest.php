<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadShortActionIconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icon' => ['required', 'image', 'max:2048'],
        ];
    }
}
