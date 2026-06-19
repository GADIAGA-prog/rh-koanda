<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rubriques_paie', function (Blueprint $table) {
            $table->id();
            // null = rubrique commune à tout le groupe.
            $table->foreignId('filiale_id')->nullable()->constrained('filiales')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('libelle');
            $table->string('type'); // App\Models\Enums\TypeRubrique (gain/retenue/cotisation)
            $table->string('mode_calcul'); // App\Models\Enums\ModeCalcul (fixe/pourcentage)
            $table->decimal('montant', 15, 2)->nullable();
            $table->decimal('taux', 8, 4)->nullable();
            $table->string('base_calcul')->nullable(); // salaire_base | brut
            $table->boolean('imposable')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['filiale_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubriques_paie');
    }
};
