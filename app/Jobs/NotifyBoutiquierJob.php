<?php

namespace App\Jobs;

use App\Models\Client; // Assurez-vous que ce chemin est correct
use App\Notifications\DemandeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyBoutiquierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $emailBoutiquier;

    /**
     * Create a new job instance.
     *
     * @param Client $client
     * @param string $emailBoutiquier
     * @return void
     */
    public function __construct(Client $client, $emailBoutiquier)
    {
        $this->client = $client;
        $this->emailBoutiquier = $emailBoutiquier;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Envoyez la notification
        \Notification::route('mail', $this->emailBoutiquier)
            ->notify(new DemandeNotification($this->client));
    }
}
