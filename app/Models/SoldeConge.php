<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldeConge extends Model
{
    protected $table = 'soldes_conges';

    protected $fillable = [
        'employe_id', 'type_conge', 'annee', 'droit_total', 'jours_pris',
    ];

    protected $casts = [
        'droit_total' => 'decimal:1',
        'jours_pris' => 'decimal:1',
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }

    public function getSoldeRestantAttribute(): float
    {
        return (float) $this->droit_total - (float) $this->jours_pris;
    }
}
