<?php

namespace App\Services;

use App\Models\BulletinPaie;
use App\Models\Employe;
use App\Models\Enums\ModeCalcul;
use App\Models\Enums\StatutBulletin;
use App\Models\Enums\TypeRubrique;
use App\Models\RubriquePaie;
use App\Models\Scopes\FilialeScope;
use Illuminate\Support\Facades\DB;

/**
 * Génère les bulletins de paie à partir du salaire de base du contrat actif
 * et des rubriques paramétrables (gains, cotisations, retenues).
 *
 * IMPORTANT : aucun taux n'est codé en dur ici. Tout provient des rubriques
 * (table rubriques_paie), à faire valider par un comptable (réglementation
 * sociale et fiscale du Burkina Faso).
 */
class PaieService
{
    /** Génère (ou régénère) le bulletin d'un employé pour une période AAAA-MM. */
    public function genererBulletin(Employe $employe, string $periode): BulletinPaie
    {
        $contrat = $employe->contratActif();
        $salaireBase = (float) ($contrat?->salaire_base ?? 0);

        $rubriques = RubriquePaie::actives()
            ->pourFiliale($employe->filiale_id)
            ->orderBy('ordre')->get();

        $lignes = [];
        $ordre = 0;
        // Ligne de base (référence).
        $lignes[] = ['libelle' => 'Salaire de base', 'type' => TypeRubrique::GAIN->value, 'base' => $salaireBase, 'taux' => null, 'montant' => $salaireBase, 'ordre' => $ordre++];

        // 1) Gains (calculés sur le salaire de base).
        $totalGains = 0;
        foreach ($rubriques->where('type', TypeRubrique::GAIN) as $r) {
            $montant = $this->montantRubrique($r, $salaireBase);
            $totalGains += $montant;
            $lignes[] = $this->ligne($r, $salaireBase, $montant, $ordre++);
        }

        $brut = round($salaireBase + $totalGains, 2);

        // 2) Cotisations puis retenues (calculées sur le brut par défaut).
        $totalCotisations = 0;
        foreach ($rubriques->where('type', TypeRubrique::COTISATION) as $r) {
            $base = $r->base_calcul === 'salaire_base' ? $salaireBase : $brut;
            $montant = $this->montantRubrique($r, $base);
            $totalCotisations += $montant;
            $lignes[] = $this->ligne($r, $base, $montant, $ordre++);
        }

        $totalRetenues = 0;
        foreach ($rubriques->where('type', TypeRubrique::RETENUE) as $r) {
            $base = $r->base_calcul === 'salaire_base' ? $salaireBase : $brut;
            $montant = $this->montantRubrique($r, $base);
            $totalRetenues += $montant;
            $lignes[] = $this->ligne($r, $base, $montant, $ordre++);
        }

        $net = round($brut - $totalCotisations - $totalRetenues, 2);
        // Coût employeur ≈ brut + charges patronales (ici approximé par les cotisations) — à affiner.
        $coutEmployeur = round($brut + $totalCotisations, 2);

        return DB::transaction(function () use ($employe, $periode, $salaireBase, $totalGains, $brut, $totalCotisations, $totalRetenues, $net, $coutEmployeur, $lignes) {
            $bulletin = BulletinPaie::updateOrCreate(
                ['employe_id' => $employe->id, 'periode' => $periode],
                [
                    'filiale_id' => $employe->filiale_id,
                    'salaire_base' => $salaireBase,
                    'total_gains' => $totalGains,
                    'salaire_brut' => $brut,
                    'total_cotisations' => $totalCotisations,
                    'total_retenues' => $totalRetenues,
                    'net_a_payer' => $net,
                    'cout_employeur' => $coutEmployeur,
                    'statut' => StatutBulletin::BROUILLON->value,
                ]
            );

            $bulletin->lignes()->delete();
            foreach ($lignes as $l) {
                $bulletin->lignes()->create($l);
            }

            return $bulletin->load('lignes');
        });
    }

    /** Génération en masse pour une filiale et une période. Retourne le nombre de bulletins. */
    public function genererMasse(int $filialeId, string $periode): int
    {
        $employes = Employe::actifs()->where('filiale_id', $filialeId)->get();
        $n = 0;
        foreach ($employes as $employe) {
            if ($employe->contratActif()) {
                $this->genererBulletin($employe, $periode);
                $n++;
            }
        }

        return $n;
    }

    /** Masse salariale (somme des nets) par filiale pour une période. */
    public function masseSalariale(?array $filiales, string $periode)
    {
        return BulletinPaie::withoutGlobalScope(FilialeScope::class)
            ->when($filiales, fn ($q) => $q->whereIn('filiale_id', $filiales))
            ->where('periode', $periode)
            ->select('filiale_id', DB::raw('sum(net_a_payer) as masse'), DB::raw('sum(cout_employeur) as cout'))
            ->groupBy('filiale_id')
            ->get()
            ->keyBy('filiale_id');
    }

    protected function montantRubrique(RubriquePaie $r, float $base): float
    {
        return $r->mode_calcul === ModeCalcul::FIXE
            ? (float) ($r->montant ?? 0)
            : round($base * (float) ($r->taux ?? 0) / 100, 2);
    }

    protected function ligne(RubriquePaie $r, float $base, float $montant, int $ordre): array
    {
        return [
            'libelle' => $r->libelle,
            'type' => $r->type->value,
            'base' => $base,
            'taux' => $r->mode_calcul === ModeCalcul::POURCENTAGE ? $r->taux : null,
            'montant' => $montant,
            'ordre' => $ordre,
        ];
    }
}
