<?php

namespace App\Policies;

use App\Models\Sanction;
use App\Models\User;

class SanctionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('sanction.view');
    }

    public function view(User $user, Sanction $sanction): bool
    {
        return $user->can('sanction.view') && $this->dansPerimetre($user, $sanction);
    }

    public function create(User $user): bool
    {
        return $user->can('sanction.create');
    }

    public function update(User $user, Sanction $sanction): bool
    {
        return $user->can('sanction.update') && $this->dansPerimetre($user, $sanction);
    }

    public function delete(User $user, Sanction $sanction): bool
    {
        return $user->can('sanction.update') && $this->dansPerimetre($user, $sanction);
    }

    protected function dansPerimetre(User $user, Sanction $sanction): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($sanction->filiale_id, $user->filialesAccessibles());
    }
}
