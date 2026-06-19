<?php

namespace App\Policies;

use App\Models\BulletinPaie;
use App\Models\User;

class BulletinPaiePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('paie.view');
    }

    public function view(User $user, BulletinPaie $bulletin): bool
    {
        return $user->can('paie.view') && $this->dansPerimetre($user, $bulletin);
    }

    public function create(User $user): bool
    {
        return $user->can('paie.create');
    }

    public function update(User $user, BulletinPaie $bulletin): bool
    {
        return $user->can('paie.update') && $this->dansPerimetre($user, $bulletin);
    }

    public function delete(User $user, BulletinPaie $bulletin): bool
    {
        return $user->can('paie.update') && $this->dansPerimetre($user, $bulletin);
    }

    protected function dansPerimetre(User $user, BulletinPaie $bulletin): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($bulletin->filiale_id, $user->filialesAccessibles());
    }
}
