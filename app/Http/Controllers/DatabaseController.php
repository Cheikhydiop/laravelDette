<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseInterface;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseInterface $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function archivePaidDebts()
    {
        try {
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
                ->get();

            // Insert archived debts into selected database
            foreach ($dettes as $dette) {
                $data = [
                    'dette_id' => $dette->id,
                    'montant' => $dette->montant,
                    'archived_at' => now(),
                ];
                $result = $this->databaseService->insertDocument($data);

                if ($result) {
                    \Log::info('Archived debt successfully inserted into database', ['dette_id' => $dette->id]);
                } else {
                    \Log::error('Failed to insert debt into database', ['dette_id' => $dette->id]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Paid debts successfully archived'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error archiving paid debts', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error archiving paid debts: ' . $e->getMessage()
            ], 500);
        }
    }
}
