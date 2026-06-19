<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Conge;
use App\Models\Contrat;
use App\Models\Employe;
use App\Services\ExportService;
use App\Services\RapportService;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function __construct(
        protected RapportService $rapports,
        protected ExportService $export,
    ) {}

    protected function autoriser(Request $request): void
    {
        abort_unless($request->user()->can('rapport.consulter'), 403);
    }

    protected function perimetre(Request $request): ?array
    {
        return $request->user()->peutVoirToutLeGroupe() ? null : $request->user()->filialesAccessibles();
    }

    public function index(Request $request)
    {
        $this->autoriser($request);

        $stats = $this->rapports->statistiquesParFiliale($this->perimetre($request));

        return view('rapports.index', [
            'stats' => $stats,
            'totaux' => $this->rapports->totaux($stats),
            'estVueGroupe' => $request->user()->peutVoirToutLeGroupe(),
        ]);
    }

    public function consolide(Request $request)
    {
        $this->autoriser($request);

        $stats = $this->rapports->statistiquesParFiliale($this->perimetre($request));

        return view('rapports.consolide', [
            'stats' => $stats,
            'totaux' => $this->rapports->totaux($stats),
            'genereLe' => now(),
        ]);
    }

    public function exportEmployes(Request $request)
    {
        $this->autoriser($request);

        $lignes = Employe::with(['filiale', 'poste', 'departement'])->orderBy('nom')->get()
            ->map(fn ($e) => [
                $e->matricule, $e->nom, $e->prenom, $e->sexe,
                $e->filiale->nom ?? '', $e->departement->nom ?? '', $e->poste->intitule ?? '',
                optional($e->date_embauche)->format('Y-m-d'), $e->statut->libelle(),
            ]);

        return $this->export->csv('employes.csv',
            ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Filiale', 'Département', 'Poste', 'Embauche', 'Statut'],
            $lignes);
    }

    public function exportContrats(Request $request)
    {
        $this->autoriser($request);

        $lignes = Contrat::with(['employe', 'filiale'])->latest('date_debut')->get()
            ->map(fn ($c) => [
                $c->reference, $c->employe->nom_complet ?? '', $c->filiale->nom ?? '',
                $c->type_contrat->libelle(), optional($c->date_debut)->format('Y-m-d'),
                optional($c->date_fin)->format('Y-m-d'), $c->salaire_base, $c->devise, $c->statut->libelle(),
            ]);

        return $this->export->csv('contrats.csv',
            ['Référence', 'Employé', 'Filiale', 'Type', 'Début', 'Fin', 'Salaire base', 'Devise', 'Statut'],
            $lignes);
    }

    public function exportConges(Request $request)
    {
        $this->autoriser($request);

        $lignes = Conge::with(['employe'])->latest('date_debut')->get()
            ->map(fn ($c) => [
                $c->employe->nom_complet ?? '', $c->type_conge->libelle(),
                optional($c->date_debut)->format('Y-m-d'), optional($c->date_fin)->format('Y-m-d'),
                $c->nombre_jours, $c->statut_validation->libelle(),
            ]);

        return $this->export->csv('conges.csv',
            ['Employé', 'Type', 'Début', 'Fin', 'Jours', 'Statut'],
            $lignes);
    }

    public function exportBulletins(Request $request)
    {
        $this->autoriser($request);

        $periode = $request->periode ?: now()->format('Y-m');
        $lignes = BulletinPaie::with(['employe', 'filiale'])->where('periode', $periode)->get()
            ->map(fn ($b) => [
                $b->employe->nom_complet ?? '', $b->filiale->nom ?? '', $b->periode,
                $b->salaire_brut, $b->total_cotisations, $b->total_retenues, $b->net_a_payer, $b->cout_employeur,
                $b->statut->libelle(),
            ]);

        return $this->export->csv("bulletins-{$periode}.csv",
            ['Employé', 'Filiale', 'Période', 'Brut', 'Cotisations', 'Retenues', 'Net à payer', 'Coût employeur', 'Statut'],
            $lignes);
    }
}
