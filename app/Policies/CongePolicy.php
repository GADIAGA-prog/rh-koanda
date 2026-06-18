<?php

namespace App\Policies;

use App\Models\Conge;
use App\Models\User;

class CongePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('conge.view');
    }

    public function view(User $user, Conge $conge): bool
    {
        return $user->can('conge.view') && $this->dansPerimetre($user, $conge);
    }

    public function create(User $user): bool
    {
        return $user->can('conge.create');
    }

    public function valider(User $user, Conge $conge): bool
    {
        return $user->can('conge.valider')
            && (
                $this->dansPerimetre($user, $conge)
                || $conge->employe?->manager_id === $user->employe?->id
            );
    }

    protected function dansPerimetre(User $user, Conge $conge): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($conge->filiale_id, $user->filialesAccessibles());
    }
}
