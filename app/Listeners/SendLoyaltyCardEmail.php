<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoyaltyCardMail;

class SendLoyaltyCardEmail
{
    public function handle(ClientCreated $event)
    {
        Mail::to($event->client->email)->send(new LoyaltyCardMail($event->client));
    }
}
