<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadHeroBackgroundImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ];
    }
}
