<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormationRequest;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Formation;
use App\Services\FormationService;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct(protected FormationService $service)
    {
        $this->authorizeResource(Formation::class, 'formation');
    }

    public function index(Request $request)
    {
        $formations = Formation::withCount('participants')
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->latest('date_debut')
            ->paginate(20)->withQueryString();

        $perimetre = auth()->user()->peutVoirToutLeGroupe() ? null : auth()->user()->filialesAccessibles();

        return view('formations.index', [
            'formations' => $formations,
            'filiales' => $this->filialesAccessibles(),
            'couts' => $this->service->coutsParFiliale($perimetre),
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('formations.create', ['filiales' => $this->filialesAccessibles()]);
    }

    public function store(FormationRequest $request)
    {
        $formation = $this->service->creer($request->validated());

        return redirect()->route('formations.show', $formation)->with('succes', 'Formation créée.');
    }

    public function show(Formation $formation)
    {
        $formation->load(['filiale', 'participants']);

        return view('formations.show', [
            'formation' => $formation,
            'employes' => Employe::actifs()->where('filiale_id', $formation->filiale_id)->orderBy('nom')->get(),
        ]);
    }

    public function edit(Formation $formation)
    {
        return view('formations.edit', [
            'formation' => $formation,
            'filiales' => $this->filialesAccessibles(),
        ]);
    }

    public function update(FormationRequest $request, Formation $formation)
    {
        $this->service->modifier($formation, $request->validated());

        return redirect()->route('formations.show', $formation)->with('succes', 'Formation mise à jour.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();

        return redirect()->route('formations.index')->with('succes', 'Formation supprimée.');
    }

    // --- Participants ---------------------------------------------------

    public function ajouterParticipant(Formation $formation, Request $request)
    {
        $this->authorize('update', $formation);
        $data = $request->validate([
            'employe_id' => ['required', 'exists:employes,id'],
            'present' => ['nullable', 'boolean'],
            'resultat' => ['nullable', 'string', 'max:100'],
        ]);
        $this->service->ajouterParticipant($formation, $data['employe_id'], [
            'present' => $request->boolean('present', true),
            'resultat' => $data['resultat'] ?? null,
        ]);

        return back()->with('succes', 'Participant ajouté.');
    }

    public function majParticipant(Formation $formation, Employe $employe, Request $request)
    {
        $this->authorize('update', $formation);
        $request->validate(['resultat' => ['nullable', 'string', 'max:100']]);
        $this->service->majParticipant($formation, $employe->id, [
            'present' => $request->boolean('present'),
            'resultat' => $request->resultat,
        ]);

        return back()->with('succes', 'Participant mis à jour.');
    }

    public function retirerParticipant(Formation $formation, Employe $employe)
    {
        $this->authorize('update', $formation);
        $this->service->retirerParticipant($formation, $employe->id);

        return back()->with('succes', 'Participant retiré.');
    }

    public function acquerirCompetence(Formation $formation, Request $request)
    {
        $this->authorize('update', $formation);
        $data = $request->validate([
            'employe_id' => ['required', 'exists:employes,id'],
            'libelle' => ['required', 'string', 'max:255'],
            'domaine' => ['nullable', 'string', 'max:255'],
            'niveau' => ['required', 'integer', 'min:1', 'max:5'],
        ]);
        $this->service->acquerirCompetence($data['employe_id'], $data['libelle'], $data['niveau'], $data['domaine'] ?? null);

        return back()->with('succes', 'Compétence acquise enregistrée.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
