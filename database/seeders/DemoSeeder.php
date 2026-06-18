<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Employe;
use App\Models\Filiale;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin Groupe
        $admin = User::firstOrCreate(
            ['email' => 'admin@koandagroupe.bf'],
            ['name' => 'Administrateur Groupe', 'password' => Hash::make('password'), 'actif' => true]
        );
        $admin->assignRole('super-admin');

        // DRH Groupe
        $drh = User::firstOrCreate(
            ['email' => 'drh@koandagroupe.bf'],
            ['name' => 'DRH Groupe', 'password' => Hash::make('password'), 'actif' => true]
        );
        $drh->assignRole('drh-groupe');

        // Pour chaque filiale : un département, un poste, un RH local et quelques employés.
        foreach (Filiale::all() as $filiale) {
            $dep = Departement::firstOrCreate(
                ['filiale_id' => $filiale->id, 'nom' => 'Administration'],
                ['code' => 'ADM']
            );
            $poste = Poste::firstOrCreate(
                ['filiale_id' => $filiale->id, 'intitule' => 'Agent'],
                ['departement_id' => $dep->id, 'categorie' => 'Exécution']
            );

            $rh = User::firstOrCreate(
                ['email' => 'rh.' . strtolower($filiale->code) . '@koandagroupe.bf'],
                ['name' => 'RH ' . $filiale->nom, 'password' => Hash::make('password'), 'filiale_id' => $filiale->id, 'actif' => true]
            );
            $rh->assignRole('rh-filiale');

            for ($i = 1; $i <= 5; $i++) {
                Employe::withoutGlobalScope(\App\Models\Scopes\FilialeScope::class)->firstOrCreate(
                    ['matricule' => "KG-{$filiale->code}-" . str_pad($i, 4, '0', STR_PAD_LEFT)],
                    [
                        'nom' => 'Nom' . $i,
                        'prenom' => 'Prenom' . $i,
                        'sexe' => $i % 2 === 0 ? 'F' : 'M',
                        'filiale_id' => $filiale->id,
                        'departement_id' => $dep->id,
                        'poste_id' => $poste->id,
                        'date_embauche' => now()->subYears(rand(1, 5)),
                        'statut' => 'actif',
                    ]
                );
            }
        }
    }
}
