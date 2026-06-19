<?php

namespace App\Policies;

use App\Models\Formation;
use App\Models\User;

class FormationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('formation.view');
    }

    public function view(User $user, Formation $formation): bool
    {
        return $user->can('formation.view') && $this->dansPerimetre($user, $formation);
    }

    public function create(User $user): bool
    {
        return $user->can('formation.create');
    }

    public function update(User $user, Formation $formation): bool
    {
        return $user->can('formation.update') && $this->dansPerimetre($user, $formation);
    }

    public function delete(User $user, Formation $formation): bool
    {
        return $user->can('formation.update') && $this->dansPerimetre($user, $formation);
    }

    protected function dansPerimetre(User $user, Formation $formation): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($formation->filiale_id, $user->filialesAccessibles());
    }
}
