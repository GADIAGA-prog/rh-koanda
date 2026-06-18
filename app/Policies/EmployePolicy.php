<?php

namespace App\Policies;

use App\Models\Employe;
use App\Models\User;

class EmployePolicy
{
    /** Le Super Admin court-circuite toutes les vérifications. */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('employe.view');
    }

    public function view(User $user, Employe $employe): bool
    {
        return $user->can('employe.view') && $this->memeFiliale($user, $employe);
    }

    public function create(User $user): bool
    {
        return $user->can('employe.create');
    }

    public function update(User $user, Employe $employe): bool
    {
        return $user->can('employe.update') && $this->memeFiliale($user, $employe);
    }

    public function delete(User $user, Employe $employe): bool
    {
        return $user->can('employe.delete') && $this->memeFiliale($user, $employe);
    }

    /** L'employé appartient-il au périmètre de l'utilisateur ? */
    protected function memeFiliale(User $user, Employe $employe): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($employe->filiale_id, $user->filialesAccessibles());
    }
}
