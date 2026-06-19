<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('objet');
            $table->string('destination');
            $table->string('lieu_depart')->nullable();
            $table->date('date_depart');
            $table->date('date_retour');
            $table->unsignedSmallInteger('nombre_jours')->default(1);
            $table->string('moyen_transport')->nullable();
            $table->decimal('indemnite_journaliere', 15, 2)->default(0);
            $table->decimal('autres_frais', 15, 2)->default(0);
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->string('devise', 3)->default('XOF');
            $table->string('statut')->default('brouillon'); // App\Models\Enums\StatutMission
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->string('motif_refus')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['filiale_id', 'statut']);
            $table->index(['employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
