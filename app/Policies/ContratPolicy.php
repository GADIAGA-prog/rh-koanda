<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;

class ContratPolicy
{
    /** Le Super Admin court-circuite toutes les vérifications. */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('contrat.view');
    }

    public function view(User $user, Contrat $contrat): bool
    {
        return $user->can('contrat.view') && $this->memeFiliale($user, $contrat);
    }

    public function create(User $user): bool
    {
        return $user->can('contrat.create');
    }

    public function update(User $user, Contrat $contrat): bool
    {
        return $user->can('contrat.update') && $this->memeFiliale($user, $contrat);
    }

    public function renouveler(User $user, Contrat $contrat): bool
    {
        return $user->can('contrat.update') && $this->memeFiliale($user, $contrat);
    }

    public function delete(User $user, Contrat $contrat): bool
    {
        return $user->can('contrat.delete') && $this->memeFiliale($user, $contrat);
    }

    /** Le contrat appartient-il au périmètre de l'utilisateur ? */
    protected function memeFiliale(User $user, Contrat $contrat): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($contrat->filiale_id, $user->filialesAccessibles());
    }
}
