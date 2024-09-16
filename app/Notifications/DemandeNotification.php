<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DemandeNotification extends Notification
{
    use Queueable;

    protected $client;

    /**
     * Create a new notification instance.
     *
     * @param  Client  $client
     * @return void
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Vous pouvez ajouter d'autres canaux ici
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Une nouvelle demande a été soumise.')
                    // ->line('Client: ' . $this->client->surname)
                    ->line('Merci de vérifier le stock.');
    }

    // Vous pouvez définir d'autres méthodes pour d'autres canaux si nécessaire
}