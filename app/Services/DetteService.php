<?php

namespace App\Services;

use App\Repositories\DetteRepository;
use MongoDB\Client as MongoClient;
use MongoDB\Exception\Exception as MongoException;
use Illuminate\Support\Facades\DB;

class DetteService
{
    protected $detteRepository;
    protected $mongoClient;
    protected $databaseName;

    public function __construct(DetteRepository $detteRepository, MongoClient $mongoClient)
    {
        $this->detteRepository = $detteRepository;
        $this->mongoClient = $mongoClient;
        $this->databaseName = env('MONGO_DB_NAME');
    }

    public function storeDette($data)
    {
        $dette = $this->detteRepository->createDette($data);
        $dette->load('client', 'articles');
        return $dette;
    }
    public function archivePaidDebts()
{
    try {
        // Connect to MongoDB
        $mongoClient = $this->mongoClient; // Use the injected MongoDB client
        $mongoDatabase = $mongoClient->selectDatabase($this->databaseName);
        $mongoCollection = $mongoDatabase->selectCollection('archives');
        $articlesCollection = $mongoDatabase->selectCollection('articles');
        $clientsCollection = $mongoDatabase->selectCollection('clients');

        // Archive debts in SQL database
        DB::table('dettes')
            ->join('paiements', 'dettes.id', '=', 'paiements.dette_id')
            ->groupBy('dettes.id')
            ->havingRaw('SUM(paiements.montant_payer) >= dettes.montant')
            ->update(['archived' => true]);

        // Fetch archived debts from SQL database
        $dettes = DB::table('dettes')
            ->leftJoin('paiements', 'dettes.id', '=', 'paiements.dette_id')
            ->select('dettes.id', 'dettes.montant', DB::raw('SUM(paiements.montant_payer) as total_payed'))
            ->groupBy('dettes.id', 'dettes.montant')
            ->havingRaw('SUM(paiements.montant_payer) >= dettes.montant')
            ->get();

        foreach ($dettes as $dette) {
            // Retrieve articles associated with the debt
            $articles = $articlesCollection->find(['dette_id' => $dette->id])->toArray();

            // Retrieve client information associated with the debt
            $client = $clientsCollection->findOne(['dette_id' => $dette->id]);

            // Insert archived debts into MongoDB with the document name being the archive date
            $result = $mongoCollection->insertOne([
                'dette_id' => $dette->id,
                'montant' => $dette->montant,
                'archived_at' => now(),
                'articles' => $articles,
                'client' => $client
            ], [
                'document_name' => now()->format('Y-m-d_H:i:s') // Document name is the current date and time
            ]);

            // Log the result
            if ($result->getInsertedCount() === 1) {
                \Log::info('Archived debt successfully inserted into MongoDB', ['dette_id' => $dette->id]);
            } else {
                \Log::error('Failed to insert debt into MongoDB', ['dette_id' => $dette->id]);
            }
        }

        // Delete archived debts from SQL database
        DB::table('dettes')
            ->where('archived', true)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Paid debts successfully archived and deleted'
        ]);

    } catch (MongoException $e) {
        \Log::error('Error archiving paid debts into MongoDB', ['message' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Error archiving paid debts into MongoDB: ' . $e->getMessage()
        ], 500);
    } catch (\Exception $e) {
        \Log::error('Error archiving paid debts', ['message' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Error archiving paid debts: ' . $e->getMessage()
        ], 500);
    }
}

}