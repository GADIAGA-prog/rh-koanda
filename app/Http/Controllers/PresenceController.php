<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresenceRequest;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Presence;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresenceController extends Controller
{
    public function __construct(protected PresenceService $service)
    {
        $this->authorizeResource(Presence::class, 'presence');
    }

    public function index(Request $request)
    {
        $mois = $request->mois ? Carbon::parse($request->mois . '-01') : Carbon::now()->startOfMonth();
        $debut = $mois->copy()->startOfMonth();
        $fin = $mois->copy()->endOfMonth();

        $filiales = $this->filialesAccessibles();
        $filialeId = $request->filiale_id ?: $filiales->first()?->id;

        $employes = Employe::with('poste')
            ->when($filialeId, fn ($q) => $q->where('filiale_id', $filialeId))
            ->orderBy('nom')->get();

        // presences[employe_id][jour] = Presence
        $presences = Presence::whereBetween('date_presence', [$debut->toDateString(), $fin->toDateString()])
            ->when($filialeId, fn ($q) => $q->where('filiale_id', $filialeId))
            ->get()
            ->groupBy('employe_id')
            ->map(fn ($lot) => $lot->keyBy(fn ($p) => $p->date_presence->day));

        return view('presences.index', [
            'filiales' => $filiales,
            'filialeId' => $filialeId,
            'mois' => $mois,
            'debut' => $debut,
            'fin' => $fin,
            'employes' => $employes,
            'presences' => $presences,
        ]);
    }

    public function store(StorePresenceRequest $request)
    {
        $donnees = $request->validated();
        // filiale_id dérivée de l'employé (garantit l'isolation : employé hors périmètre = introuvable).
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;

        $this->service->pointer($donnees);

        return back()->with('succes', 'Pointage enregistré.');
    }

    public function update(StorePresenceRequest $request, Presence $presence)
    {
        $presence->update($request->safe()->only([
            'heure_arrivee', 'heure_depart', 'statut', 'commentaire',
        ]));

        return back()->with('succes', 'Pointage mis à jour.');
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();

        return back()->with('succes', 'Pointage supprimé.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
