<?php

namespace App\Services;

use App\Models\DocumentRh;
use App\Models\Employe;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function enregistrer(array $donnees, UploadedFile $fichier, int $auteurId): DocumentRh
    {
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;
        $donnees['fichier'] = $fichier->store('documents-rh', 'local');
        $donnees['ajoute_par'] = $auteurId;

        return DocumentRh::create($donnees);
    }

    public function supprimer(DocumentRh $document): void
    {
        if ($document->fichier && Storage::disk('local')->exists($document->fichier)) {
            Storage::disk('local')->delete($document->fichier);
        }
        $document->delete();
    }
}
