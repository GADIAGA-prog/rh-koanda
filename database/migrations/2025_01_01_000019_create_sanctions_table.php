<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiale_id')->constrained('filiales')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type'); // avertissement, blame, mise_a_pied, licenciement
            $table->date('date_sanction');
            $table->text('motif');
            $table->string('document')->nullable();
            $table->foreignId('prononce_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['filiale_id', 'employe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
