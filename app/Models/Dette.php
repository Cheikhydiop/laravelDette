<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Dette;


class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',      // Montant total dû pour la dette
        'client_id',    // Identifiant du client
        'date_dette', 
        'article_id'
    ];

    protected $hidden = ['updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

   


    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')->withPivot('quantite', 'prix_vente');
    }
    


    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function getMontantPayeAttribute()
    {
        return $this->paiements->sum('montant_payer');
    }

    // Méthode pour vérifier si la dette est soldée
    public function isSold()
    {
        return $this->montant == $this->montant_paye;
    }

    protected static function booted()
    {
        static::updated(function ($dette) {
            DB::beginTransaction();

            try {
                foreach ($dette->articles as $articleData) {
                    $article = Article::find($articleData->id);
                    $ancienneQuantite = $dette->getOriginal('quantite');
                    $nouvelleQuantite = $articleData->pivot->quantite;

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
        });
    }

    
}
