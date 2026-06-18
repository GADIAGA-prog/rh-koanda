<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 30)->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('cnib', 30)->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('situation_familiale')->nullable();
            $table->unsignedSmallInteger('nombre_enfants')->default(0);

            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->foreignId('poste_id')->nullable()->constrained('postes')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('date_embauche')->nullable();
            $table->string('photo')->nullable();
            $table->string('statut')->default('actif'); // App\Models\Enums\StatutEmploye
            $table->timestamps();
            $table->softDeletes();

            $table->index(['filiale_id', 'statut']);
            $table->index(['filiale_id', 'departement_id']);
            $table->index(['nom', 'prenom']);
        });

        // Boucle la FK responsable de filiale vers un employé.
        Schema::table('filiales', function (Blueprint $table) {
            $table->foreign('responsable_id')->references('id')->on('employes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('filiales', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
        });
        Schema::dropIfExists('employes');
    }
};
