<?php

use App\Http\Controllers\CongeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\FilialeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
});

require __DIR__ . '/auth.php'; // fourni par Laravel Breeze (à installer)
