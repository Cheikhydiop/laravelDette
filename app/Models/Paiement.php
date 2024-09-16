<?php
// app/Models/Paiement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    // ...

    /**
     * Get the dette that owns the paiement.
     */

     protected $fillable = [
        'montant_payer',  // Montant payé
        'mode_paiement',  // Mode de paiement
        'dette_id',       // Identifiant de la dette
    ];
    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
