<?php

use App\Http\Controllers\CongeController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DocumentRhController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\OrganigrammeController;
use App\Http\Controllers\PaieController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RubriquePaieController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\FilialeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (fourni par Breeze, rebranché après écrasement de web.php)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Filiales
    Route::get('/filiales', [FilialeController::class, 'index'])->name('filiales.index');
    Route::post('/filiales', [FilialeController::class, 'store'])->name('filiales.store');

    // Employés (CRUD complet)
    Route::resource('employes', EmployeController::class);

    // Contrats (CRUD + renouvellement)
    Route::post('/contrats/{contrat}/renouveler', [ContratController::class, 'renouveler'])->name('contrats.renouveler');
    Route::resource('contrats', ContratController::class);

    // Organisation (départements, postes, fonctions, organigramme)
    Route::resource('departements', DepartementController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('postes', PosteController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('fonctions', FonctionController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/organigramme', [OrganigrammeController::class, 'index'])->name('organigramme');

    // Présences & absences
    Route::resource('presences', PresenceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/absences/{absence}/justificatif', [AbsenceController::class, 'justificatif'])->name('absences.justificatif');
    Route::resource('absences', AbsenceController::class)->except(['show']);

    // Missions (ordres de mission + workflow)
    Route::post('/missions/{mission}/soumettre', [MissionController::class, 'soumettre'])->name('missions.soumettre');
    Route::post('/missions/{mission}/valider', [MissionController::class, 'valider'])->name('missions.valider');
    Route::post('/missions/{mission}/refuser', [MissionController::class, 'refuser'])->name('missions.refuser');
    Route::post('/missions/{mission}/cloturer', [MissionController::class, 'cloturer'])->name('missions.cloturer');
    Route::resource('missions', MissionController::class);

    // Paie : catalogue des rubriques + bulletins
    Route::resource('rubriques', RubriquePaieController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/paie', [PaieController::class, 'index'])->name('paie.index');
    Route::post('/paie/generer', [PaieController::class, 'generer'])->name('paie.generer');
    Route::get('/paie/{bulletin}', [PaieController::class, 'show'])->name('paie.show');
    Route::get('/paie/{bulletin}/imprimer', [PaieController::class, 'imprimer'])->name('paie.imprimer');
    Route::patch('/paie/{bulletin}/statut', [PaieController::class, 'changerStatut'])->name('paie.statut');
    Route::delete('/paie/{bulletin}', [PaieController::class, 'destroy'])->name('paie.destroy');

    // Performance (évaluations)
    Route::resource('evaluations', PerformanceController::class);

    // Formation (sessions + participants + compétences)
    Route::post('/formations/{formation}/participants', [FormationController::class, 'ajouterParticipant'])->name('formations.participants.ajouter');
    Route::patch('/formations/{formation}/participants/{employe}', [FormationController::class, 'majParticipant'])->name('formations.participants.maj');
    Route::delete('/formations/{formation}/participants/{employe}', [FormationController::class, 'retirerParticipant'])->name('formations.participants.retirer');
    Route::post('/formations/{formation}/competence', [FormationController::class, 'acquerirCompetence'])->name('formations.competence');
    Route::resource('formations', FormationController::class);

    // Documents RH (téléversement privé + téléchargement contrôlé)
    Route::get('/documents/{document}/telecharger', [DocumentRhController::class, 'download'])->name('documents.download');
    Route::resource('documents', DocumentRhController::class)->only(['index', 'create', 'store', 'destroy']);

    // Discipline & sanctions
    Route::get('/sanctions/{sanction}/document', [SanctionController::class, 'download'])->name('sanctions.download');
    Route::resource('sanctions', SanctionController::class)->only(['index', 'create', 'store', 'destroy']);

    // Rapports & exports
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/consolide', [RapportController::class, 'consolide'])->name('rapports.consolide');
    Route::get('/rapports/export/employes', [RapportController::class, 'exportEmployes'])->name('rapports.export.employes');
    Route::get('/rapports/export/contrats', [RapportController::class, 'exportContrats'])->name('rapports.export.contrats');
    Route::get('/rapports/export/conges', [RapportController::class, 'exportConges'])->name('rapports.export.conges');
    Route::get('/rapports/export/bulletins', [RapportController::class, 'exportBulletins'])->name('rapports.export.bulletins');

    // Congés
    Route::get('/conges', [CongeController::class, 'index'])->name('conges.index');
    Route::get('/conges/nouveau', [CongeController::class, 'create'])->name('conges.create');
    Route::post('/conges', [CongeController::class, 'store'])->name('conges.store');
    Route::post('/conges/{conge}/valider', [CongeController::class, 'valider'])->name('conges.valider');
    Route::post('/conges/{conge}/refuser', [CongeController::class, 'refuser'])->name('conges.refuser');

    // Administration des accès (super-admin / drh-groupe)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('utilisateurs', UtilisateurController::class)->except(['show']);
        Route::patch('utilisateurs/{utilisateur}/activation', [UtilisateurController::class, 'basculerActivation'])->name('utilisateurs.activation');
        Route::patch('utilisateurs/{utilisateur}/mot-de-passe', [UtilisateurController::class, 'reinitialiserMotDePasse'])->name('utilisateurs.reset');

        Route::get('roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::put('roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
    });
});

require __DIR__ . '/auth.php'; // fourni par Laravel Breeze (à installer)
