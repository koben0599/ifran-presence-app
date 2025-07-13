<?php

namespace Database\Seeders;

use App\Models\Seance;
use App\Models\Classe;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SeancesTableSeeder extends Seeder
{
    public function run()
    {
        $classes = Classe::all();
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();

        if ($classes->isEmpty() || $modules->isEmpty() || $enseignants->isEmpty()) {
            $this->command->error('Classes, modules ou enseignants manquants.');
            return;
        }

        // Générer des séances pour la semaine courante
        $lundi = Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        for ($jour = 0; $jour < 5; $jour++) { // Lundi à vendredi
            $date = $lundi->copy()->addDays($jour);
            
            foreach ($classes as $classe) {
                $module = $modules->random();
                $enseignant = $enseignants->random();
                
                // Créer 1-2 séances par classe par jour
                $nbSeances = rand(1, 2);
                
                for ($i = 0; $i < $nbSeances; $i++) {
                    $heureDebut = ['08:00', '10:00', '14:00', '16:00'][array_rand(['08:00', '10:00', '14:00', '16:00'])];
                    $heureFin = $this->ajouterHeures($heureDebut, 2);
                    
                    $type = ['presentiel', 'elearning', 'workshop'][array_rand(['presentiel', 'elearning', 'workshop'])];
                    $salle = ['A101', 'A102', 'A103', 'B201', 'B202'][array_rand(['A101', 'A102', 'A103', 'B201', 'B202'])];
                    
                    Seance::create([
                        'module_id' => $module->id,
                        'enseignant_id' => $enseignant->id,
                        'classe_id' => $classe->id,
                        'date_debut' => $date->copy()->setTimeFromTimeString($heureDebut),
                        'date_fin' => $date->copy()->setTimeFromTimeString($heureFin),
                        'type' => $type,
                        'salle' => $salle,
                    ]);
                }
            }
        }

        $this->command->info('Séances créées avec succès !');
    }

    private function ajouterHeures($heure, $heuresAAjouter)
    {
        $timestamp = strtotime($heure);
        $nouvelleHeure = date('H:i', $timestamp + ($heuresAAjouter * 3600));
        return $nouvelleHeure;
    }
} 