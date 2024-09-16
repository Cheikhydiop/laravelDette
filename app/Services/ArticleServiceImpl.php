<?php
namespace App\Services;

use App\Facades\ArticleRepositoryFacade;
use App\Http\Resources\ArticleRessource;
use Exception;
use App\Models\Article; // Chemin correct pour le modèle Article


class ArticleServiceImpl
{
    protected $articleRepositoryFacade;

    public function __construct(ArticleRepositoryFacade $articleRepositoryFacade)
    {
        $this->articleRepositoryFacade = $articleRepositoryFacade;
    }

    public function getArticles($disponible, $perPage)
    {
        return $this->articleRepositoryFacade->getArticles($disponible, $perPage);
    }

    public function findArticleById($id)
    {
        $article = $this->articleRepositoryFacade->findArticleById($id);
        if ($article) {
            return new ArticleRessource($article);
        } else {
            throw new Exception('Article non trouvé');
        }
    }

    public function findArticleByLibelle($libelle)
    {
        $article = $this->articleRepositoryFacade->findArticleByLibelle($libelle);
        if ($article) {
            return new ArticleRessource($article);
        } else {
            throw new Exception('Objet non trouvé');
        }
    }

 
 
    public function updateStock(array $articlesData)
    {
        $failedArticles = []; // Tableau pour stocker les articles échoués

        foreach ($articlesData as $articleData) {
            if (isset($articleData['id']) && isset($articleData['quantite']) && isset($articleData['libeller'])) {
                $article = Article::where('id', $articleData['id'])
                                  ->where('libeller', $articleData['libeller'])
                                  ->first();

                if ($article) {
                    // Met à jour la quantité de l'article
                    $article->quantite += $articleData['quantite'];
                    $article->save();
                } else {
                    // Ajoute l'article à la liste des échecs
                    $failedArticles[] = $articleData;
                }
            } else {
                // Ajoute l'article à la liste des échecs
                $failedArticles[] = $articleData;
            }
        }

        $response = [
            'message' => 'Stock mis à jour avec succès.',
            'failed_articles' => $failedArticles // Retourne les articles échoués
        ];

        return $response;
    }
    public function storeArticles(array $articlesData)
    {
        $addedArticles = [];
        // dd($articlesData);
        $failedArticles = [];
    
        foreach ($articlesData as $articleData) {
            try {
                // Validation des données
                $validatedData = $this->validateArticleData($articleData);
    
                // Création de l'article
                $article = Article::create($validatedData);
                $addedArticles[] = $article;
            } catch (\Exception $e) {
                // Capture des erreurs et ajout à la liste des échecs
                $failedArticles[] = [
                    'data' => $articleData,
                    'error' => $e->getMessage(),
                ];
            }
        }
    
        return [
            'message' => 'Articles ajoutés avec succès.',
            'added_articles' => ArticleRessource::collection($addedArticles),
            'failed_articles' => $failedArticles,
        ];
    }
    
    private function validateArticleData($articleData)
    {
        // Exemple de validation des données, adaptez selon vos besoins
        if (empty($articleData['reference']) || empty($articleData['libeller']) ||
            !isset($articleData['quantite']) || !isset($articleData['prix'])) {
            throw new \Exception('Données d\'article manquantes ou invalides.');
        }
    
        return $articleData;
    }
    
}
