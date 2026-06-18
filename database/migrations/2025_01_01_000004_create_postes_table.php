<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->string('intitule');
            $table->string('categorie')->nullable();
            $table->timestamps();
            $table->index(['filiale_id', 'departement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postes');
    }
};
