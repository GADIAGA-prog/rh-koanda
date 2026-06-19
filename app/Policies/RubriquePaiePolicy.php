<?php

namespace App\Policies;

use App\Models\RubriquePaie;
use App\Models\User;

class RubriquePaiePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('paie.view');
    }

    public function view(User $user, RubriquePaie $rubrique): bool
    {
        return $user->can('paie.view') && $this->gerable($user, $rubrique, lecture: true);
    }

    public function create(User $user): bool
    {
        return $user->can('paie.create');
    }

    public function update(User $user, RubriquePaie $rubrique): bool
    {
        return $user->can('paie.update') && $this->gerable($user, $rubrique);
    }

    public function delete(User $user, RubriquePaie $rubrique): bool
    {
        return $user->can('paie.update') && $this->gerable($user, $rubrique);
    }

    /**
     * Une rubrique « groupe » (filiale_id null) n'est gérable que par un rôle Groupe.
     * Une rubrique de filiale n'est gérable que dans le périmètre de l'utilisateur.
     */
    protected function gerable(User $user, RubriquePaie $rubrique, bool $lecture = false): bool
    {
        if ($rubrique->filiale_id === null) {
            return $lecture || $user->peutVoirToutLeGroupe();
        }

        return $user->peutVoirToutLeGroupe()
            || in_array($rubrique->filiale_id, $user->filialesAccessibles());
    }
}
