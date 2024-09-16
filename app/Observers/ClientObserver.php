<?php

namespace App\Observers;

use App\Models\Client;
use App\Events\ClientCreated;

class ClientObserver
{
    public function created(Client $client)
    {
        // Déclenche l'événement ClientCreated après la création du client
        event(new ClientCreated($client));
    }
}
