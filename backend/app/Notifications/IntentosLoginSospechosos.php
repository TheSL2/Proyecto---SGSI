<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class IntentosLoginSospechosos extends Notification
{
    use Queueable;

    public function __construct(public string $ip) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Intentos de inicio de sesión sospechosos en tu cuenta')
            ->line("Se detectaron múltiples intentos fallidos de inicio de sesión desde la IP: {$this->ip}")
            ->line('Si no fuiste tú, te recomendamos cambiar tu contraseña de inmediato.');
    }
}