<?php

namespace App\Policies;

use App\Models\Departement;
use App\Models\User;

class DepartementPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('organisation.view');
    }

    public function view(User $user, Departement $departement): bool
    {
        return $user->can('organisation.view') && $this->dansPerimetre($user, $departement);
    }

    public function create(User $user): bool
    {
        return $user->can('organisation.create');
    }

    public function update(User $user, Departement $departement): bool
    {
        return $user->can('organisation.update') && $this->dansPerimetre($user, $departement);
    }

    public function delete(User $user, Departement $departement): bool
    {
        return $user->can('organisation.delete') && $this->dansPerimetre($user, $departement);
    }

    protected function dansPerimetre(User $user, Departement $departement): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($departement->filiale_id, $user->filialesAccessibles());
    }
}
