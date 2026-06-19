<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bulletins_paie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('periode', 7); // AAAA-MM
            $table->decimal('salaire_base', 15, 2)->default(0);
            $table->decimal('total_gains', 15, 2)->default(0);
            $table->decimal('salaire_brut', 15, 2)->default(0);
            $table->decimal('total_cotisations', 15, 2)->default(0);
            $table->decimal('total_retenues', 15, 2)->default(0);
            $table->decimal('net_a_payer', 15, 2)->default(0);
            $table->decimal('cout_employeur', 15, 2)->default(0);
            $table->string('statut')->default('brouillon'); // App\Models\Enums\StatutBulletin
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employe_id', 'periode']);
            $table->index(['filiale_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins_paie');
    }
};
