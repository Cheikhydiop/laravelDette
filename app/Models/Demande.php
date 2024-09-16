<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    // Autres attributs et méthodes du modèle


    protected $fillable = ['client_id', 'status'];
    /**
     * Get the client that owns the demande.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);

    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)
                    ->withPivot('quantite'); // Assurez-vous d'inclure 'quantite' si nécessaire
    }
}
