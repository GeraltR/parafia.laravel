<?php

namespace App\Http\Requests;

use App\Enums\ContentPageSlug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['required', 'string', Rule::in(array_column(ContentPageSlug::cases(), 'value'))],
            'iconUrl' => ['nullable', 'string'],
            'title' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'visibleFrom' => ['nullable', 'date'],
            'authorId' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
