<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Demande;
use App\Models\Client;
use App\Models\Article;
use App\Jobs\NotifyBoutiquierJob;
use App\Http\Requests\DemandeRequest;
use Illuminate\Support\Facades\DB;
use App\Notifications\RelanceNotification;
use App\Models\User;

use App\Jobs\SendRelanceNotification;

use Illuminate\Support\Facades\Notification;
use Exception;

class DemandeControlleur extends Controller
{
    public function store(DemandeRequest $request)
    {
        $user = Auth::user();

        // Utilisation d'un client de test pour les tests
        $client = Client::find(26); 

        if (!$client) {
            return response()->json(['error' => 'Client non trouvé.'], 404);
        }

        // Récupération des articles de la demande
        $articles = $request->input('articles');

        // Vérification des conditions en fonction de la catégorie du client
        $errorMessage = $this->checkClientConditions($client, $articles);
        if ($errorMessage) {
            return response()->json(['error' => $errorMessage], 400);
        }

        // Création de la demande
        $result = $this->createDemande($client, $articles);
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result; // En cas d'erreur, retournez la réponse JSON
        }

        // Notification aux boutiquiers après la création réussie
        NotifyBoutiquierJob::dispatch($client);

        return response()->json(['message' => 'Demande soumise avec succès.'], 201);
    }

    protected function checkClientConditions($client, $articles)
    {
        if ($client->categorie->libelle === 'Gold') {
            // Pas de restrictions pour les clients Gold
            return null;
        } elseif ($client->categorie->libelle === 'Silver') {
            if ($client->max_montant < $this->calculateTotalAmount($articles)) {
                return 'Le montant maximum de dette est atteint.';
            }
        } elseif ($client->categorie->libelle === 'Bronze') {
            if ($client->dette()->exists()) {
                return 'Vous avez déjà des dettes en cours.';
            }
        }
        return null;
    }

    protected function calculateTotalAmount($articles)
    {
        $total = 0;
        foreach ($articles as $article) {
            $total += $article['prix'] * $article['quantite'];
        }
        return $total;
    }

    protected function createDemande($client, $articles)
    {
        // Vérification des articles avant de commencer une transaction
        foreach ($articles as $article) {
            $articleModel = Article::where('libeller', $article['libelle'])->first();
            if (!$articleModel) {
                return response()->json(['error' => 'Article avec libeller "' . $article['libelle'] . '" n\'existe pas. Veuillez vérifier le stock.'], 400);
            }
        }

        DB::beginTransaction();
        
        try {
            // Création de la demande
            $demande = Demande::create([
                'client_id' => $client->id,
                'status' => 'en attente' // Utilisation du nom correct de la colonne
            ]);

            // Attacher chaque article existant à la demande
            foreach ($articles as $article) {
                $articleModel = Article::where('libeller', $article['libelle'])->first();
                $demande->articles()->attach($articleModel->id, ['quantite' => $article['quantite']]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la demande: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la demande.'], 500);
        }

        return true;
    }



    public function showDemande(Request $request)
    {
        // Récupérer le paramètre status de la requête
        $status = $request->query('status');

        // Filtrer les demandes selon le status s'il est présent
        $demandes = Demande::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })->get();

        // Retourner les données sans formatage JSON
        return $demandes;
    }
    

   
    public function relancer($id)
    {
        $demande = Demande::find($id);
    
        if ($demande && $demande->status === 'en attente') {
            // Récupérer tous les utilisateurs avec le rôle 2
            $users = User::where('role_id', 2)->get();
    
            // Planifiez le job pour chaque utilisateur
            foreach ($users as $user) {
                SendRelanceNotification::dispatch($demande, $user->login) // Assurez-vous que $user->login est utilisé ici
                    ->delay(now()->addSeconds(10));
            }
    
            return response()->json(['message' => 'Relance programmée pour tous les utilisateurs avec le rôle 2.']);
        }
    
        return response()->json(['message' => 'Demande non trouvée ou état non valide.'], 404);
    }
    
}

















