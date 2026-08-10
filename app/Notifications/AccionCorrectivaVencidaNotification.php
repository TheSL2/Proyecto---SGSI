<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\AccionCorrectiva;

class AccionCorrectivaVencidaNotification extends Notification
{
    use Queueable;

    public function __construct(public AccionCorrectiva $accion) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Acción Correctiva Vencida: ' . $this->accion->descripcion_accion)
            ->line('La siguiente acción correctiva superó su fecha límite y fue marcada como Vencida.')
            ->line('Descripción: ' . $this->accion->descripcion_accion)
            ->line('Fecha límite: ' . \Carbon\Carbon::parse($this->accion->fecha_limite)->format('d/m/Y'))
            ->line('Por favor da seguimiento a la brevedad.');
    }
}