<?php

namespace App\Http\Controllers;

use App\Services\ContratService;
use App\Services\PaieService;
use App\Services\PresenceService;
use App\Services\StatistiqueRhService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        protected ContratService $contrats,
        protected PresenceService $presences,
        protected PaieService $paie,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // Périmètre : tout le groupe pour les rôles Groupe, sinon les filiales de l'utilisateur.
        $perimetre = $user->peutVoirToutLeGroupe() ? null : $user->filialesAccessibles();
        $stats = new StatistiqueRhService($perimetre);

        $taux = $user->can('presence.view')
            ? $this->presences->tauxParFiliale($perimetre, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth())
            : collect();

        $masse = $user->can('paie.view')
            ? $this->paie->masseSalariale($perimetre, Carbon::now()->format('Y-m'))
            : collect();

        return view('dashboard.index', [
            'estVueGroupe' => $user->peutVoirToutLeGroupe(),
            'indicateurs' => $stats->indicateursCles(),
            'sexe' => $stats->repartitionSexe(),
            'effectifParFiliale' => $stats->effectifParFiliale(),
            'contrats' => $stats->repartitionContrats(),
            'aRenouveler' => $user->can('contrat.view')
                ? $this->contrats->aRenouvelerPourPerimetre($perimetre)
                : collect(),
            'tauxPresence' => $taux,
            'masseSalariale' => $masse,
        ]);
    }
}
