<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [ 'reference',
    'libeller',
    'quantite',
    'prix'];

    public function scopeDisponible($query)
    {
        return $query->where('quantite', '>', 0);
    }

    public function scopeNonDisponible($query)
    {
        return $query->where('quantite', '=', 0);
    }


   
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')->withPivot('quantite', 'prix_vente');
    }
    
    public function demandes()
    {
        return $this->belongsToMany(Demande::class)
                    ->withPivot('quantite'); // Assurez-vous d'inclure 'quantite' si nécessaire
    }
 
}
