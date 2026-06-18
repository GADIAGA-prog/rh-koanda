<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->date('date_presence');
            $table->time('heure_arrivee')->nullable();
            $table->time('heure_depart')->nullable();
            $table->string('statut')->default('present'); // App\Models\Enums\StatutPresence
            $table->string('commentaire')->nullable();
            $table->timestamps();

            $table->unique(['employe_id', 'date_presence']);
            $table->index(['filiale_id', 'date_presence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
