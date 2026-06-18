<?php

namespace App\Http\Controllers;

use App\Services\StatistiqueRhService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Périmètre : tout le groupe pour les rôles Groupe, sinon les filiales de l'utilisateur.
        $perimetre = $user->peutVoirToutLeGroupe() ? null : $user->filialesAccessibles();
        $stats = new StatistiqueRhService($perimetre);

        return view('dashboard.index', [
            'estVueGroupe' => $user->peutVoirToutLeGroupe(),
            'indicateurs' => $stats->indicateursCles(),
            'sexe' => $stats->repartitionSexe(),
            'effectifParFiliale' => $stats->effectifParFiliale(),
            'contrats' => $stats->repartitionContrats(),
        ]);
    }
}
