<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('absence.view');
    }

    public function view(User $user, Absence $absence): bool
    {
        return $user->can('absence.view') && $this->dansPerimetre($user, $absence);
    }

    public function create(User $user): bool
    {
        return $user->can('absence.create');
    }

    public function update(User $user, Absence $absence): bool
    {
        return $user->can('absence.update') && $this->dansPerimetre($user, $absence);
    }

    public function delete(User $user, Absence $absence): bool
    {
        return $user->can('absence.update') && $this->dansPerimetre($user, $absence);
    }

    protected function dansPerimetre(User $user, Absence $absence): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($absence->filiale_id, $user->filialesAccessibles());
    }
}
