<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Models\Departement;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Poste;
use App\Services\EmployeService;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    public function __construct(protected EmployeService $service)
    {
        $this->authorizeResource(Employe::class, 'employe');
    }

    public function index(Request $request)
    {
        $employes = $this->service->lister($request->only([
            'recherche', 'filiale_id', 'departement_id', 'poste_id', 'statut',
        ]));

        return view('employes.index', [
            'employes' => $employes,
            'filiales' => Filiale::orderBy('nom')->get(),
            'filtres' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('employes.create', $this->referentiels());
    }

    public function store(StoreEmployeRequest $request)
    {
        $employe = $this->service->creer($request->validated());

        return redirect()->route('employes.show', $employe)
            ->with('succes', "Employé {$employe->nom_complet} créé (matricule {$employe->matricule}).");
    }

    public function show(Employe $employe)
    {
        $employe->load(['filiale', 'departement', 'poste', 'manager', 'contrats', 'conges', 'documents']);

        return view('employes.show', compact('employe'));
    }

    public function edit(Employe $employe)
    {
        return view('employes.edit', array_merge(['employe' => $employe], $this->referentiels()));
    }

    public function update(UpdateEmployeRequest $request, Employe $employe)
    {
        $this->service->modifier($employe, $request->validated());

        return redirect()->route('employes.show', $employe)->with('succes', 'Dossier mis à jour.');
    }

    public function destroy(Employe $employe)
    {
        $this->service->supprimer($employe);

        return redirect()->route('employes.index')->with('succes', 'Employé archivé.');
    }

    protected function referentiels(): array
    {
        return [
            'filiales' => Filiale::orderBy('nom')->get(),
            'departements' => Departement::orderBy('nom')->get(),
            'postes' => Poste::orderBy('intitule')->get(),
            'managers' => Employe::actifs()->orderBy('nom')->get(),
        ];
    }
}
