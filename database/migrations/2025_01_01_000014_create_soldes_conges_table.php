<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('soldes_conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type_conge')->default('annuel');
            $table->year('annee');
            $table->decimal('droit_total', 5, 1)->default(0);
            $table->decimal('jours_pris', 5, 1)->default(0);
            $table->timestamps();

            $table->unique(['employe_id', 'type_conge', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldes_conges');
    }
};
