<?php

namespace App\Policies;

use App\Models\Employe;
use App\Models\EvaluationPerformance;
use App\Models\User;

class EvaluationPerformancePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('super-admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('performance.view');
    }

    public function view(User $user, EvaluationPerformance $evaluation): bool
    {
        return $user->can('performance.view') && $this->dansPerimetre($user, $evaluation->filiale_id);
    }

    public function create(User $user): bool
    {
        return $user->can('performance.create');
    }

    public function update(User $user, EvaluationPerformance $evaluation): bool
    {
        return $user->can('performance.create') && $this->peutEvaluer($user, $evaluation->employe);
    }

    public function delete(User $user, EvaluationPerformance $evaluation): bool
    {
        return $this->update($user, $evaluation);
    }

    /**
     * Un RH (employe.update) évalue dans son périmètre ; un manager
     * n'évalue que ses subordonnés directs (manager_id).
     */
    public function peutEvaluer(User $user, Employe $employe): bool
    {
        if ($user->can('employe.update')) {
            return $this->dansPerimetre($user, $employe->filiale_id);
        }

        return $employe->manager_id && optional($user->employe)->id === $employe->manager_id;
    }

    protected function dansPerimetre(User $user, ?int $filialeId): bool
    {
        return $user->peutVoirToutLeGroupe() || in_array($filialeId, $user->filialesAccessibles());
    }
}
