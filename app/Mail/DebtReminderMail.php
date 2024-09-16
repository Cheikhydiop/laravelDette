<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DebtReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $totalDue;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $totalDue)
    {
        $this->user = $user;
        $this->totalDue = $totalDue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.debt_reminder')
                    ->with([
                        'prenom' => $this->user->prenom, 
                        'nom' => $this->user->nom,       
                        'totalDue' => $this->totalDue,
                        'photo' => $this->user->photo,
                    ]);
    }
}
