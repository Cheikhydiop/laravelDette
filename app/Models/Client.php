<?php

namespace App\Models;

use App\Scopes\TelephoneScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['surname', 'telephone', 'adresse', 'user_id', 'email', 'categorie_id', 'max_montant'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $attributes = [
        'categorie_id' => 3,  
        'max_montant' => null,  
    ];

    public static function scopeWithTelephone($query, $telephone)
    {
        return $query->withoutGlobalScope(TelephoneScope::class)
                     ->where('telephone', $telephone);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}
