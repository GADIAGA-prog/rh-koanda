<?php

namespace App\Policies;

use App\Models\DocumentRh;
use App\Models\Enums\Confidentialite;
use App\Models\User;

class DocumentRhPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('document.view');
    }

    public function view(User $user, DocumentRh $document): bool
    {
        return $user->can('document.view')
            && $this->dansPerimetre($user, $document)
            && $this->niveauAutorise($user, $document);
    }

    public function create(User $user): bool
    {
        return $user->can('document.create');
    }

    public function delete(User $user, DocumentRh $document): bool
    {
        return $user->can('document.create') && $this->dansPerimetre($user, $document);
    }

    protected function dansPerimetre(User $user, DocumentRh $document): bool
    {
        return $user->peutVoirToutLeGroupe()
            || in_array($document->filiale_id, $user->filialesAccessibles());
    }

    /** Contrôle le niveau de confidentialité selon le rôle. */
    protected function niveauAutorise(User $user, DocumentRh $document): bool
    {
        return match ($document->confidentialite) {
            Confidentialite::PUBLIC => true,
            Confidentialite::RH => $user->can('employe.view'),
            Confidentialite::DIRECTION => $user->peutVoirToutLeGroupe() || $user->hasRole('rh-filiale'),
        };
    }
}
