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
       
        
            Schema::create('articles', function (Blueprint $table) {
                $table->id(); // Clé primaire auto-incrémentée
                $table->string('reference')->unique(); // Référence unique
                $table->string('libeller'); // Libellé
                $table->integer('quantite'); // Quantité en stock
                $table->decimal('prix', 10, 2); // Prix avec 10 chiffres au total et 2 après la virgule
                $table->timestamps(); // Champs created_at et updated_at
            });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
