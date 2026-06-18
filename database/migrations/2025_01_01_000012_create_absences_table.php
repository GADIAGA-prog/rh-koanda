<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->boolean('justifiee')->default(false);
            $table->string('motif')->nullable();
            $table->string('justificatif')->nullable();
            $table->timestamps();
            $table->index(['filiale_id', 'employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
