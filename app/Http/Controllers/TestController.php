<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\DebtReminderNotification;

class TestController extends Controller
{
    public function sendSms()
    {
        // Assurez-vous d'avoir un utilisateur dans votre base de données
        $user = User::first();

        // Créez un objet client
        $client = (object)[
            'nom' => 'Jean Dupont',
            'phone' => '+1234567890'
        ];

        // Créez une instance de la notification
        $notification = new DebtReminderNotification($client, 150.00);

        // Envoyez la notification
        $user->notify($notification);

        return response()->json(['message' => 'Notification sent successfully']);
    }
}
