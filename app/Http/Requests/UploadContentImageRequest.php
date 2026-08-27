<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadContentImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'max:5120'],
        ];
    }
}
