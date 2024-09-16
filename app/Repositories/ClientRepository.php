<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Services\UploadServiceCloud;

class ClientRepository implements ClientRepositoryInterface
{
    protected $uploadService;

    public function __construct(UploadServiceCloud $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function find($id): ?Client
    {
        return Client::find($id);
    }

    // public function findByTelephone($telephone): ?Client
    // {
    //     return Client::withTelephone($telephone)->first();
    // }


    public function findByTelephone($telephone): ?Client
{
    return Client::where('telephone', $telephone)->first();
}

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update($id, array $data): ?Client
    {
        $client = $this->find($id);
        if ($client) {
            $client->update($data);
        }
        return $client;
    }

    public function delete($id): bool
    {
        $client = $this->find($id);
        if ($client) {
            return $client->delete();
        }
        return false;
    }

   
    public function createClient($clientRequest)
    {
        DB::beginTransaction();
    
        try {
            // Gestion du téléversement de la photo
            if (isset($clientRequest['photo']) && file_exists($clientRequest['photo'])) {
                $clientRequest['photo'] = $this->uploadService->uploadImage($clientRequest['photo']);
                Log::info('Photo upload result: ', ['result' => $clientRequest['photo']]);
            } else {
                $clientRequest['photo'] = 'https://res.cloudinary.com/dtossohz4/image/upload/v1725465088/xcb8pgm42qc6vkzgwnvd.png';
            }
    
            // Création du client
            $client = Client::create($clientRequest);
    
            // Gestion des utilisateurs associés au client
            if (isset($clientRequest['user'])) {
                $userRequest = $clientRequest['user'];
    
                // Définir un role_id par défaut si non spécifié
                $roleId = $userRequest['role']['id'] ?? 3;  // Par défaut, role_id est 3
    
                // Vérifiez si le rôle existe
                $role = Role::find($roleId);
    
                if (!$role) {
                    Log::error('Le rôle avec l\'ID ' . $roleId . ' n\'existe pas.');
                    return response()->json(['error' => 'Rôle non valide.'], 400);
                }
    
                $user = User::create([
                    'nom' => $userRequest['nom'],
                    'prenom' => $userRequest['prenom'],
                    'login' => $userRequest['login'],
                    'password' => Hash::make($userRequest['password']),
                    'photo' => $userRequest['photo'] ?? $clientRequest['photo'],
                    'role_id' => $role->id
                ]);
    
                // Associer l'utilisateur au client
                $client->user()->associate($user);
                $client->save();
            }
    
            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating client: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    

}