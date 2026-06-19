<?php

namespace App\Models;

use App\Models\Enums\ModeCalcul;
use App\Models\Enums\TypeRubrique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rubrique de paie paramétrable. filiale_id null = commune au groupe.
 * Volontairement NON scoppée par FilialeScope : une rubrique « groupe »
 * (filiale_id null) doit rester visible par toutes les filiales.
 */
class RubriquePaie extends Model
{
    protected $table = 'rubriques_paie';

    protected $fillable = [
        'filiale_id', 'code', 'libelle', 'type', 'mode_calcul',
        'montant', 'taux', 'base_calcul', 'imposable', 'ordre', 'actif',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'taux' => 'decimal:4',
        'imposable' => 'boolean',
        'actif' => 'boolean',
        'type' => TypeRubrique::class,
        'mode_calcul' => ModeCalcul::class,
    ];

    public function filiale(): BelongsTo { return $this->belongsTo(Filiale::class); }

    public function scopeActives($query) { return $query->where('actif', true); }

    /** Rubriques applicables à une filiale : les communes (null) + celles de la filiale. */
    public function scopePourFiliale($query, int $filialeId)
    {
        return $query->where(fn ($q) => $q->whereNull('filiale_id')->orWhere('filiale_id', $filialeId));
    }
}
