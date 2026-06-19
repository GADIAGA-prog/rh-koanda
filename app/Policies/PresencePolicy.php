<?php

namespace App\Policies;

use App\Models\Presence;
use App\Models\User;

class PresencePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('presence.view');
    }

    public function view(User $user, Presence $presence): bool
    {
        return $user->can('presence.view') && $this->dansPerimetre($user, $presence);
    }

    public function create(User $user): bool
    {
        return $user->can('presence.create');
    }

    public function update(User $user, Presence $presence): bool
    {
        return $user->can('presence.update') && $this->dansPerimetre($user, $presence);
    }

    public function delete(User $user, Presence $presence): bool
    {
        return $user->can('presence.update') && $this->dansPerimetre($user, $presence);
    }

    protected function dansPerimetre(User $user, Presence $presence): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($presence->filiale_id, $user->filialesAccessibles());
    }
}
