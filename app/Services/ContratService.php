<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Enums\StatutContrat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContratService
{
    public function creer(array $donnees): Contrat
    {
        $donnees['statut'] ??= StatutContrat::ACTIF->value;
        $donnees['reference'] = $donnees['reference'] ?? null;

        return Contrat::create($donnees);
    }

    public function modifier(Contrat $contrat, array $donnees): Contrat
    {
        $contrat->update($donnees);

        return $contrat->refresh();
    }

    /**
     * Renouvelle un contrat : clôture le contrat courant (statut « expiré »)
     * et crée un nouveau contrat à la suite, héritant des données du précédent.
     */
    public function renouveler(Contrat $contrat, array $donnees): Contrat
    {
        return DB::transaction(function () use ($contrat, $donnees) {
            $contrat->update(['statut' => StatutContrat::EXPIRE->value]);

            return Contrat::create([
                'filiale_id'    => $contrat->filiale_id,
                'employe_id'    => $contrat->employe_id,
                'reference'     => $donnees['reference'] ?? null,
                'type_contrat'  => $donnees['type_contrat'] ?? $contrat->type_contrat->value,
                'date_debut'    => $donnees['date_debut'],
                'date_fin'      => $donnees['date_fin'] ?? null,
                'salaire_base'  => $donnees['salaire_base'] ?? $contrat->salaire_base,
                'devise'        => $donnees['devise'] ?? $contrat->devise,
                'statut'        => StatutContrat::ACTIF->value,
                'observations'  => $donnees['observations'] ?? null,
            ]);
        });
    }

    /** Liste paginée et filtrable (le scope filiale s'applique automatiquement). */
    public function lister(array $filtres)
    {
        return Contrat::with(['employe', 'filiale'])
            ->recherche($filtres['recherche'] ?? null)
            ->when($filtres['filiale_id'] ?? null, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($filtres['type_contrat'] ?? null, fn ($q, $v) => $q->where('type_contrat', $v))
            ->when($filtres['statut'] ?? null, fn ($q, $v) => $q->where('statut', $v))
            ->when($filtres['employe_id'] ?? null, fn ($q, $v) => $q->where('employe_id', $v))
            ->latest('date_debut')
            ->paginate(20)
            ->withQueryString();
    }

    /** Marque comme expirés les contrats CDD dont la date de fin est dépassée. */
    public function expirerEcheances(): int
    {
        return Contrat::sansFiltreFiliale()
            ->where('statut', StatutContrat::ACTIF->value)
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<', today())
            ->update(['statut' => StatutContrat::EXPIRE->value]);
    }

    /** Contrats actifs arrivant à échéance dans N jours (tout le groupe). */
    public function expirantDansJours(int $jours = 30)
    {
        return Contrat::sansFiltreFiliale()
            ->with(['employe', 'filiale'])
            ->expirantAvant(Carbon::today()->addDays($jours))
            ->whereDate('date_fin', '>=', today())
            ->orderBy('date_fin')
            ->get();
    }

    /**
     * Contrats à renouveler dans le périmètre de filiales fourni
     * (null = tout le groupe). Alimente l'encart du tableau de bord.
     */
    public function aRenouvelerPourPerimetre(?array $filiales = null, int $jours = 30)
    {
        return Contrat::sansFiltreFiliale()
            ->with(['employe', 'filiale'])
            ->when($filiales, fn ($q, $v) => $q->whereIn('filiale_id', $v))
            ->aRenouveler($jours)
            ->orderBy('date_fin')
            ->get();
    }
}
