<?php

namespace App\Services;

use App\Models\Conge;
use App\Models\Enums\StatutConge;
use App\Models\SoldeConge;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CongeService
{
    /** Jours ouvrés entre deux dates (hors samedi/dimanche). */
    public function calculerJoursOuvres(Carbon $debut, Carbon $fin): float
    {
        $jours = 0;
        foreach (CarbonPeriod::create($debut, $fin) as $jour) {
            if (! $jour->isWeekend()) {
                $jours++;
            }
        }
        return $jours;
    }

    public function demander(array $donnees): Conge
    {
        $debut = Carbon::parse($donnees['date_debut']);
        $fin = Carbon::parse($donnees['date_fin']);
        $donnees['nombre_jours'] = $this->calculerJoursOuvres($debut, $fin);
        $donnees['statut_validation'] = StatutConge::EN_ATTENTE->value;

        return Conge::create($donnees);
    }

    public function valider(Conge $conge, int $validateurId): Conge
    {
        return DB::transaction(function () use ($conge, $validateurId) {
            $conge->update([
                'statut_validation' => StatutConge::VALIDE->value,
                'validateur_id' => $validateurId,
                'valide_le' => now(),
            ]);

            // Décrémente le solde pour les congés annuels.
            if ($conge->type_conge->value === 'annuel') {
                $this->imputerSolde($conge);
            }

            return $conge->refresh();
        });
    }

    public function refuser(Conge $conge, int $validateurId, ?string $motif = null): Conge
    {
        $conge->update([
            'statut_validation' => StatutConge::REFUSE->value,
            'validateur_id' => $validateurId,
            'valide_le' => now(),
            'motif_refus' => $motif,
        ]);

        return $conge->refresh();
    }

    protected function imputerSolde(Conge $conge): void
    {
        $solde = SoldeConge::firstOrCreate(
            ['employe_id' => $conge->employe_id, 'type_conge' => 'annuel', 'annee' => $conge->date_debut->year],
            ['droit_total' => 30, 'jours_pris' => 0] // 30 jours : à ajuster selon convention
        );

        $solde->increment('jours_pris', $conge->nombre_jours);
    }
}
