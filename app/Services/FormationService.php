<?php

namespace App\Services;

use App\Models\Competence;
use App\Models\Formation;
use App\Models\Scopes\FilialeScope;
use Illuminate\Support\Facades\DB;

class FormationService
{
    public function creer(array $donnees): Formation
    {
        return Formation::create($donnees);
    }

    public function modifier(Formation $formation, array $donnees): Formation
    {
        $formation->update($donnees);

        return $formation->refresh();
    }

    public function ajouterParticipant(Formation $formation, int $employeId, array $pivot = []): void
    {
        $formation->participants()->syncWithoutDetaching([
            $employeId => [
                'present' => $pivot['present'] ?? true,
                'resultat' => $pivot['resultat'] ?? null,
            ],
        ]);
    }

    public function majParticipant(Formation $formation, int $employeId, array $pivot): void
    {
        $formation->participants()->updateExistingPivot($employeId, [
            'present' => $pivot['present'] ?? false,
            'resultat' => $pivot['resultat'] ?? null,
        ]);
    }

    public function retirerParticipant(Formation $formation, int $employeId): void
    {
        $formation->participants()->detach($employeId);
    }

    /** Acquisition d'une compétence par un employé (niveau 1 à 5). */
    public function acquerirCompetence(int $employeId, string $libelle, int $niveau, ?string $domaine = null): void
    {
        $competence = Competence::firstOrCreate(['libelle' => $libelle], ['domaine' => $domaine]);

        $competence->employes()->syncWithoutDetaching([$employeId => ['niveau' => $niveau]]);
    }

    /** Coût total des formations par filiale (périmètre fourni, null = groupe). */
    public function coutsParFiliale(?array $filiales)
    {
        return Formation::withoutGlobalScope(FilialeScope::class)
            ->when($filiales, fn ($q) => $q->whereIn('filiale_id', $filiales))
            ->select('filiale_id', DB::raw('count(*) as nombre'), DB::raw('sum(cout) as cout_total'))
            ->groupBy('filiale_id')
            ->get()
            ->keyBy('filiale_id');
    }
}
