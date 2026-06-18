<?php

namespace Database\Seeders;

use App\Models\Filiale;
use Illuminate\Database\Seeder;

class FilialeSeeder extends Seeder
{
    public function run(): void
    {
        $filiales = [
            ['code' => 'GCMI', 'nom' => 'GCM Industries', 'domaine' => 'Industrie / cimenterie', 'ville' => 'Ouagadougou'],
            ['code' => 'GCMIMMO', 'nom' => 'GCM Immobilier', 'domaine' => 'Immobilier', 'ville' => 'Ouagadougou'],
            ['code' => 'FASOEN', 'nom' => 'Faso Energy', 'domaine' => 'Énergie solaire', 'ville' => 'Ouagadougou'],
            ['code' => 'ECOOIL', 'nom' => 'Eco Oil', 'domaine' => 'Distribution pétrolière', 'ville' => 'Bobo-Dioulasso'],
            ['code' => 'ECOFOOD', 'nom' => 'Eco Food', 'domaine' => 'Agroalimentaire / restauration', 'ville' => 'Ouagadougou'],
            ['code' => 'AMKO', 'nom' => 'AMKO Trading SA', 'domaine' => 'Trading / négoce', 'ville' => 'Ouagadougou'],
        ];

        foreach ($filiales as $f) {
            Filiale::firstOrCreate(['code' => $f['code']], $f + ['pays' => 'Burkina Faso', 'statut' => true]);
        }
    }
}
