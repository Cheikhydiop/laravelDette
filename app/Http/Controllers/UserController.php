<?php
namespace App\Http\Controllers;

use App\Http\Middleware\ApiResponseMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function __construct()
    {
        // Appliquer le middleware ApiResponseMiddleware
        $this->middleware(ApiResponseMiddleware::class);
    }

    /**
     * Affiche la liste des ressources.
     */
    public function getUsers(Request $request)
    {
        // Vérifie l'autorisation pour voir tous les utilisateurs
        $this->authorize('viewAny', User::class);

        // Récupère les paramètres de filtrage depuis la requête
        $filters = $request->query();

        // Filtrage basé sur 'active' et 'role_id'
        $query = User::query();

        // Appliquer le filtre sur 'active'
        if ($request->has('active')) {
            $activeFilter = strtoupper($request->query('active'));
            $query->where('active', $activeFilter);
        }

        // Appliquer le filtre sur 'role_id'
        if ($request->has('role_id')) {
            $roleFilter = $request->query('role_id');
            $query->where('role_id', $roleFilter);
        }

        // Exécute la requête
        $users = $query->get();

        // Retourner les données brutes
        return $users;
    }


    public function storeUser(Request $request)
    {
        // Validation des données entrantes
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users,login|max:255',
            'password' => [
                'required',
                'string',
                'min:5', // Minimum 5 caractères
                'regex:/[a-z]/', // Contient des lettres minuscules
                'regex:/[A-Z]/', // Contient des lettres majuscules
                'regex:/[0-9]/', // Contient des chiffres
                'regex:/[@$!%*#?&]/', // Contient des caractères spéciaux
            ],
            'role_id' => ['required', 'integer', 'in:1,2'], // `role_id` est obligatoire et doit être 1 ou 2
            'photo' => 'nullable|string|max:255', // Optionnel pour 'photo'
        ]);
    
        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'login' => $validatedData['login'],
            'password' => Hash::make($validatedData['password']), // Hasher le mot de passe
            'role_id' => $validatedData['role_id'], // Ajouter le role_id
            'photo' => $validatedData['photo'] ?? 'default.jpg', // Valeur par défaut si non fourni
        ]);
    
        // Retourner la réponse en JSON
        return response()->json([
            'status' => 201,
            'data' => $user,
            'message' => 'Utilisateur créé avec succès',
        ], 201);
    }
    
}
