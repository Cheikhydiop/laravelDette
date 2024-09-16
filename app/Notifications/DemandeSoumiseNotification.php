<?php
use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeSoumiseNotification extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Une nouvelle demande a été soumise par un client.')
                    ->action('Voir la demande', url('/demandes/'.$this->demande->id))
                    ->line('Merci de gérer la demande au plus vite.');
    }
}

