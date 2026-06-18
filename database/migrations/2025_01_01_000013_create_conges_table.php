<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type_conge'); // App\Models\Enums\TypeConge
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('nombre_jours', 5, 1)->default(0);
            $table->text('motif')->nullable();
            $table->string('statut_validation')->default('en_attente'); // App\Models\Enums\StatutConge
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->text('motif_refus')->nullable();
            $table->timestamps();

            $table->index(['filiale_id', 'statut_validation']);
            $table->index(['employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
