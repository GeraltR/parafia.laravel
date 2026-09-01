<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Tests\TestCase;

class ContactMessageMailTest extends TestCase
{
    public function test_mail_renders_without_errors(): void
    {
        $mail = new ContactMessageMail(
            name: 'Jan Kowalski',
            email: 'jan@example.com',
            messageSubject: 'Pytanie o intencję',
            messageBody: 'Treść wiadomości testowej.',
        );

        $rendered = $mail->render();

        $this->assertStringContainsString('Jan Kowalski', $rendered);
        $this->assertStringContainsString('jan@example.com', $rendered);
        $this->assertStringContainsString('Treść wiadomości testowej.', $rendered);
    }
}
