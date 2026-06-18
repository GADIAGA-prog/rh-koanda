<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('ancienne_filiale_id')->nullable()->constrained('filiales')->nullOnDelete();
            $table->foreignId('nouvelle_filiale_id')->nullable()->constrained('filiales')->nullOnDelete();
            $table->foreignId('ancien_poste_id')->nullable()->constrained('postes')->nullOnDelete();
            $table->foreignId('nouveau_poste_id')->nullable()->constrained('postes')->nullOnDelete();
            $table->date('date_effet');
            $table->string('motif')->nullable();
            $table->foreignId('decide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['employe_id', 'date_effet']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
