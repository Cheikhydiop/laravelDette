<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SmsMessage;

class DebtReminderNotification extends Notification
{
    protected $client;
    protected $totalDue;

    public function __construct($client, $totalDue)
    {
        $this->client = $client;
        $this->totalDue = $totalDue;
    }

    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable)
    {
        return (new SmsMessage)
            ->line("Bonjour {$this->client->nom},")
            ->line("Vous devez un montant de {$this->totalDue} EUR.")
            ->line("Merci de régler votre dette dès que possible.");
    }
}
