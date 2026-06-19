<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lignes_bulletin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_paie_id')->constrained('bulletins_paie')->cascadeOnDelete();
            $table->string('libelle');
            $table->string('type'); // gain/retenue/cotisation
            $table->decimal('base', 15, 2)->default(0);
            $table->decimal('taux', 8, 4)->nullable();
            $table->decimal('montant', 15, 2)->default(0);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_bulletin');
    }
};
