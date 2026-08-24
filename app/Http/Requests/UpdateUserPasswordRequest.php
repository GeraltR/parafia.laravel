<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'newPassword' => ['required', 'string', Password::min(12)->mixedCase()->numbers()->symbols()],
            'newPasswordConfirmation' => ['required', 'string', 'same:newPassword'],
        ];
    }
}
