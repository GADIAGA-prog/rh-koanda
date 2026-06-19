<?php

namespace App\Services;

use App\Models\Enums\StatutPresence;
use App\Models\Presence;
use App\Models\Scopes\FilialeScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PresenceService
{
    /** Enregistre (ou met à jour) le pointage d'un employé pour une date. */
    public function pointer(array $donnees): Presence
    {
        return Presence::updateOrCreate(
            ['employe_id' => $donnees['employe_id'], 'date_presence' => $donnees['date_presence']],
            [
                // filiale_id requis pour les utilisateurs Groupe (filiale_id null) :
                // updateOrCreate n'ajoute que les valeurs listées ici lors de la création.
                'filiale_id' => $donnees['filiale_id'] ?? null,
                'heure_arrivee' => $donnees['heure_arrivee'] ?? null,
                'heure_depart' => $donnees['heure_depart'] ?? null,
                'statut' => $donnees['statut'] ?? StatutPresence::PRESENT->value,
                'commentaire' => $donnees['commentaire'] ?? null,
            ]
        );
    }

    /**
     * Taux d'absentéisme et de retard par filiale sur un mois donné.
     * @return \Illuminate\Support\Collection<int,array>
     */
    public function tauxParFiliale(?array $filiales, Carbon $debut, Carbon $fin)
    {
        $lignes = Presence::withoutGlobalScope(FilialeScope::class)
            ->when($filiales, fn ($q) => $q->whereIn('filiale_id', $filiales))
            ->whereBetween('date_presence', [$debut->toDateString(), $fin->toDateString()])
            ->select('filiale_id',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when statut = 'absent' then 1 else 0 end) as absents"),
                DB::raw("sum(case when statut = 'retard' then 1 else 0 end) as retards"))
            ->groupBy('filiale_id')
            ->get()
            ->keyBy('filiale_id');

        return $lignes->map(fn ($l) => [
            'filiale_id' => $l->filiale_id,
            'total' => (int) $l->total,
            'taux_absenteisme' => $l->total ? round($l->absents / $l->total * 100, 1) : 0,
            'taux_retard' => $l->total ? round($l->retards / $l->total * 100, 1) : 0,
        ]);
    }
}
