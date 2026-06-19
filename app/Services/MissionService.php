<?php

namespace App\Services;

use App\Models\Enums\StatutMission;
use App\Models\Mission;
use Illuminate\Support\Carbon;

class MissionService
{
    /** Montant total = nombre de jours × indemnité journalière + autres frais. */
    public function calculerMontant(array $donnees): float
    {
        $jours = (int) ($donnees['nombre_jours'] ?? 0);
        $indemnite = (float) ($donnees['indemnite_journaliere'] ?? 0);
        $autres = (float) ($donnees['autres_frais'] ?? 0);

        return round($jours * $indemnite + $autres, 2);
    }

    /** Déduit le nombre de jours des dates (inclusif). */
    public function joursEntre(string $depart, string $retour): int
    {
        return Carbon::parse($depart)->diffInDays(Carbon::parse($retour)) + 1;
    }

    public function creer(array $donnees): Mission
    {
        $donnees['nombre_jours'] = $donnees['nombre_jours']
            ?? $this->joursEntre($donnees['date_depart'], $donnees['date_retour']);
        $donnees['montant_total'] = $this->calculerMontant($donnees);
        $donnees['statut'] ??= StatutMission::BROUILLON->value;

        return Mission::create($donnees);
    }

    public function modifier(Mission $mission, array $donnees): Mission
    {
        $donnees['nombre_jours'] = $donnees['nombre_jours']
            ?? $this->joursEntre($donnees['date_depart'], $donnees['date_retour']);
        $donnees['montant_total'] = $this->calculerMontant($donnees);

        $mission->update($donnees);

        return $mission->refresh();
    }

    public function soumettre(Mission $mission): Mission
    {
        $mission->update(['statut' => StatutMission::SOUMISE->value, 'motif_refus' => null]);

        return $mission->refresh();
    }

    public function valider(Mission $mission, int $validateurId): Mission
    {
        $mission->update([
            'statut' => StatutMission::VALIDEE->value,
            'validateur_id' => $validateurId,
            'valide_le' => now(),
            'motif_refus' => null,
        ]);

        return $mission->refresh();
    }

    public function refuser(Mission $mission, int $validateurId, ?string $motif = null): Mission
    {
        $mission->update([
            'statut' => StatutMission::REFUSEE->value,
            'validateur_id' => $validateurId,
            'valide_le' => now(),
            'motif_refus' => $motif,
        ]);

        return $mission->refresh();
    }

    public function cloturer(Mission $mission): Mission
    {
        $mission->update(['statut' => StatutMission::CLOTUREE->value]);

        return $mission->refresh();
    }
}
