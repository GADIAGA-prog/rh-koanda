<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCongeRequest;
use App\Models\Conge;
use App\Models\Employe;
use App\Services\CongeService;
use Illuminate\Http\Request;

class CongeController extends Controller
{
    public function __construct(protected CongeService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Conge::class);

        $conges = Conge::with(['employe', 'validateur'])
            ->when($request->statut, fn ($q, $v) => $q->where('statut_validation', $v))
            ->latest('date_debut')
            ->paginate(20)->withQueryString();

        return view('conges.index', [
            'conges' => $conges,
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Conge::class);

        return view('conges.create', [
            'employes' => Employe::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function store(StoreCongeRequest $request)
    {
        $this->service->demander($request->validated());

        return redirect()->route('conges.index')->with('succes', 'Demande de congé enregistrée.');
    }

    public function valider(Conge $conge, Request $request)
    {
        $this->authorize('valider', $conge);
        $this->service->valider($conge, $request->user()->id);

        return back()->with('succes', 'Congé validé.');
    }

    public function refuser(Conge $conge, Request $request)
    {
        $this->authorize('valider', $conge);
        $request->validate(['motif_refus' => ['nullable', 'string', 'max:500']]);
        $this->service->refuser($conge, $request->user()->id, $request->motif_refus);

        return back()->with('succes', 'Congé refusé.');
    }
}
