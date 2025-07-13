<?php

namespace Database\Seeders;

use App\Models\Presence;
use App\Models\Seance;
use App\Models\User;
use Illuminate\Database\Seeder;

class PresencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seances = Seance::all();
        $etudiants = User::where('role', 'etudiant')->get();

        if ($seances->isEmpty() || $etudiants->isEmpty()) {
            $this->command->error('Séances ou étudiants manquants. Lancez d\'abord les autres seeders.');
            return;
        }

        $statuts = ['present', 'absent', 'retard'];
        $poids = [70, 20, 10]; // 70% présents, 20% absents, 10% retards

        foreach ($seances as $seance) {
            // Récupérer les étudiants de la classe de cette séance
            $etudiantsClasse = $etudiants->where('classe_id', $seance->classe_id);
            
            foreach ($etudiantsClasse as $etudiant) {
                // Générer un statut basé sur les poids
                $statut = $this->choisirStatut($statuts, $poids);
                
                // Ajouter une variation selon l'étudiant (certains sont plus assidus)
                $assiduite = $this->getAssiduiteEtudiant($etudiant->id);
                if ($assiduite < 0.6 && $statut === 'present') {
                    $statut = $statuts[array_rand($statuts)];
                }

                Presence::updateOrCreate(
                    [
                        'etudiant_id' => $etudiant->id,
                        'seance_id' => $seance->id,
                    ],
                    [
                        'statut' => $statut,
                    ]
                );
            }
        }

        $this->command->info('Présences créées avec succès !');
    }

    private function choisirStatut($statuts, $poids)
    {
        $total = array_sum($poids);
        $rand = mt_rand(1, $total);
        
        $cumul = 0;
        foreach ($poids as $index => $poids) {
            $cumul += $poids;
            if ($rand <= $cumul) {
                return $statuts[$index];
            }
        }
        
        return $statuts[0]; // fallback
    }

    private function getAssiduiteEtudiant($etudiantId)
    {
        // Simuler une assiduité basée sur l'ID de l'étudiant
        return 0.5 + (($etudiantId % 10) * 0.05); // Entre 0.5 et 1.0
    }

    private function genererCommentaire($statut)
    {
        $commentaires = [
            'present' => ['Présent', 'Assidu', 'À l\'heure'],
            'absent' => ['Absent', 'Non justifié', 'Pas de nouvelle'],
            'retard' => ['Retard de 15 min', 'Retard de 30 min', 'Arrivé en retard'],
            'justifie' => ['Certificat médical', 'Justification parentale', 'Motif valable']
        ];

        $commentairesStatut = $commentaires[$statut] ?? ['Aucun commentaire'];
        return $commentairesStatut[array_rand($commentairesStatut)];
    }
} 