<?php

namespace App\Services;

use App\Models\Absence;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AbsenceService
{
    public function enregistrer(array $donnees, ?UploadedFile $justificatif = null): Absence
    {
        if ($justificatif) {
            $donnees['justificatif'] = $justificatif->store('justificatifs', 'local');
        }

        return Absence::create($donnees);
    }

    public function modifier(Absence $absence, array $donnees, ?UploadedFile $justificatif = null): Absence
    {
        if ($justificatif) {
            if ($absence->justificatif) {
                Storage::disk('local')->delete($absence->justificatif);
            }
            $donnees['justificatif'] = $justificatif->store('justificatifs', 'local');
        }

        $absence->update($donnees);

        return $absence->refresh();
    }

    /** Nombre de jours calendaires couverts par l'absence. */
    public function nombreJours(Absence $absence): int
    {
        return $absence->date_debut->diffInDays($absence->date_fin) + 1;
    }
}
