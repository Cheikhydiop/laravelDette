<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant_payer', 8, 2);  // Montant payé
            $table->string('mode_paiement', 50)->default('comptant');  // Mode de paiement (comptant par défaut)
            $table->foreignId('dette_id')->constrained('dettes')->onDelete('cascade');  // Clé étrangère vers la table dettes
            $table->timestamps();  // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
