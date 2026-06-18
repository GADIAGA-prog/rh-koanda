<?php

namespace App\Models\Concerns;

use App\Models\Filiale;
use App\Models\Scopes\FilialeScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * À appliquer sur tout modèle dont les lignes appartiennent à une filiale :
 * Employe, Contrat, Presence, Absence, Conge, DocumentRh, EvaluationPerformance,
 * Sanction, Affectation, etc.
 *
 * Ajoute :
 *  - le filtrage automatique en lecture (FilialeScope) ;
 *  - l'affectation automatique de filiale_id à la création.
 */
trait BelongsToFiliale
{
    protected static function bootBelongsToFiliale(): void
    {
        static::addGlobalScope(new FilialeScope);

        static::creating(function ($model) {
            if (empty($model->filiale_id) && auth()->check() && auth()->user()->filiale_id) {
                $model->filiale_id = auth()->user()->filiale_id;
            }
        });
    }

    public function filiale(): BelongsTo
    {
        return $this->belongsTo(Filiale::class);
    }

    /**
     * Helper : exécuter une requête sans le filtre de filiale.
     * Réservé aux tableaux de bord Groupe, rapports consolidés et jobs.
     */
    public static function sansFiltreFiliale(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->withoutGlobalScope(FilialeScope::class);
    }
}
