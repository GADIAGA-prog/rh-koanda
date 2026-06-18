<?php

namespace App\Services;

use App\Models\Employe;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EmployeService
{
    /** Liste paginée filtrée (le scope filiale s'applique automatiquement). */
    public function lister(array $filtres = []): LengthAwarePaginator
    {
        return Employe::query()
            ->with(['filiale', 'departement', 'poste'])
            ->recherche($filtres['recherche'] ?? null)
            ->when($filtres['filiale_id'] ?? null, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($filtres['departement_id'] ?? null, fn ($q, $v) => $q->where('departement_id', $v))
            ->when($filtres['poste_id'] ?? null, fn ($q, $v) => $q->where('poste_id', $v))
            ->when($filtres['statut'] ?? null, fn ($q, $v) => $q->where('statut', $v))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();
    }

    public function creer(array $donnees): Employe
    {
        return DB::transaction(function () use ($donnees) {
            $donnees['matricule'] ??= $this->genererMatricule($donnees['filiale_id']);
            return Employe::create($donnees);
        });
    }

    public function modifier(Employe $employe, array $donnees): Employe
    {
        $employe->update($donnees);
        return $employe->refresh();
    }

    public function supprimer(Employe $employe): void
    {
        $employe->delete(); // soft delete
    }

    /** Matricule unique par filiale : ex KG-GCMI-0007. */
    public function genererMatricule(int $filialeId): string
    {
        $filiale = \App\Models\Filiale::withTrashed()->find($filialeId);
        $prefixe = 'KG-' . strtoupper($filiale?->code ?? 'XXX');
        $compteur = Employe::withoutGlobalScope(\App\Models\Scopes\FilialeScope::class)
            ->where('filiale_id', $filialeId)->withTrashed()->count() + 1;

        return sprintf('%s-%04d', $prefixe, $compteur);
    }
}
