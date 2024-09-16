<?php
namespace App\Http\Controllers;

use App\Services\AuthentificationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Enums\StatusResponseEnum;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Traits\RestResponseTrait;
use App\Events\TestEvent;


class AuthController extends Controller
{
    use RestResponseTrait; 
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }


  /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="photo", type="string"),
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="client.id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Récupérer les données de la requête
            $userRequest = $request->only('nom', 'prenom', 'photo', 'login', 'password', 'client');
            
            // Vérifier l'ID du client
            $clientId = $userRequest['client']['id'] ?? null;
            if (!$clientId) {
                $userRequest['photo'] = '/home/diop/Images/avatar.png'; 
            }
            
            // Définir le rôle et hasher le mot de passe
            $userRequest['role_id'] = 3; 
            $userRequest['password'] = Hash::make($userRequest['password']);
            
            // Créer l'utilisateur
            $user = User::create($userRequest);
            
            if ($clientId) {
                // Trouver le client et l'associer si l'ID client est présent
                $client = Client::find($clientId);
                if (!$client) {
                    throw new Exception("Client non trouvé");
                }
                
                $client->user()->associate($user);
                $client->save();
            }
            
            DB::commit();
            event(new TestEvent('hello world'));

            
            return $this->sendResponse(new ClientResource($client ?? $user), StatusResponseEnum::SUCCESS, 'Compte créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }
    
    

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login or password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login ou mot de passe incorrect")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        
        if ($this->authService->authenticate($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return response()->json(['token' => $token], 200);
        }
        
        return response()->json(['message' => 'Login ou mot de passe incorrect'], 401);
    }


//     public function login(Request $request)
// {
//     $credentials = $request->only('login', 'password');
    
//     if ($this->authService->authenticate($credentials)) {
//         // Récupérer l'utilisateur authentifié
//         /** @var \App\Models\User $user */
//         $user = Auth::user();
        
//         // Vérifier si l'utilisateur est associé à un client
//         $client = $user->client; // Relation entre User et Client
        
//         // Générer le token avec des informations supplémentaires
//         $token = $user->createToken('authToken', [
//             'client_id' => $client->id ?? null,
//             'client_category' => $client->category->libelle ?? 'Bronze' // par défaut 'Bronze'
//         ])->accessToken;

//         return response()->json(['token' => $token], 200);
//     }

//     return response()->json(['message' => 'Login ou mot de passe incorrect'], 401);
// }


    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout a user",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Vous avez été déconnecté avec succès !'], 200);
    }
}
