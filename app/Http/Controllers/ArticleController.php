<?php

namespace App\Http\Controllers;

use App\Services\ArticleServiceImpl;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleRessource;
use App\Models\Article; // Assurez-vous que ce chemin est correct


use Exception;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceImpl $articleService)
    {
        $this->articleService = $articleService;
    }

    public function verification(Request $request)
    {
        try {
            $disponible = $request->query('disponible');
            $perPage = $request->query('per_page', 10);
            return $this->articleService->getArticles($disponible, $perPage);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            return $this->articleService->findArticleById($id);
            dd($this->articleService);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        $articlesData = $request->input('articles'); 
        return $this->articleService->storeArticles($articlesData);
    }

    public function updateStock(Request $request)
    {
        $articlesData = $request->input('articles');
        
        $validatedData = $request->validate([
            'articles' => 'required|array',
            'articles.*.id' => 'required|integer|exists:articles,id',
            'articles.*.quantite' => 'required|integer|gt:0', 
        ]);
    
        return $this->articleService->updateStock($articlesData);
    }
   
    public function findByLibelle(Request $request)
    {
        $libelle = $request->input('libeller');
        $article = Article::where('libeller', $libelle)->first();
        
        if ($article) {
            return response()->json(new ArticleRessource($article));
        } else {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }
    }

    public function updateOne(Request $request, $id)
     {
        $validatedData = $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);
    
        $article = Article::find($id);
    
        if (!$article) {
            return response()->json([
                'status' => 'error',
                'message' => 'Article non trouvé.',
            ], 404);
        }
    
        $article->quantite += $validatedData['quantite'];
    
        $article->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Article mis à jour avec succès.',
            'data' => $article,
        ], 200);
    }
    

}
