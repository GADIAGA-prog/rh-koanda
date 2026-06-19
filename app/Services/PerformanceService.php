<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\EvaluationPerformance;

class PerformanceService
{
    public function creer(array $donnees, int $evaluateurId): EvaluationPerformance
    {
        $donnees['evaluateur_id'] = $evaluateurId;
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;

        return EvaluationPerformance::create($donnees);
    }

    public function modifier(EvaluationPerformance $evaluation, array $donnees): EvaluationPerformance
    {
        $evaluation->update($donnees);

        return $evaluation->refresh();
    }
}
