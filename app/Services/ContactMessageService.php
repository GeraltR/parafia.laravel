<?php

namespace App\Services;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactMessageService
{
    private const RECAPTCHA_MIN_SCORE = 0.5;

    private const RECAPTCHA_EXPECTED_ACTION = 'contact_form';

    public function verifyRecaptcha(string $token): bool
    {
        $projectId = config('services.recaptcha.project_id');
        $apiKey = config('services.recaptcha.api_key');
        $siteKey = config('services.recaptcha.site_key');

        if (! $projectId || ! $apiKey || ! $siteKey) {
            Log::warning('reCAPTCHA config missing', [
                'has_project_id' => (bool) $projectId,
                'has_api_key' => (bool) $apiKey,
                'has_site_key' => (bool) $siteKey,
            ]);

            return false;
        }

        $response = Http::post(
            "https://recaptchaenterprise.googleapis.com/v1/projects/{$projectId}/assessments?key={$apiKey}",
            [
                'event' => [
                    'token' => $token,
                    'siteKey' => $siteKey,
                    'expectedAction' => self::RECAPTCHA_EXPECTED_ACTION,
                ],
            ]
        );

        if (! $response->successful()) {
            Log::warning('reCAPTCHA assessment request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $data = $response->json();
        $valid = (bool) ($data['tokenProperties']['valid'] ?? false);
        $actionMatches = ($data['tokenProperties']['action'] ?? null) === self::RECAPTCHA_EXPECTED_ACTION;
        $score = $data['riskAnalysis']['score'] ?? 0;

        $passed = $valid && $actionMatches && $score >= self::RECAPTCHA_MIN_SCORE;

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
            messageBody: $data['message'],
        ));
    }
}
