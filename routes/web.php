<?php

use App\Http\Controllers\CongeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeController;
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
