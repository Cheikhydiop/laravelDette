<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\RelanceNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRelanceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $demande;
    protected $login;
    protected $defaultLogin = 'defaultUser'; // Définir le login par défaut

    public function __construct($demande, $login = null)
    {
        $this->demande = $demande;
        $this->login = $login ?: $this->defaultLogin; // Utiliser le login par défaut si aucun login n'est fourni
    }

    public function handle()
    {
        // Récupérer l'utilisateur par son login
        $user = User::where('login', $this->login)->first();

        if ($user) {
            // Envoyer la notification à l'utilisateur
            $user->notify(new RelanceNotification($this->demande));
            
            \Log::info('Notification envoyée à ' . $this->login);
        } else {
            \Log::warning('Utilisateur non trouvé pour le login ' . $this->login);
        }
    }
}
