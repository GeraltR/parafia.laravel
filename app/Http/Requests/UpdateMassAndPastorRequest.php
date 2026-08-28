<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMassAndPastorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'config' => ['array'],
            'config.positionFont' => ['nullable', 'string'],
            'config.positionSize' => ['nullable', 'string'],
            'config.positionColor' => ['nullable', 'string'],
            'config.nameFont' => ['nullable', 'string'],
            'config.nameSize' => ['nullable', 'string'],
            'config.nameColor' => ['nullable', 'string'],
            'massTimes' => ['array'],
            'massTimes.*.id' => ['nullable', 'integer'],
            'massTimes.*.label' => ['required', 'string'],
            'massTimes.*.hours' => ['required', 'string'],
            'massTimes.*.note' => ['nullable', 'string'],
            'pastors' => ['array'],
            'pastors.*.id' => ['nullable', 'integer'],
            'pastors.*.position' => ['required', 'string'],
            'pastors.*.fullName' => ['required', 'string'],
            'pastors.*.photoUrl' => ['nullable', 'string'],
            'pastors.*.duties' => ['nullable', 'string'],
        ];
    }
}
