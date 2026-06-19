<?php

namespace App\Policies;

use App\Models\Poste;
use App\Models\User;

class PostePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('organisation.view');
    }

    public function view(User $user, Poste $poste): bool
    {
        return $user->can('organisation.view') && $this->dansPerimetre($user, $poste);
    }

    public function create(User $user): bool
    {
        return $user->can('organisation.create');
    }

    public function update(User $user, Poste $poste): bool
    {
        return $user->can('organisation.update') && $this->dansPerimetre($user, $poste);
    }

    public function delete(User $user, Poste $poste): bool
    {
        return $user->can('organisation.delete') && $this->dansPerimetre($user, $poste);
    }

    protected function dansPerimetre(User $user, Poste $poste): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($poste->filiale_id, $user->filialesAccessibles());
    }
}
