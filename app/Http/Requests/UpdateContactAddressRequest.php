<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'street' => ['required', 'string'],
            'city' => ['required', 'string'],
            'postCode' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'phone' => ['required', 'string'],
            'nip' => ['nullable', 'string'],
            'bankAccountNumber' => ['nullable', 'string'],
            'bankName' => ['nullable', 'string'],
            'social' => ['array'],
            'social.facebook' => ['boolean'],
            'social.youtube' => ['boolean'],
            'social.x' => ['boolean'],
            'social.instagram' => ['boolean'],
            'social.tiktok' => ['boolean'],
            'social.pinterest' => ['boolean'],
            'social.linkedin' => ['boolean'],
        ];
    }
}
