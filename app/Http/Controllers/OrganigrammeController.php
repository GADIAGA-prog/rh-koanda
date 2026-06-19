<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Filiale;
use Illuminate\Http\Request;

class OrganigrammeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('organisation.view'), 403);

        $filiales = Filiale::query()
            ->when(! $request->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', $request->user()->filialesAccessibles()))
            ->orderBy('nom')->get();

        $filialeId = $request->filiale_id ?: $filiales->first()?->id;

        // Le FilialeScope garantit déjà l'isolation ; on restreint à la filiale choisie.
        $employes = Employe::with('poste')
            ->when($filialeId, fn ($q) => $q->where('filiale_id', $filialeId))
            ->orderBy('nom')
            ->get();

        $ids = $employes->pluck('id')->all();
        // Racines = sans manager, ou manager hors du périmètre chargé.
        $racines = $employes->filter(fn ($e) => ! $e->manager_id || ! in_array($e->manager_id, $ids))->values();
        $enfantsParManager = $employes->groupBy('manager_id');

        return view('organisation.organigramme', [
            'filiales' => $filiales,
            'filialeId' => $filialeId,
            'racines' => $racines,
            'enfantsParManager' => $enfantsParManager,
            'total' => $employes->count(),
        ]);
    }
}
