<?php

namespace App\Http\Requests;

use App\Enums\PermissionLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(12)->mixedCase()->numbers()->symbols()],
            'passwordConfirmation' => ['required', 'string', 'same:password'],
            'permissionLevel' => ['required', 'integer', Rule::in(array_column(PermissionLevel::cases(), 'value'))],
        ];
    }
}
