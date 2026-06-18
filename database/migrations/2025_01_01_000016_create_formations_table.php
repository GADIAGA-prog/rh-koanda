<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->string('intitule');
            $table->text('objectif')->nullable();
            $table->string('organisme')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('cout', 15, 2)->default(0);
            $table->string('devise', 3)->default('XOF');
            $table->string('statut')->default('planifiee');
            $table->timestamps();
            $table->index(['filiale_id']);
        });

        Schema::create('formation_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->boolean('present')->default(true);
            $table->string('resultat')->nullable();
            $table->timestamps();
            $table->unique(['formation_id', 'employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_employe');
        Schema::dropIfExists('formations');
    }
};
