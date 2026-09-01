<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'subject' => 'Pytanie o intencję',
            'message' => 'Treść wiadomości testowej.',
            'recaptchaToken' => 'test-token',
        ];
    }

    public function test_store_requires_fields(): void
    {
        $response = $this->postJson('/api/contact-message', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'subject', 'message', 'recaptchaToken']);
    }

    public function test_store_rejects_invalid_email(): void
    {
        $payload = $this->payload();
        $payload['email'] = 'not-an-email';

        $response = $this->postJson('/api/contact-message', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_store_sends_mail_when_recaptcha_passes(): void
    {
        Http::fake([
            'recaptchaenterprise.googleapis.com/*' => Http::response([
                'tokenProperties' => ['valid' => true, 'action' => 'contact_form'],
                'riskAnalysis' => ['score' => 0.9],
            ]),
        ]);
        Mail::fake();

        $response = $this->postJson('/api/contact-message', $this->payload());

        $response->assertOk();
        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->name === 'Jan Kowalski'
                && $mail->email === 'jan@example.com'
                && $mail->messageSubject === 'Pytanie o intencję'
                && $mail->messageBody === 'Treść wiadomości testowej.';
        });
    }

    public function test_store_rejects_when_recaptcha_fails(): void
    {
        Http::fake([
            'recaptchaenterprise.googleapis.com/*' => Http::response([
                'tokenProperties' => ['valid' => false],
            ]),
        ]);
        Mail::fake();

        $response = $this->postJson('/api/contact-message', $this->payload());

        $response->assertStatus(422);
        Mail::assertNotSent(ContactMessageMail::class);
    }

    public function test_store_rejects_when_score_is_too_low(): void
    {
        Http::fake([
            'recaptchaenterprise.googleapis.com/*' => Http::response([
                'tokenProperties' => ['valid' => true, 'action' => 'contact_form'],
                'riskAnalysis' => ['score' => 0.1],
            ]),
        ]);
        Mail::fake();

        $response = $this->postJson('/api/contact-message', $this->payload());

        $response->assertStatus(422);
        Mail::assertNotSent(ContactMessageMail::class);
    }
}
