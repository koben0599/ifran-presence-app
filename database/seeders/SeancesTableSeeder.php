<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeancesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('seances')->insertOrIgnore([
            [
                'module_id' => 1,
                'enseignant_id' => 3,
                'classe' => 'L1',
                'date_debut' => Carbon::now()->addDay()->setTime(8, 0),
                'date_fin' => Carbon::now()->addDay()->setTime(10, 0),
                'type_cours' => 'presentiel',
                'salle' => 'A101',
            ],
            [
                'module_id' => 2,
                'enseignant_id' => 3,
                'classe' => 'L1',
                'date_debut' => Carbon::now()->addDays(2)->setTime(10, 0),
                'date_fin' => Carbon::now()->addDays(2)->setTime(12, 0),
                'type_cours' => 'e-learning',
                'salle' => 'A102',
            ],
        ]);
    }
} 