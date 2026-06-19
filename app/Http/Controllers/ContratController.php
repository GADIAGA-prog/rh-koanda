<?php

namespace App\Http\Controllers;

use App\Http\Requests\RenouvelerContratRequest;
use App\Http\Requests\StoreContratRequest;
use App\Http\Requests\UpdateContratRequest;
use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Filiale;
use App\Services\ContratService;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    public function __construct(protected ContratService $service)
    {
        $this->authorizeResource(Contrat::class, 'contrat');
    }

    public function index(Request $request)
    {
        $contrats = $this->service->lister($request->only([
            'recherche', 'filiale_id', 'type_contrat', 'statut', 'employe_id',
        ]));

        return view('contrats.index', [
            'contrats' => $contrats,
            'filiales' => Filiale::orderBy('nom')->get(),
            'filtres'  => $request->all(),
        ]);
    }

    public function create(Request $request)
    {
        return view('contrats.create', [
            'employes'     => Employe::actifs()->orderBy('nom')->get(),
            'employeChoisi' => $request->integer('employe_id') ?: null,
        ]);
    }

    public function store(StoreContratRequest $request)
    {
        $donnees = $request->validated();
        // filiale_id dérivée de l'employé : indispensable pour les utilisateurs Groupe
        // (filiale_id null) que le hook BelongsToFiliale ne peut pas renseigner.
        $donnees['filiale_id'] = Employe::findOrFail($donnees['employe_id'])->filiale_id;

        $contrat = $this->service->creer($donnees);

        return redirect()->route('contrats.show', $contrat)
            ->with('succes', 'Contrat enregistré.');
    }

    public function show(Contrat $contrat)
    {
        $contrat->load(['employe', 'filiale']);

        return view('contrats.show', compact('contrat'));
    }

    public function edit(Contrat $contrat)
    {
        return view('contrats.edit', [
            'contrat'  => $contrat,
            'employes' => Employe::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function update(UpdateContratRequest $request, Contrat $contrat)
    {
        $this->service->modifier($contrat, $request->validated());

        return redirect()->route('contrats.show', $contrat)
            ->with('succes', 'Contrat mis à jour.');
    }

    public function renouveler(RenouvelerContratRequest $request, Contrat $contrat)
    {
        $nouveau = $this->service->renouveler($contrat, $request->validated());

        return redirect()->route('contrats.show', $nouveau)
            ->with('succes', 'Contrat renouvelé : un nouveau contrat a été créé.');
    }

    public function destroy(Contrat $contrat)
    {
        $contrat->delete();

        return redirect()->route('contrats.index')->with('succes', 'Contrat archivé.');
    }
}
