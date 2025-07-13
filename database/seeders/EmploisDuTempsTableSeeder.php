<?php

namespace Database\Seeders;

use App\Models\EmploiDuTemps;
use App\Models\Classe;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmploisDuTempsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classe::all();
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();

        if ($classes->isEmpty() || $modules->isEmpty() || $enseignants->isEmpty()) {
            $this->command->error('Classes, modules ou enseignants manquants. Lancez d\'abord les autres seeders.');
            return;
        }

        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        $types = ['presentiel', 'elearning', 'workshop'];
        $salles = ['Salle A101', 'Salle A102', 'Salle A103', 'Salle B201', 'Salle B202', 'Labo Info 1', 'Labo Info 2', 'Amphi 1', 'Amphi 2'];

        // Créer des emplois du temps pour chaque classe
        foreach ($classes as $classe) {
            // 4-6 modules par classe selon le niveau
            $nbModules = $classe->nom[1] == '3' ? 6 : ($classe->nom[1] == '2' ? 5 : 4);
            $modulesClasse = $modules->random($nbModules);
            
            foreach ($modulesClasse as $module) {
                // 2-3 créneaux par module selon l'importance
                $nbCreneaux = in_array($module->nom, ['Développement Web', 'Design UI/UX', 'Base de données']) ? 3 : 2;
                
                for ($i = 0; $i < $nbCreneaux; $i++) {
                    $jour = $joursSemaine[array_rand($joursSemaine)];
                    $type = $types[array_rand($types)];
                    $salle = $salles[array_rand($salles)];
                    $enseignant = $enseignants->random();

                    // Heures différentes selon le type et le module
                    if ($type === 'presentiel') {
                        $heureDebut = ['08:00', '10:00', '14:00', '16:00'][array_rand(['08:00', '10:00', '14:00', '16:00'])];
                        $heureFin = $this->ajouterHeures($heureDebut, 2);
                    } elseif ($type === 'elearning') {
                        $heureDebut = ['09:00', '11:00', '15:00'][array_rand(['09:00', '11:00', '15:00'])];
                        $heureFin = $this->ajouterHeures($heureDebut, 1.5);
                    } else { // workshop
                        $heureDebut = ['13:00', '15:00'][array_rand(['13:00', '15:00'])];
                        $heureFin = $this->ajouterHeures($heureDebut, 3);
                    }

                    EmploiDuTemps::create([
                        'classe_id' => $classe->id,
                        'module_id' => $module->id,
                        'enseignant_id' => $enseignant->id,
                        'jour_semaine' => $jour,
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'type' => $type,
                        'salle' => $salle,
                        'est_actif' => true
                    ]);
                }
            }
        }

        $this->command->info('Emplois du temps créés avec succès !');
    }

    private function ajouterHeures($heure, $heuresAAjouter)
    {
        $timestamp = strtotime($heure);
        $nouvelleHeure = date('H:i', $timestamp + ($heuresAAjouter * 3600));
        return $nouvelleHeure;
    }
}
