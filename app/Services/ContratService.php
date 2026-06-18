<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Enums\StatutContrat;
use Illuminate\Support\Carbon;

class ContratService
{
    public function creer(array $donnees): Contrat
    {
        $donnees['statut'] ??= StatutContrat::ACTIF->value;
        return Contrat::create($donnees);
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
}
