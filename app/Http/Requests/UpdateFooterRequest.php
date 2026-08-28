<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'officeTitle' => ['required', 'string'],
            'officeNote' => ['nullable', 'string'],
            'mapEmbedUrl' => ['nullable', 'string'],
            'mapLink' => ['nullable', 'string'],
            'config' => ['array'],
            'config.bgColor' => ['nullable', 'string'],
            'config.titleFont' => ['nullable', 'string'],
            'config.titleSize' => ['nullable', 'string'],
            'config.titleColor' => ['nullable', 'string'],
            'officeHours' => ['array'],
            'officeHours.*.id' => ['nullable', 'integer'],
            'officeHours.*.day' => ['required', 'string'],
            'officeHours.*.hoursOn' => ['required', 'string', 'regex:/^\d{1,2}:\d{2}$/'],
            'officeHours.*.hoursEnd' => ['required', 'string', 'regex:/^\d{1,2}:\d{2}$/'],
        ];
    }
}
