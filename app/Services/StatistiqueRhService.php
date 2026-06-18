<?php

namespace App\Services;

use App\Models\Conge;
use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Enums\StatutConge;
use App\Models\Enums\StatutContrat;
use App\Models\Enums\StatutEmploye;
use App\Models\Filiale;
use App\Models\Scopes\FilialeScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Produit les indicateurs RH consolidés (Groupe) et par filiale.
 *
 * Pour les rôles Groupe, on lit sans le scope filiale afin d'agréger
 * l'ensemble. Pour un RH Filiale, $filialesAutorisees restreint le périmètre.
 */
class StatistiqueRhService
{
    public function __construct(
        protected ?array $filialesAutorisees = null // null = tout le groupe
    ) {}

    protected function employes()
    {
        $q = Employe::withoutGlobalScope(FilialeScope::class);
        return $this->filialesAutorisees
            ? $q->whereIn('filiale_id', $this->filialesAutorisees)
            : $q;
    }

    protected function contrats()
    {
        $q = Contrat::withoutGlobalScope(FilialeScope::class);
        return $this->filialesAutorisees
            ? $q->whereIn('filiale_id', $this->filialesAutorisees)
            : $q;
    }

    /** Indicateurs synthétiques pour les cartes KPI. */
    public function indicateursCles(): array
    {
        $effectif = (clone $this->employes())->where('statut', StatutEmploye::ACTIF->value)->count();
        $contratsActifs = (clone $this->contrats())->where('statut', StatutContrat::ACTIF->value)->count();
        $expirantBientot = (clone $this->contrats())
            ->expirantAvant(Carbon::today()->addDays(30))
            ->whereDate('date_fin', '>=', today())
            ->count();
        $congesEnAttente = Conge::withoutGlobalScope(FilialeScope::class)
            ->when($this->filialesAutorisees, fn ($q, $v) => $q->whereIn('filiale_id', $v))
            ->where('statut_validation', StatutConge::EN_ATTENTE->value)
            ->count();

        return [
            'effectif_total' => $effectif,
            'contrats_actifs' => $contratsActifs,
            'contrats_expirant' => $expirantBientot,
            'conges_en_attente' => $congesEnAttente,
        ];
    }

    /** Répartition H/F. */
    public function repartitionSexe(): array
    {
        $r = (clone $this->employes())
            ->where('statut', StatutEmploye::ACTIF->value)
            ->select('sexe', DB::raw('count(*) as total'))
            ->groupBy('sexe')->pluck('total', 'sexe')->all();

        return ['hommes' => $r['M'] ?? 0, 'femmes' => $r['F'] ?? 0];
    }

    /** Effectif par filiale (pour graphique et tableau consolidé). */
    public function effectifParFiliale(): \Illuminate\Support\Collection
    {
        $effectifs = (clone $this->employes())
            ->where('statut', StatutEmploye::ACTIF->value)
            ->select('filiale_id', DB::raw('count(*) as total'))
            ->groupBy('filiale_id')->pluck('total', 'filiale_id');

        return Filiale::query()
            ->when($this->filialesAutorisees, fn ($q, $v) => $q->whereIn('id', $v))
            ->orderBy('nom')->get()
            ->map(fn ($f) => [
                'filiale' => $f->nom,
                'code' => $f->code,
                'effectif' => $effectifs[$f->id] ?? 0,
            ]);
    }

    /** Contrats actifs / expirés / expirant pour donut. */
    public function repartitionContrats(): array
    {
        $base = $this->contrats();
        return [
            'actifs' => (clone $base)->where('statut', StatutContrat::ACTIF->value)->count(),
            'expires' => (clone $base)->where('statut', StatutContrat::EXPIRE->value)->count(),
            'expirant' => (clone $base)->expirantAvant(Carbon::today()->addDays(30))
                ->whereDate('date_fin', '>=', today())->count(),
        ];
    }
}
