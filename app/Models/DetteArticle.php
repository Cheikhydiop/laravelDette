<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetteArticle extends Model
{
    protected $table = 'article_dette';  // Spécifie la table associée

    protected $fillable = [
        'dette_id',
        'article_id',
        'quantite',
        'prix_vente',
    ];

    // Désactive les timestamps si la table ne les utilise pas
    public $timestamps = false;

    // Vous pouvez choisir d'utiliser une clé primaire unique si possible
    // protected $primaryKey = 'id';  // Décommentez si vous ajoutez une colonne `id`
    // public $incrementing = true;   // Décommentez si vous ajoutez une colonne `id` et utilisez l'auto-incrémentation
}
