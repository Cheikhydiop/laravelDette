<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Repositories\ClientRepositoryInterface;
use App\Services\ClientServiceInterface;
use App\Services\QrCodeService;
use App\Services\UploadServiceCloud;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoyaltyCardMail;
use App\Exceptions\ClientNotFoundException;
use App\Models\Categorie;  


class ClientController extends Controller
{
    protected $uploadService;
    protected $clientService;
    protected $qrCodeService;
    protected $clientRepository;

    public function __construct(
        ClientServiceInterface $clientService,
        UploadServiceCloud $uploadService,
        QrCodeService $qrCodeService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
        $this->qrCodeService = $qrCodeService;
        $this->clientRepository = $clientRepository;
    }

    public function WithUser($id)
    {
        // dd("Client");
        try {
            $client = Client::with('user')->find($id);

            if (!$client) {
                throw new ClientNotFoundException();
            }

            return response()->json($client);
        } catch (ClientNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.'
            ], 500);
        }
    }
    public function store(ClientRequest $request)
    {
        try {
            // Récupération des données de la requête
            $clientRequest = $request->only('surname', 'adresse', 'telephone', 'email', 'user');
    
            // Recherche de l'ID de la catégorie dans la table categories en utilisant le libellé
            $categorie = \App\Models\Categorie::where('libelle', $request->input('categorie'))->first();
            
            if (!$categorie) {
                return response()->json(['error' => 'Catégorie non trouvée.'], 400);
            }
            
            // Utilisation de l'ID de la catégorie
            $clientRequest['categorie_id'] = (int)$categorie->id;  // Convertir explicitement en entier
            
            // Gestion du montant maximal pour les clients Silver
            if ($categorie->libelle === 'Silver') {
                // Vérifier si le montant maximum est spécifié dans la requête
                if (!$request->has('max_montant')) {
                    return response()->json(['error' => 'Le montant maximum est requis pour la catégorie Silver.'], 400);
                }
                $clientRequest['max_montant'] = $request->input('max_montant');
            } else {
                $clientRequest['max_montant'] = 0;  // Pas de montant maximum pour les autres catégories
            }
            
            // Gestion de la photo du client
            if ($request->hasFile('photo')) {
                $clientRequest['photo'] = $request->file('photo')->store('photos', 'public');
            } else {
                $clientRequest['photo'] = 'https://res.cloudinary.com/default.png';
            }
    
            // Logguer les données avant l'insertion
            Log::info('Données du client avant l\'insertion : ', $clientRequest);
            
            // Insertion du client avec la clé étrangère categorie_id
            $client = $this->clientRepository->createClient($clientRequest);
            
            if (!$client->email) {
                return response()->json([
                    'error' => 'L\'adresse e-mail du client est manquante.'
                ], 400);
            }
            
            // Générer la carte de fidélité et envoyer un e-mail
            $this->qrCodeService->generateLoyaltyCard($client);
            Mail::to($client->email)->send(new LoyaltyCardMail($client));
            
            return response()->json(new ClientResource($client), 201);
        } catch (Exception $e) {
            Log::error('Erreur lors de la création du client: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    // public function createClient($clientRequest)
    // {
    //     DB::beginTransaction();
    
    //     try {
    //         // Gestion du téléversement de la photo
    //         if (isset($clientRequest['photo']) && file_exists($clientRequest['photo'])) {
    //             $clientRequest['photo'] = $this->uploadService->uploadImage($clientRequest['photo']);
    //             Log::info('Photo upload result: ', ['result' => $clientRequest['photo']]);
    //         } else {
    //             $clientRequest['photo'] = 'https://res.cloudinary.com/default.png';
    //         }
    
    //         // Log des données avant l'insertion
    //         Log::info('Données du client à insérer : ', $clientRequest);
    
    //         // Création du client
    //         $client = Client::create($clientRequest);
    
    //         // Gestion des utilisateurs associés au client
    //         if (isset($clientRequest['user'])) {
    //             $roleId = $clientRequest['user']['role']['id'];
    //             $role = Role::find($roleId);
    
    //             $user = User::create([
    //                 'nom' => $clientRequest['user']['nom'],
    //                 'prenom' => $clientRequest['user']['prenom'],
    //                 'login' => $clientRequest['user']['login'],
    //                 'password' => Hash::make($clientRequest['user']['password']),
    //                 'photo' => $clientRequest['user']['photo'] ?? $clientRequest['photo'],
    //                 'role_id' => $role->id
    //             ]);
    
    //             // Associer l'utilisateur au client
    //             $client->user()->associate($user);
    //             $client->save();
    //         }
    
    //         DB::commit();
    //         return $client;
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error creating client: ', ['error' => $e->getMessage()]);
    //         throw $e;
    //     }
    // }
    
    


    // public function index()
    // {
    //     $clients = Client::all();
    //     return response()->json($clients);
    // }

  

    public function showClient($id)
    {
        // Rechercher le client par son id
        $client = Client::find($id);

        // Si le client n'existe pas, renvoyer une erreur 404
        if (!$client) {
            return response()->json(['message' => 'Client non trouvé'], 404);
        }

        // Renvoyer les informations du client
        return response()->json($client);
    }
    public function index(Request $request)
    {
        $clientsQuery = Client::query();
    
        if ($request->has('comptes')) {
            $hasCompte = $request->query('comptes') === 'oui';
            if ($hasCompte) {
                $clientsQuery->whereNotNull('user_id');
            } else {
                $clientsQuery->whereNull('user_id');
            }
        }
    
        if ($request->has('active')) {
            $isActive = $request->query('active') === 'oui';
            $clientsQuery->whereHas('user', function ($query) use ($isActive) {
                $query->where('active', $isActive ? 'oui' : 'non');
            });
        }
    
        $clients = $clientsQuery->get();
        return response()->json($clients);
    }

    
   
    
}
