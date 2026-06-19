<?php

namespace App\Http\Controllers;

use App\Http\Requests\RubriquePaieRequest;
use App\Models\Filiale;
use App\Models\RubriquePaie;

class RubriquePaieController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(RubriquePaie::class, 'rubrique');
    }

    public function index()
    {
        $user = auth()->user();

        $rubriques = RubriquePaie::with('filiale')
            ->when(! $user->peutVoirToutLeGroupe(), fn ($q) => $q->where(
                fn ($w) => $w->whereNull('filiale_id')->orWhereIn('filiale_id', $user->filialesAccessibles())
            ))
            ->orderBy('ordre')->orderBy('libelle')
            ->paginate(30);

        return view('paie.rubriques.index', [
            'rubriques' => $rubriques,
            'filiales' => $this->filialesAccessibles(),
        ]);
    }

    public function store(RubriquePaieRequest $request)
    {
        RubriquePaie::create($request->validated());

        return redirect()->route('rubriques.index')->with('succes', 'Rubrique créée.');
    }

    public function edit(RubriquePaie $rubrique)
    {
        return view('paie.rubriques.edit', [
            'rubrique' => $rubrique,
            'filiales' => $this->filialesAccessibles(),
        ]);
    }

    public function update(RubriquePaieRequest $request, RubriquePaie $rubrique)
    {
        $rubrique->update($request->validated());

        return redirect()->route('rubriques.index')->with('succes', 'Rubrique mise à jour.');
    }

    public function destroy(RubriquePaie $rubrique)
    {
        $rubrique->delete();

        return redirect()->route('rubriques.index')->with('succes', 'Rubrique supprimée.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
