<?php

namespace Database\Seeders;

use App\Models\RubriquePaie;
use Illuminate\Database\Seeder;

/**
 * Rubriques de DÉMONSTRATION communes au groupe (filiale_id = null).
 *
 * ⚠️ Les taux et montants ci-dessous sont des valeurs indicatives « à vérifier ».
 * La réglementation sociale et fiscale du Burkina Faso (CNSS, IUTS, barèmes)
 * doit être validée par un comptable avant toute exploitation réelle.
 */
class RubriquePaieSeeder extends Seeder
{
    public function run(): void
    {
        $rubriques = [
            ['code' => 'TRANSPORT', 'libelle' => 'Prime de transport (à vérifier)', 'type' => 'gain', 'mode_calcul' => 'fixe', 'montant' => 25000, 'taux' => null, 'base_calcul' => null, 'imposable' => false, 'ordre' => 10],
            ['code' => 'LOGEMENT', 'libelle' => 'Indemnité de logement (à vérifier)', 'type' => 'gain', 'mode_calcul' => 'pourcentage', 'montant' => null, 'taux' => 15, 'base_calcul' => 'salaire_base', 'imposable' => true, 'ordre' => 20],
            ['code' => 'CNSS_SAL', 'libelle' => 'CNSS part salariale (à vérifier)', 'type' => 'cotisation', 'mode_calcul' => 'pourcentage', 'montant' => null, 'taux' => 5.5, 'base_calcul' => 'brut', 'imposable' => false, 'ordre' => 30],
            ['code' => 'IUTS', 'libelle' => 'IUTS – impôt sur salaires (à vérifier)', 'type' => 'retenue', 'mode_calcul' => 'pourcentage', 'montant' => null, 'taux' => 12, 'base_calcul' => 'brut', 'imposable' => false, 'ordre' => 40],
        ];

        foreach ($rubriques as $r) {
            RubriquePaie::updateOrCreate(
                ['filiale_id' => null, 'code' => $r['code']],
                array_merge($r, ['filiale_id' => null, 'actif' => true]),
            );
        }
    }
}
