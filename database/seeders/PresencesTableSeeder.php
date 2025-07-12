<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresencesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('presences')->insertOrIgnore([
            [
                'etudiant_id' => 4,
                'seance_id' => 1,
                'statut' => 'present',
                'justifie' => false,
            ],
            [
                'etudiant_id' => 4,
                'seance_id' => 2,
                'statut' => 'absent',
                'justifie' => true,
            ],
        ]);
    }
} 