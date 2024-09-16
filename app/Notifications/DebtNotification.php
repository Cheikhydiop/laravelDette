<?php

// app/Notifications/DebtNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DebtNotification extends Notification
{
    use Queueable;

    protected $totalDebt;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($totalDebt)
    {
        $this->totalDebt = $totalDebt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Vous pouvez ajouter 'database' ou 'sms' ici si vous voulez.
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
                    ->subject('Rappel : Montant total de votre dette')
                    ->greeting('Bonjour ' . $notifiable->name)
                    ->line('Nous vous rappelons que vous avez une dette totale de : ' . $this->totalDebt . ' FCFA.')
                    ->line('Veuillez régulariser votre situation dès que possible.')
                    ->action('Voir votre compte', url('/mon-compte'))
                    ->line('Merci de faire affaire avec nous.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'total_debt' => $this->totalDebt,
        ];
    }
}
