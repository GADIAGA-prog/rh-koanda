<?php

namespace App\Policies;

use App\Models\Fonction;
use App\Models\User;

class FonctionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('organisation.view');
    }

    public function view(User $user, Fonction $fonction): bool
    {
        return $user->can('organisation.view');
    }

    public function create(User $user): bool
    {
        return $user->can('organisation.create');
    }

    public function update(User $user, Fonction $fonction): bool
    {
        return $user->can('organisation.update');
    }

    public function delete(User $user, Fonction $fonction): bool
    {
        return $user->can('organisation.delete');
    }
}
