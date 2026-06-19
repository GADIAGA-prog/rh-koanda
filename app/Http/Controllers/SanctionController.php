<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSanctionRequest;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Sanction;
use App\Services\SanctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SanctionController extends Controller
{
    public function __construct(protected SanctionService $service)
    {
        $this->authorizeResource(Sanction::class, 'sanction');
    }

    public function index(Request $request)
    {
        $sanctions = Sanction::with(['employe', 'filiale', 'auteur'])
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->latest('date_sanction')
            ->paginate(20)->withQueryString();

        return view('sanctions.index', [
            'sanctions' => $sanctions,
            'filiales' => $this->filialesAccessibles(),
            'filtres' => $request->all(),
        ]);
    }

    public function create(Request $request)
    {
        return view('sanctions.create', [
            'employes' => Employe::actifs()->orderBy('nom')->get(),
            'employeChoisi' => $request->integer('employe_id') ?: null,
        ]);
    }

    public function store(StoreSanctionRequest $request)
    {
        $this->service->enregistrer($request->safe()->except('document'), $request->file('document'), $request->user()->id);

        return redirect()->route('sanctions.index')->with('succes', 'Sanction enregistrée.');
    }

    public function download(Sanction $sanction)
    {
        $this->authorize('view', $sanction);
        abort_unless($sanction->document && Storage::disk('local')->exists($sanction->document), 404);

        return Storage::disk('local')->download($sanction->document);
    }

    public function destroy(Sanction $sanction)
    {
        $this->service->supprimer($sanction);

        return back()->with('succes', 'Sanction supprimée.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
