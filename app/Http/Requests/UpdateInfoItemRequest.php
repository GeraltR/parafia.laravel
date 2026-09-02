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
            'image' => ['nullable', 'string'],
            'progressValue' => ['nullable', 'integer', 'min:0', 'max:100'],
            'progressDescription' => ['nullable', 'string'],
            'information' => ['nullable', 'string'],
            'bannerText' => ['nullable', 'string'],
            'bannerFont' => ['nullable', 'string'],
            'bannerTextColor' => ['nullable', 'string'],
            'bannerBgColor' => ['nullable', 'string'],
            'bannerDurationSeconds' => ['required', 'integer', 'min:0', 'max:300'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
