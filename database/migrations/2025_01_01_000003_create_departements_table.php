<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('nom');
            $table->string('code', 30)->nullable();
            $table->timestamps();
            $table->index(['filiale_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departements');
    }
};
