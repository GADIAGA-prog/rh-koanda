<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents_rh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type_document'); // contrat, diplome, cnib, attestation, fiche_poste, sanction, certificat
            $table->string('titre');
            $table->string('fichier');
            $table->date('date_expiration')->nullable();
            $table->string('confidentialite')->default('rh'); // App\Models\Enums\Confidentialite
            $table->foreignId('ajoute_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['filiale_id', 'employe_id']);
            $table->index(['date_expiration']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_rh');
    }
};
