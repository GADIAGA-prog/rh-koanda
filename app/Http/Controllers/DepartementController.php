<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartementRequest;
use App\Models\Departement;
use App\Models\Filiale;
use App\Models\Site;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Departement::class, 'departement');
    }

    public function index(Request $request)
    {
        $departements = Departement::with(['filiale', 'site'])
            ->withCount(['postes', 'employes'])
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->orderBy('nom')
            ->paginate(20)->withQueryString();

        return view('organisation.departements.index', [
            'departements' => $departements,
            'filiales' => $this->filialesAccessibles(),
            'sites' => $this->sitesAccessibles(),
            'filtres' => $request->all(),
        ]);
    }

    public function store(DepartementRequest $request)
    {
        Departement::create($request->validated());

        return redirect()->route('departements.index')->with('succes', 'Département créé.');
    }

    public function edit(Departement $departement)
    {
        return view('organisation.departements.edit', [
            'departement' => $departement,
            'filiales' => $this->filialesAccessibles(),
            'sites' => $this->sitesAccessibles(),
        ]);
    }

    public function update(DepartementRequest $request, Departement $departement)
    {
        $departement->update($request->validated());

        return redirect()->route('departements.index')->with('succes', 'Département mis à jour.');
    }

    public function destroy(Departement $departement)
    {
        $departement->delete();

        return redirect()->route('departements.index')->with('succes', 'Département supprimé.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }

    protected function sitesAccessibles()
    {
        return Site::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('filiale_id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
