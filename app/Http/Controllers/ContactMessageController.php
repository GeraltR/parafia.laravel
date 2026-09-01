<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContactMessageController extends Controller
{
    public function __construct(private readonly ContactMessageService $contactMessageService) {}

    public function store(SubmitContactMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (! $this->contactMessageService->verifyRecaptcha($data['recaptchaToken'])) {
            throw ValidationException::withMessages([
                'recaptchaToken' => 'Weryfikacja reCAPTCHA nie powiodła się. Spróbuj ponownie.',
            ]);
        }

        $this->contactMessageService->send($data);

        return response()->json(['message' => 'Wiadomość została wysłana.']);
    }
}
