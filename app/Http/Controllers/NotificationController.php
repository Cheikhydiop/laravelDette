<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $clientId = Auth::id();
        dd($clientId); // Ajoutez ceci pour vérifier l'ID de l'utilisateur
        $notifications = Notification::where('notifiable_id', $clientId)
            ->where('type', 'App\Notifications\DebtReminderNotification')
            ->get();
    
        return response()->json([
            'donner' => $notifications,
            'status' => 'success',
            'message' => 'Données récupérées avec succès',
        ]);
    }
    
}
