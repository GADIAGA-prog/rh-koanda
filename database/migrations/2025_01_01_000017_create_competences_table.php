<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('competences', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('domaine')->nullable();
            $table->timestamps();
        });

        Schema::create('competence_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competence_id')->constrained('competences')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->unsignedTinyInteger('niveau')->default(1); // 1 à 5
            $table->timestamps();
            $table->unique(['competence_id', 'employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competence_employe');
        Schema::dropIfExists('competences');
    }
};
