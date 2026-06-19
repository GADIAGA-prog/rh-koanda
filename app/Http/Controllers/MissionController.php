<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMissionRequest;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Mission;
use App\Services\MissionService;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function __construct(protected MissionService $service)
    {
        $this->authorizeResource(Mission::class, 'mission');
    }

    public function index(Request $request)
    {
        $missions = Mission::with(['employe', 'filiale', 'validateur'])
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->when($request->employe_id, fn ($q, $v) => $q->where('employe_id', $v))
            ->latest('date_depart')
            ->paginate(20)->withQueryString();

        return view('missions.index', [
            'missions' => $missions,
            'filiales' => $this->filialesAccessibles(),
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('missions.create', ['employes' => Employe::actifs()->orderBy('nom')->get()]);
    }

    public function store(StoreMissionRequest $request)
    {
        $donnees = $request->validated();
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;

        $mission = $this->service->creer($donnees);

        return redirect()->route('missions.show', $mission)->with('succes', 'Ordre de mission créé.');
    }

    public function show(Mission $mission)
    {
        $mission->load(['employe', 'filiale', 'validateur']);

        return view('missions.show', compact('mission'));
    }

    public function edit(Mission $mission)
    {
        return view('missions.edit', [
            'mission' => $mission,
            'employes' => Employe::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function update(StoreMissionRequest $request, Mission $mission)
    {
        $this->service->modifier($mission, $request->validated());

        return redirect()->route('missions.show', $mission)->with('succes', 'Mission mise à jour.');
    }

    public function destroy(Mission $mission)
    {
        $mission->delete();

        return redirect()->route('missions.index')->with('succes', 'Mission supprimée.');
    }

    public function soumettre(Mission $mission)
    {
        $this->authorize('update', $mission);
        $this->service->soumettre($mission);

        return back()->with('succes', 'Mission soumise pour validation.');
    }

    public function valider(Mission $mission, Request $request)
    {
        $this->authorize('valider', $mission);
        $this->service->valider($mission, $request->user()->id);

        return back()->with('succes', 'Mission validée.');
    }

    public function refuser(Mission $mission, Request $request)
    {
        $this->authorize('valider', $mission);
        $request->validate(['motif_refus' => ['nullable', 'string', 'max:500']]);
        $this->service->refuser($mission, $request->user()->id, $request->motif_refus);

        return back()->with('succes', 'Mission refusée.');
    }

    public function cloturer(Mission $mission)
    {
        $this->authorize('valider', $mission);
        $this->service->cloturer($mission);

        return back()->with('succes', 'Mission clôturée.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
