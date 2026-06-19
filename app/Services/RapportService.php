<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\Enums\StatutEmploye;
use App\Models\Filiale;
use App\Models\Scopes\FilialeScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Agrège les statistiques RH par filiale en réutilisant les services métier.
 */
class RapportService
{
    public function __construct(
        protected PresenceService $presences,
        protected PaieService $paie,
        protected ContratService $contrats,
    ) {}

    /** Statistiques consolidées par filiale pour le périmètre fourni (null = groupe). */
    public function statistiquesParFiliale(?array $perimetre): \Illuminate\Support\Collection
    {
        $stats = new StatistiqueRhService($perimetre);
        $effectifs = $stats->effectifParFiliale();

        $periode = Carbon::now()->format('Y-m');
        $taux = $this->presences->tauxParFiliale($perimetre, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $masse = $this->paie->masseSalariale($perimetre, $periode);
        $renouveler = $this->contrats->aRenouvelerPourPerimetre($perimetre)->groupBy('filiale_id');
        $departs = $this->departsParFiliale($perimetre);

        return $effectifs->map(function ($f) use ($taux, $masse, $renouveler, $departs) {
            $effectif = $f['effectif'];
            $depart = $departs[$f['id']] ?? 0;
            $turnover = ($effectif + $depart) > 0 ? round($depart / ($effectif + $depart) * 100, 1) : 0;

            return [
                'filiale' => $f['filiale'],
                'code' => $f['code'],
                'effectif' => $effectif,
                'taux_absenteisme' => $taux[$f['id']]['taux_absenteisme'] ?? 0,
                'taux_retard' => $taux[$f['id']]['taux_retard'] ?? 0,
                'turnover' => $turnover,
                'masse_salariale' => isset($masse[$f['id']]) ? (float) $masse[$f['id']]->masse : 0,
                'contrats_a_renouveler' => isset($renouveler[$f['id']]) ? $renouveler[$f['id']]->count() : 0,
            ];
        });
    }

    protected function departsParFiliale(?array $perimetre): array
    {
        return Employe::withoutGlobalScope(FilialeScope::class)
            ->when($perimetre, fn ($q) => $q->whereIn('filiale_id', $perimetre))
            ->where('statut', StatutEmploye::DEPART->value)
            ->select('filiale_id', DB::raw('count(*) as total'))
            ->groupBy('filiale_id')
            ->pluck('total', 'filiale_id')->all();
    }

    /** Totaux groupe (pour l'en-tête du rapport). */
    public function totaux(\Illuminate\Support\Collection $stats): array
    {
        return [
            'effectif' => $stats->sum('effectif'),
            'masse_salariale' => $stats->sum('masse_salariale'),
            'contrats_a_renouveler' => $stats->sum('contrats_a_renouveler'),
        ];
    }
}
