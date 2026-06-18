<?php

namespace App\Policies;

use App\Models\User;

/**
 * Seuls super-admin et drh-groupe administrent les comptes.
 * Un drh-groupe ne peut pas modifier ni supprimer un super-admin.
 */
class UtilisateurPolicy
{
    /** Le Super Admin court-circuite toutes les vérifications. */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('utilisateur.view');
    }

    public function view(User $user, User $cible): bool
    {
        return $user->can('utilisateur.view');
    }

    public function create(User $user): bool
    {
        return $user->can('utilisateur.create');
    }

    public function update(User $user, User $cible): bool
    {
        return $user->can('utilisateur.update') && ! $cible->hasRole('super-admin');
    }

    public function delete(User $user, User $cible): bool
    {
        return $user->can('utilisateur.delete')
            && $cible->id !== $user->id
            && ! $cible->hasRole('super-admin');
    }
}
