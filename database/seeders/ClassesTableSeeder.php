<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            'B3DEV',
            'B2DEV', 
            'B1DEV',
            'B3CREA',
            'B2CREA',
            'B1CREA',
        ];

        foreach ($classes as $classe) {
            Classe::create(['nom' => $classe]);
        }

        $this->command->info('Classes créées avec succès !');
    }
} 