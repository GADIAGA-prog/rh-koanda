<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationRequest;
use App\Models\Employe;
use App\Models\EvaluationPerformance;
use App\Models\Filiale;
use App\Services\PerformanceService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(protected PerformanceService $service)
    {
        $this->authorizeResource(EvaluationPerformance::class, 'evaluation');
    }

    public function index(Request $request)
    {
        $evaluations = EvaluationPerformance::with(['employe', 'evaluateur', 'filiale'])
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->periode, fn ($q, $v) => $q->where('periode', $v))
            ->latest('id')
            ->paginate(20)->withQueryString();

        return view('performance.index', [
            'evaluations' => $evaluations,
            'filiales' => $this->filialesAccessibles(),
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('performance.create', ['employes' => $this->employesEvaluables()]);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $evaluation = $this->service->creer($request->validated(), $request->user()->id);

        return redirect()->route('evaluations.show', $evaluation)->with('succes', 'Évaluation enregistrée.');
    }

    public function show(EvaluationPerformance $evaluation)
    {
        $evaluation->load(['employe', 'evaluateur', 'filiale']);

        return view('performance.show', compact('evaluation'));
    }

    public function edit(EvaluationPerformance $evaluation)
    {
        return view('performance.edit', [
            'evaluation' => $evaluation,
            'employes' => $this->employesEvaluables(),
        ]);
    }

    public function update(StoreEvaluationRequest $request, EvaluationPerformance $evaluation)
    {
        $this->service->modifier($evaluation, $request->validated());

        return redirect()->route('evaluations.show', $evaluation)->with('succes', 'Évaluation mise à jour.');
    }

    public function destroy(EvaluationPerformance $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluations.index')->with('succes', 'Évaluation supprimée.');
    }

    /** Périmètre d'évaluation : subordonnés pour un manager, filiales pour un RH. */
    protected function employesEvaluables()
    {
        $user = auth()->user();

        if ($user->can('employe.update')) {
            return Employe::actifs()->orderBy('nom')->get();
        }

        return Employe::actifs()
            ->when($user->employe, fn ($q) => $q->where('manager_id', $user->employe->id), fn ($q) => $q->whereRaw('1 = 0'))
            ->orderBy('nom')->get();
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
