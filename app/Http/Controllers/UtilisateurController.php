<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUtilisateurRequest;
use App\Http\Requests\UpdateUtilisateurRequest;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\User;
use App\Services\UtilisateurService;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    public function __construct(protected UtilisateurService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $utilisateurs = User::with(['filiale', 'roles'])
            ->when($request->recherche, function ($q, $terme) {
                $q->where(fn ($q) => $q->where('name', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->role, fn ($q, $v) => $q->whereHas('roles', fn ($q) => $q->where('name', $v)))
            ->when($request->filled('actif'), fn ($q) => $q->where('actif', $request->boolean('actif')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.utilisateurs.index', [
            'utilisateurs' => $utilisateurs,
            'filtres' => $request->all(),
            'roles' => User::ROLES,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.utilisateurs.create', $this->referentiels());
    }

    public function store(StoreUtilisateurRequest $request)
    {
        $utilisateur = $this->service->creer($request->validated());

        return redirect()->route('admin.utilisateurs.index')
            ->with('succes', "Utilisateur {$utilisateur->name} créé.");
    }

    public function edit(User $utilisateur)
    {
        $this->authorize('update', $utilisateur);

        return view('admin.utilisateurs.edit', array_merge(
            ['utilisateur' => $utilisateur->load(['roles', 'filialesGerees', 'employe'])],
            $this->referentiels(),
        ));
    }

    public function update(UpdateUtilisateurRequest $request, User $utilisateur)
    {
        $this->service->modifier($utilisateur, $request->validated());

        return redirect()->route('admin.utilisateurs.index')
            ->with('succes', "Utilisateur {$utilisateur->name} mis à jour.");
    }

    public function destroy(User $utilisateur)
    {
        $this->authorize('delete', $utilisateur);
        $this->service->supprimer($utilisateur);

        return redirect()->route('admin.utilisateurs.index')->with('succes', 'Utilisateur supprimé.');
    }

    public function basculerActivation(User $utilisateur)
    {
        $this->authorize('update', $utilisateur);
        $actif = $this->service->basculerActivation($utilisateur);

        return back()->with('succes', $actif ? 'Compte activé.' : 'Compte désactivé.');
    }

    public function reinitialiserMotDePasse(Request $request, User $utilisateur)
    {
        $this->authorize('update', $utilisateur);
        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $this->service->reinitialiserMotDePasse($utilisateur, $request->password);

        return back()->with('succes', 'Mot de passe réinitialisé.');
    }

    protected function referentiels(): array
    {
        return [
            'roles' => User::ROLES,
            'filiales' => Filiale::orderBy('nom')->get(),
            'employesLibres' => Employe::sansFiltreFiliale()->whereNull('user_id')->orderBy('nom')->get(),
        ];
    }
}
