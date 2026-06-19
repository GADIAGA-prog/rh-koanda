<?php

namespace App\Http\Controllers;

use App\Http\Requests\PosteRequest;
use App\Models\Departement;
use App\Models\Filiale;
use App\Models\Poste;
use Illuminate\Http\Request;

class PosteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Poste::class, 'poste');
    }

    public function index(Request $request)
    {
        $postes = Poste::with(['filiale', 'departement'])
            ->withCount('employes')
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->departement_id, fn ($q, $v) => $q->where('departement_id', $v))
            ->orderBy('intitule')
            ->paginate(20)->withQueryString();

        return view('organisation.postes.index', [
            'postes' => $postes,
            'filiales' => $this->filialesAccessibles(),
            'departements' => Departement::orderBy('nom')->get(),
            'filtres' => $request->all(),
        ]);
    }

    public function store(PosteRequest $request)
    {
        Poste::create($request->validated());

        return redirect()->route('postes.index')->with('succes', 'Poste créé.');
    }

    public function edit(Poste $poste)
    {
        return view('organisation.postes.edit', [
            'poste' => $poste,
            'filiales' => $this->filialesAccessibles(),
            'departements' => Departement::orderBy('nom')->get(),
        ]);
    }

    public function update(PosteRequest $request, Poste $poste)
    {
        $poste->update($request->validated());

        return redirect()->route('postes.index')->with('succes', 'Poste mis à jour.');
    }

    public function destroy(Poste $poste)
    {
        $poste->delete();

        return redirect()->route('postes.index')->with('succes', 'Poste supprimé.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
