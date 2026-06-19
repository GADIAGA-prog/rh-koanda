<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Enums\StatutBulletin;
use App\Models\Filiale;
use App\Services\PaieService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaieController extends Controller
{
    public function __construct(protected PaieService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', BulletinPaie::class);

        $periode = $request->periode ?: now()->format('Y-m');

        $bulletins = BulletinPaie::with(['employe', 'filiale'])
            ->where('periode', $periode)
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->latest('id')
            ->paginate(25)->withQueryString();

        return view('paie.index', [
            'bulletins' => $bulletins,
            'filiales' => $this->filialesAccessibles(),
            'periode' => $periode,
            'filtres' => $request->all(),
        ]);
    }

    public function generer(Request $request)
    {
        $this->authorize('create', BulletinPaie::class);

        $valide = $request->validate([
            'filiale_id' => ['required', 'exists:filiales,id', Rule::in($this->filialesAccessibles()->pluck('id')->all())],
            'periode' => ['required', 'date_format:Y-m'],
        ]);

        $nombre = $this->service->genererMasse($valide['filiale_id'], $valide['periode']);

        return redirect()->route('paie.index', ['filiale_id' => $valide['filiale_id'], 'periode' => $valide['periode']])
            ->with('succes', "{$nombre} bulletin(s) généré(s) pour {$valide['periode']}.");
    }

    public function show(BulletinPaie $bulletin)
    {
        $this->authorize('view', $bulletin);
        $bulletin->load(['employe', 'filiale', 'lignes']);

        return view('paie.show', compact('bulletin'));
    }

    public function imprimer(BulletinPaie $bulletin)
    {
        $this->authorize('view', $bulletin);
        $bulletin->load(['employe.poste', 'filiale', 'lignes']);

        return view('paie.bulletin-imprimable', compact('bulletin'));
    }

    public function changerStatut(BulletinPaie $bulletin, Request $request)
    {
        $this->authorize('update', $bulletin);
        $request->validate(['statut' => ['required', Rule::enum(StatutBulletin::class)]]);
        $bulletin->update(['statut' => $request->statut]);

        return back()->with('succes', 'Statut du bulletin mis à jour.');
    }

    public function destroy(BulletinPaie $bulletin)
    {
        $this->authorize('delete', $bulletin);
        $bulletin->delete();

        return redirect()->route('paie.index')->with('succes', 'Bulletin supprimé.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
