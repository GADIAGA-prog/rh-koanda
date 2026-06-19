<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\DocumentRh;
use App\Models\Employe;
use App\Models\Filiale;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentRhController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
        $this->authorizeResource(DocumentRh::class, 'document');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $documents = DocumentRh::with(['employe', 'filiale'])
            ->visiblesPour($user)
            ->when($request->filiale_id, fn ($q, $v) => $q->where('filiale_id', $v))
            ->when($request->type_document, fn ($q, $v) => $q->where('type_document', $v))
            ->latest('id')
            ->paginate(20)->withQueryString();

        $expirant = DocumentRh::visiblesPour($user)
            ->expirantAvant(Carbon::today()->addDays(30))
            ->whereDate('date_expiration', '>=', today())
            ->count();

        return view('documents.index', [
            'documents' => $documents,
            'filiales' => $this->filialesAccessibles(),
            'expirant' => $expirant,
            'filtres' => $request->all(),
        ]);
    }

    public function create(Request $request)
    {
        return view('documents.create', [
            'employes' => Employe::actifs()->orderBy('nom')->get(),
            'employeChoisi' => $request->integer('employe_id') ?: null,
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $document = $this->service->enregistrer($request->safe()->except('fichier'), $request->file('fichier'), $request->user()->id);

        return redirect()->route('documents.index')->with('succes', 'Document téléversé.');
    }

    public function download(DocumentRh $document)
    {
        $this->authorize('view', $document);
        abort_unless(Storage::disk('local')->exists($document->fichier), 404);

        return Storage::disk('local')->download($document->fichier, $document->titre);
    }

    public function destroy(DocumentRh $document)
    {
        $this->service->supprimer($document);

        return back()->with('succes', 'Document supprimé.');
    }

    protected function filialesAccessibles()
    {
        return Filiale::query()
            ->when(! auth()->user()->peutVoirToutLeGroupe(),
                fn ($q) => $q->whereIn('id', auth()->user()->filialesAccessibles()))
            ->orderBy('nom')->get();
    }
}
