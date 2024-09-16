<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Paiement;
use App\Models\Dette;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Gère la mise à jour des stocks et des paiements lors de la création d'une dette.
     */
    public function handleCreation(Dette $dette)
    {
        DB::beginTransaction();
        try {
            // Validation et mise à jour des stocks
            foreach ($dette->articles as $articleData) {
                $article = Article::find($articleData->id);
                if ($article->quantite < $articleData->pivot->qteVente) {
                    throw new \Exception('Quantité en stock insuffisante pour l\'article ' . $articleData->id);
                }

                // Mise à jour des quantités d'articles
                $article->quantite -= $articleData->pivot->qteVente;
                $article->save();
            }

            // Si un paiement est associé
            if ($dette->paiement && $dette->paiement->montant_payer) {
                Paiement::create([
                    'montant_payer' => $dette->paiement->montant_payer,
                    'dette_id' => $dette->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Gère la mise à jour des stocks lors de la modification d'une dette.
     */
    public function handleUpdate(Dette $dette)
    {
        DB::beginTransaction();
        try {
            // Recalcul et mise à jour du stock si des modifications sont faites sur les articles
            foreach ($dette->articles as $articleData) {
                $article = Article::find($articleData->id);

                $ancienneQuantite = $dette->getOriginal('qteVente');
                $nouvelleQuantite = $articleData->pivot->qteVente;

                // Ajustement des stocks
                if ($nouvelleQuantite != $ancienneQuantite) {
                    $article->quantite += ($ancienneQuantite - $nouvelleQuantite);
                    $article->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
