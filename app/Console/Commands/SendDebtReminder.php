<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Notifications\DebtReminderNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class SendDebtReminder extends Command
{
    protected $signature = 'email:send-debt-reminder';
    protected $description = 'Envoie un rappel de dettes aux clients tous les vendredis à 14h.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Récupérer les clients ayant des dettes non soldées
        $clients = Client::whereHas('dettes', function($query) {
            $query->whereRaw('montant != (SELECT IFNULL(SUM(montant_payer), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        })->get();

        foreach ($clients as $client) {
            // Calculer le montant total dû pour chaque client
            $totalDue = $client->dettes()->whereRaw('montant != (SELECT IFNULL(SUM(montant_payer), 0) FROM paiements WHERE paiements.dette_id = dettes.id)')
                                           ->sum(DB::raw('montant - (SELECT IFNULL(SUM(montant_payer), 0) FROM paiements WHERE paiements.dette_id = dettes.id)'));

            // Envoi de l'e-mail
            Notification::route('mail', $client->email)
                        ->notify(new DebtReminderNotification($client, $totalDue));
        }

        $this->info('Rappel des dettes envoyé avec succès.');
    }
}
