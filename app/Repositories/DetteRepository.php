<?php
namespace App\Repositories;

use App\Models\Dette;
use App\Models\Paiement;
use App\Models\Article;
use App\Models\DetteArticle;

class DetteRepository
{
    public function createDette($data)
    {
        // Création de la dette
        $dette = Dette::create([
            'montant' => $data['montant'],
            'client_id' => $data['clientId'],
            'date_dette' => now(),
        ]);

        // Ajout des articles à la dette
        foreach ($data['articles'] as $articleData) {
            $article = Article::find($articleData['articleId']); // Correction ici
            if (!$article) {
                throw new \Exception('Article non trouvé pour l\'ID ' . $articleData['articleId']);
            }
            if ($article->quantite < $articleData['qteVente']) {
                throw new \Exception('Quantité insuffisante pour l\'article ' . $articleData['articleId']);
            }
            $article->quantite -= $articleData['qteVente'];
            $article->save();

            DetteArticle::create([
                'dette_id' => $dette->id,
                'article_id' => $articleData['articleId'],
                'quantite' => $articleData['qteVente'],
                'prix_vente' => $articleData['prixVente'],
            ]);
        }

        // Enregistrement du paiement si présent
        if (isset($data['paiement']['montant'])) {
            Paiement::create([
                'montant_payer' => $data['paiement']['montant'],
                'dette_id' => $dette->id,
            ]);
        }

        return $dette;
    }
}
