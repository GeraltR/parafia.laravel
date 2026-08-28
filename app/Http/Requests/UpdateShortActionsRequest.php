<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShortActionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'config' => ['array'],
            'config.titleFont' => ['nullable', 'string'],
            'config.titleSize' => ['nullable', 'string'],
            'config.titleColor' => ['nullable', 'string'],
            'config.subtitleFont' => ['nullable', 'string'],
            'config.subtitleSize' => ['nullable', 'string'],
            'config.subtitleColor' => ['nullable', 'string'],
            'config.bgColor' => ['nullable', 'string'],
            'config.bgColorHover' => ['nullable', 'string'],
            'items' => ['required', 'array', 'size:6'],
            'items.*.id' => ['required', 'integer', 'exists:short_action_items,id'],
            'items.*.icon' => ['nullable', 'string'],
            'items.*.iconUrl' => ['nullable', 'string'],
            'items.*.title' => ['required', 'string'],
            'items.*.description' => ['required', 'string'],
            'items.*.href' => ['required', 'string'],
            'items.*.external' => ['nullable', 'boolean'],
        ];
    }
}
