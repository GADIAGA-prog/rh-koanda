<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\User;

class MissionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('mission.view');
    }

    public function view(User $user, Mission $mission): bool
    {
        return $user->can('mission.view') && $this->dansPerimetre($user, $mission);
    }

    public function create(User $user): bool
    {
        return $user->can('mission.create');
    }

    public function update(User $user, Mission $mission): bool
    {
        return $user->can('mission.create')
            && $mission->estModifiable()
            && $this->dansPerimetre($user, $mission);
    }

    public function valider(User $user, Mission $mission): bool
    {
        return $user->can('mission.valider') && $this->dansPerimetre($user, $mission);
    }

    public function delete(User $user, Mission $mission): bool
    {
        return $user->can('mission.create')
            && $mission->estModifiable()
            && $this->dansPerimetre($user, $mission);
    }

    protected function dansPerimetre(User $user, Mission $mission): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($mission->filiale_id, $user->filialesAccessibles());
    }
}
