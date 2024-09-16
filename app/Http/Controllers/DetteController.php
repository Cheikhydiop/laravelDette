<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\DetteArticle;  // Importation du modèle
use App\Http\Resources\DetteResource;
use App\Http\Requests\DetteRequest;
use App\Services\DetteService;
use MongoDB\Client as MongoClient;
use App\Services\DatabaseInterface;




class DetteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $dettes = Dette::all(); // Récupère toutes les dettes
    //     return response()->json($dettes); // Retourne les dettes au format JSON
    // }
    protected $mongoClient;
    protected $databaseName;
 
    protected $databaseService;

   


    public function __construct(DetteService $detteService, MongoClient $mongoClient,DatabaseInterface $databaseService)
    {
        $this->detteService = $detteService;
        $this->mongoClient = $mongoClient;
        $this->databaseName = env('MONGO_DB_NAME'); // Assurez-vous que cette variable est définie dans le fichier .env
        $this->databaseService = $databaseService;

    }
    

    public function index(Request $request)
    {
        $statut = $request->query('statut');

        $query = Dette::query();

        if ($statut === 'Solde') {
            $query->whereRaw('prixVente = (SELECT IFNULL(SUM(montant_payer), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        } elseif ($statut === 'NonSolde') {
            $query->whereRaw('prixVente != (SELECT IFNULL(SUM(montant_payer), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        }

        $dettes = $query->get();

        return response()->json($dettes);
    }

  
    public function store(DetteRequest $request)
    {
        try {
            $dette = $this->detteService->storeDette($request->all());

            return response()->json([
                'data' => new DetteResource($dette),
                'message' => 'Dette enregistrée avec succès'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement de la dette',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getArticles($id)
{
    try {
        $dette = Dette::findOrFail($id);
        $articles = $dette->articles; // Assurez-vous que la relation est définie dans le modèle Dette
        return response()->json([
            'data' => [
                'dette' => new DetteResource($dette),
                'articles' => $articles
            ],
            'message' => 'Articles trouvés'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Objet non trouvé',
            'error' => $e->getMessage()
        ], 404);
    }
}
public function archivePaidDebts()
{
    try {
        $dettes = Dette::select('dettes.*')
            ->join('paiements', 'dettes.id', '=', 'paiements.dette_id')
            ->groupBy('dettes.id')
            ->havingRaw('SUM(paiements.montant_payer) >= dettes.montant')
            ->get();

        $mongoCollection = $this->mongoClient->selectDatabase($this->databaseName)->selectCollection('archives');

        foreach ($dettes as $dette) {
            $result = $mongoCollection->insertOne([
                'dette_id' => $dette->id,
                'montant' => $dette->montant,
                'archived_at' => now(),
            ]);

            if ($result->getInsertedCount() === 1) {
                \Log::info('Dette archivée dans MongoDB', ['dette_id' => $dette->id]);
                $dette->delete(); // Suppression de la dette
            } else {
                \Log::error('Erreur lors de l\'insertion dans MongoDB', ['dette_id' => $dette->id]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Dettes payées archivées avec succès'
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur lors de l\'archivage des dettes payées', ['message' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de l\'archivage des dettes payées: ' . $e->getMessage()
        ], 500);
    }
}

public function addPaiement(Request $request, $id)
{
    // $validator = Validator::make($request->all(), [
    //     'montant_payer' => 'required|numeric|positive|max:'.$this->getRemainingAmount($id),
    //     'mode_paiement' => 'string|max:50',
    // ]);

    // if ($validator->fails()) {
    //     return response()->json([
    //         'status' => 411,
    //         'data' => null,
    //         'message' => 'Validation échouée',
    //         'errors' => $validator->errors()
    //     ], 411);
    // }

    try {
        $dette = Dette::findOrFail($id);
        $paiement = new Paiement();
        $paiement->montant_payer = $request->input('montant_payer');
        $paiement->mode_paiement = $request->input('mode_paiement', 'comptant');
        $paiement->dette_id = $id;
        $paiement->save();

        // Optionally update the debt remaining amount

        return response()->json([
            'status' => 200,
            'data' => [
                'dette' => new DetteResource($dette),
                'paiements' => $dette->paiements
            ],
            'message' => 'Paiement ajouté'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 404,
            'data' => null,
            'message' => 'Objet non trouvé',
            'error' => $e->getMessage()
        ], 404);
    }
}

private function getRemainingAmount($id)
{
    $dette = Dette::findOrFail($id);
    $totalPaid = Paiement::where('dette_id', $id)->sum('montant_payer');
    return max(0, $dette->montant - $totalPaid);
}



public function getDetailsWithArticles($id)
{
    try {
        // Récupérer la dette avec les articles associés
        $dette = Dette::with('articles')->findOrFail($id);

        // Retourner la réponse JSON
        return response()->json([
            'data' => [
                'dette' => $dette,
                'articles' => $dette->articles
            ],
            'message' => 'Détails de la dette et articles trouvés'
        ], 200);
    } catch (ModelNotFoundException $e) {
        // Gérer les erreurs si la dette n'est pas trouvée
        return response()->json([
            'message' => 'Objet non trouvé',
            'error' => $e->getMessage()
        ], 404);
    }
}





}
