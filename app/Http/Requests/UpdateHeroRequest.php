<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'titleWidth' => ['required', 'integer', 'min:1', 'max:12'],
            'titleFont' => ['nullable', 'string'],
            'titleVAlign' => ['required', 'in:top,center,bottom'],
            'titleColor' => ['nullable', 'string'],
            'subtitle' => ['nullable', 'string'],
            'subtitleWidth' => ['required', 'integer', 'min:1', 'max:12'],
            'subtitleFont' => ['nullable', 'string'],
            'subtitleVAlign' => ['required', 'in:top,center,bottom'],
            'subtitleColor' => ['nullable', 'string'],
            'keynote' => ['nullable', 'string'],
            'keynoteWidth' => ['required', 'integer', 'min:1', 'max:12'],
            'keynoteFont' => ['nullable', 'string'],
            'keynoteVAlign' => ['required', 'in:top,center,bottom'],
            'backgroundImage' => ['required', 'string'],
            'buttons' => ['array'],
            'buttons.*.id' => ['nullable', 'integer'],
            'buttons.*.label' => ['required', 'string'],
            'buttons.*.href' => ['required', 'string'],
            'buttons.*.icon' => ['required', 'in:mass,announcements,live'],
            'buttons.*.external' => ['nullable', 'boolean'],
            'buttons.*.textColor' => ['nullable', 'string'],
            'buttons.*.textColorHover' => ['nullable', 'string'],
            'buttons.*.bgColor' => ['nullable', 'string'],
            'buttons.*.bgColorHover' => ['nullable', 'string'],
        ];
    }
}
