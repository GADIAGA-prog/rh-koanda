<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtre automatiquement chaque requête sur les modèles « scoppés »
 * selon les filiales accessibles de l'utilisateur connecté.
 *
 * - Aucun utilisateur (console, jobs, seeders) -> aucun filtrage.
 * - Rôle Groupe (super-admin, DG, DRH, auditeur) -> accès total.
 * - Sinon -> restreint aux filiales accessibles de l'utilisateur.
 *
 * C'est la garantie technique de l'isolation des données entre filiales.
 */
class FilialeScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if (method_exists($user, 'peutVoirToutLeGroupe') && $user->peutVoirToutLeGroupe()) {
            return;
        }

        $builder->whereIn(
            $model->getTable() . '.filiale_id',
            method_exists($user, 'filialesAccessibles') ? $user->filialesAccessibles() : [$user->filiale_id]
        );
    }
}
