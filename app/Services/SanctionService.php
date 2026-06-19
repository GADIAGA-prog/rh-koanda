<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\Sanction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SanctionService
{
    public function enregistrer(array $donnees, ?UploadedFile $document, int $auteurId): Sanction
    {
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;
        $donnees['prononce_par'] = $auteurId;
        if ($document) {
            $donnees['document'] = $document->store('sanctions', 'local');
        }

        return Sanction::create($donnees);
    }

    public function supprimer(Sanction $sanction): void
    {
        if ($sanction->document && Storage::disk('local')->exists($sanction->document)) {
            Storage::disk('local')->delete($sanction->document);
        }
        $sanction->delete();
    }
}
