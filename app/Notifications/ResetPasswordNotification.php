<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = rtrim(config('app.frontend_url'), '/').'/reset-password?token='.$this->token
            .'&email='.urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset hasła — panel parafii')
            ->greeting('Witaj,')
            ->line('Otrzymaliśmy prośbę o zresetowanie hasła do panelu parafii.')
            ->action('Ustaw nowe hasło', $url)
            ->line('Link jest ważny przez 60 minut.')
            ->line('Jeśli to nie Ty prosiłeś/aś o reset hasła, zignoruj tę wiadomość.');
    }
}
