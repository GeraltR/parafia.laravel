<?php

namespace App\Services;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactMessageService
{
    private const RECAPTCHA_MIN_SCORE = 0.5;

    public function verifyRecaptcha(string $token): bool
    {
        $secret = config('services.recaptcha.secret_key');

        if (! $secret) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
        ]);

        if (! $response->successful()) {
            Log::warning('reCAPTCHA siteverify request failed', ['status' => $response->status()]);

            return false;
        }

        $data = $response->json();
        $passed = ($data['success'] ?? false) === true
            && ($data['score'] ?? 0) >= self::RECAPTCHA_MIN_SCORE;

        if (! $passed) {
            Log::warning('reCAPTCHA verification did not pass', ['response' => $data]);
        }

        return $passed;
    }

    public function send(array $data): void
    {
        Mail::to(config('contact.recipient_email'))->send(new ContactMessageMail(
            name: $data['name'],
            email: $data['email'],
            messageSubject: $data['subject'],
            message: $data['message'],
        ));
    }
}
