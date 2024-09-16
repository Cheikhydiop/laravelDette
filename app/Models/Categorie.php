<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    // Indiquer explicitement le nom de la table
    

    // Permet de définir les colonnes pouvant être massivement assignées
    protected $fillable = ['libelle'];

    // Définir la relation avec le modèle Client
    public function clients()
    {
        return $this->hasMany(Client::class, 'categorie_id');
    }
}

