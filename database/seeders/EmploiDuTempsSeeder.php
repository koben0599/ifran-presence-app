<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploiDuTempsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si des modules existent, sinon en créer
        $modules = DB::table('modules')->get();
        if ($modules->isEmpty()) {
            DB::table('modules')->insert([
                ['nom' => 'Développement Web', 'code' => 'WEB101', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Base de données', 'code' => 'DB101', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Design UI/UX', 'code' => 'UI101', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Marketing Digital', 'code' => 'MKT101', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Vérifier si des classes existent, sinon en créer
        $classes = DB::table('classes')->get();
        if ($classes->isEmpty()) {
            DB::table('classes')->insert([
                ['nom' => 'B3DEV', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'B2DEV', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'B3CREA', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Récupérer les IDs
        $modules = DB::table('modules')->get();
        $classes = DB::table('classes')->get();
        $enseignants = DB::table('users')->where('role', 'enseignant')->get();

        // Créer l'emploi du temps
        $emploiDuTemps = [
            // Lundi
            [
                'jour_semaine' => 'Lundi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '12:00:00',
                'module_id' => $modules->where('nom', 'Développement Web')->first()->id,
                'classe_id' => $classes->where('nom', 'B3DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'cours',
                'salle' => 'Salle 101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jour_semaine' => 'Lundi',
                'heure_debut' => '14:00:00',
                'heure_fin' => '17:00:00',
                'module_id' => $modules->where('nom', 'Base de données')->first()->id,
                'classe_id' => $classes->where('nom', 'B2DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'workshop',
                'salle' => 'Salle 102',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mardi
            [
                'jour_semaine' => 'Mardi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '12:00:00',
                'module_id' => $modules->where('nom', 'Design UI/UX')->first()->id,
                'classe_id' => $classes->where('nom', 'B3CREA')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'cours',
                'salle' => 'Salle 103',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jour_semaine' => 'Mardi',
                'heure_debut' => '14:00:00',
                'heure_fin' => '17:00:00',
                'module_id' => $modules->where('nom', 'Marketing Digital')->first()->id,
                'classe_id' => $classes->where('nom', 'B3CREA')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'e-learning',
                'salle' => 'Salle virtuelle',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mercredi
            [
                'jour_semaine' => 'Mercredi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '12:00:00',
                'module_id' => $modules->where('nom', 'Développement Web')->first()->id,
                'classe_id' => $classes->where('nom', 'B2DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'workshop',
                'salle' => 'Salle 101',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Jeudi
            [
                'jour_semaine' => 'Jeudi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '12:00:00',
                'module_id' => $modules->where('nom', 'Base de données')->first()->id,
                'classe_id' => $classes->where('nom', 'B3DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'cours',
                'salle' => 'Salle 102',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Vendredi
            [
                'jour_semaine' => 'Vendredi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '12:00:00',
                'module_id' => $modules->where('nom', 'Design UI/UX')->first()->id,
                'classe_id' => $classes->where('nom', 'B2DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'cours',
                'salle' => 'Salle 103',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jour_semaine' => 'Vendredi',
                'heure_debut' => '14:00:00',
                'heure_fin' => '17:00:00',
                'module_id' => $modules->where('nom', 'Marketing Digital')->first()->id,
                'classe_id' => $classes->where('nom', 'B3DEV')->first()->id,
                'enseignant_id' => $enseignants->first()?->id,
                'type' => 'e-learning',
                'salle' => 'Salle virtuelle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('emploi_du_temps')->insert($emploiDuTemps);
    }
}
