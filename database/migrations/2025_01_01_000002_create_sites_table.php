<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->string('nom');
            $table->string('ville')->nullable();
            $table->string('adresse')->nullable();
            $table->boolean('statut')->default(true);
            $table->timestamps();
            $table->index(['filiale_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
