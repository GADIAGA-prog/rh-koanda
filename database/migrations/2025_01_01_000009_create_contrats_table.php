<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('reference', 50)->nullable();
            $table->string('type_contrat'); // App\Models\Enums\TypeContrat
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('salaire_base', 15, 2)->default(0);
            $table->string('devise', 3)->default('XOF');
            $table->string('statut')->default('actif'); // App\Models\Enums\StatutContrat
            $table->string('fichier_contrat')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['filiale_id', 'statut']);
            $table->index(['employe_id']);
            $table->index(['date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
