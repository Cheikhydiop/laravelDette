<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DemandeResponseNotification extends Notification
{
    protected $demande;

    public function __construct($demande)
    {
        $this->demande = $demande;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Réponse à votre demande de dette.')
                    ->action('Voir la réponse', url('/demandes/' . $this->demande->id))
                    ->line('Merci pour votre patience.');
    }
}
