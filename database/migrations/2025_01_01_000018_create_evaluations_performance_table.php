<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluations_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('evaluateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('periode'); // ex: 2025-S1
            $table->text('objectifs')->nullable();
            $table->decimal('note_globale', 4, 2)->nullable(); // /20 ou /5
            $table->text('commentaire')->nullable();
            $table->decimal('prime_proposee', 15, 2)->nullable();
            $table->timestamps();
            $table->index(['filiale_id', 'employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations_performance');
    }
};
