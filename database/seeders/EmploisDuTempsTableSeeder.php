<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploisDuTempsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('emploi_du_temps')->insertOrIgnore([
            [
                'classe' => 'L1',
                'module_id' => 1,
                'enseignant_id' => 3,
                'jour_semaine' => 1,
                'heure_debut' => '08:00',
                'heure_fin' => '10:00',
                'type_cours' => 'presentiel',
                'salle' => 'A101',
                'est_actif' => true,
            ],
            [
                'classe' => 'L1',
                'module_id' => 2,
                'enseignant_id' => 3,
                'jour_semaine' => 2,
                'heure_debut' => '10:00',
                'heure_fin' => '12:00',
                'type_cours' => 'e-learning',
                'salle' => 'A102',
                'est_actif' => true,
            ],
        ]);
    }
}
