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
        Schema::create('categories_clients', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique(); // Gold, Silver, Bronze
            $table->timestamps();
        });
        
        // Ajouter une colonne categorie_id dans la table clients
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('categorie_id')->default(3)->constrained('categories_clients')->onDelete('cascade'); // Par défaut, Bronze
            $table->decimal('max_montant', 8, 2)->nullable(); // Montant max pour Silver
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_clients');
        
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['categorie_id']);
            $table->dropColumn('categorie_id');
            $table->dropColumn('max_montant');
        });
    }
};
