<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbsenceRequest;
use App\Models\Absence;
use App\Models\Employe;
use App\Models\Filiale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\AbsenceService;

class AbsenceController extends Controller
{
    public function __construct(protected AbsenceService $service)
    {
        $this->authorizeResource(Absence::class, 'absence');
    }

    public function index(Request $request)
    {
        $absences = Absence::with(['employe', 'filiale'])
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->filled('justifiee'), fn ($q) => $q->where('justifiee', $request->boolean('justifiee')))
            ->latest('date_debut')
            ->paginate(20)->withQueryString();

        return view('absences.index', [
            'absences' => $absences,
            'filiales' => $this->filialesAccessibles(),
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('absences.create', ['employes' => Employe::actifs()->orderBy('nom')->get()]);
    }

    public function store(StoreAbsenceRequest $request)
    {
        $donnees = $request->safe()->except('justificatif');
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;

        $this->service->enregistrer($donnees, $request->file('justificatif'));

        return redirect()->route('absences.index')->with('succes', 'Absence enregistrée.');
    }

    public function edit(Absence $absence)
    {
        return view('absences.edit', [
            'absence' => $absence,
            'employes' => Employe::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function update(StoreAbsenceRequest $request, Absence $absence)
    {
        $this->service->modifier($absence, $request->safe()->except(['justificatif', 'employe_id']), $request->file('justificatif'));

        return redirect()->route('absences.index')->with('succes', 'Absence mise à jour.');
    }

    public function destroy(Absence $absence)
    {
        if ($absence->justificatif) {
            Storage::disk('local')->delete($absence->justificatif);
        }
        $absence->delete();

        return redirect()->route('absences.index')->with('succes', 'Absence supprimée.');
    }

    public function justificatif(Absence $absence)
    {
        $this->authorize('view', $absence);
        abort_unless($absence->justificatif && Storage::disk('local')->exists($absence->justificatif), 404);

        return Storage::disk('local')->download($absence->justificatif);
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
