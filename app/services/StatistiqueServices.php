<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Module;
use App\Models\EmploiDuTemps;
use App\Models\Seance;
use Carbon\Carbon;

class StatistiqueServices
{
    public static function genererSeances($dateDebut = null, $dateFin = null)
    {
        if (!$dateDebut) {
            $dateDebut = Carbon::now()->startOfWeek();
        }
        if (!$dateFin) {
            $dateFin = Carbon::now()->addWeeks(2)->endOfWeek();
        }

        $emplois = EmploiDuTemps::where('est_actif', true)->get();
        
        foreach ($emplois as $emploi) {
            $date = $dateDebut->copy();
            
            while ($date <= $dateFin) {
                // Vérifier si c'est le bon jour de la semaine
                if ($date->dayOfWeek == $emploi->jour_semaine) {
                    // Créer la séance
                    $heureDebut = Carbon::parse($emploi->heure_debut);
                    $heureFin = Carbon::parse($emploi->heure_fin);
                    
                    $dateDebutSeance = $date->copy()->setTime($heureDebut->hour, $heureDebut->minute);
                    $dateFinSeance = $date->copy()->setTime($heureFin->hour, $heureFin->minute);
                    
                    Seance::updateOrCreate(
                        [
                            'module_id' => $emploi->module_id,
                            'enseignant_id' => $emploi->enseignant_id,
                            'date_debut' => $dateDebutSeance,
                            'classe' => $emploi->classe
                        ],
                        [
                            'date_fin' => $dateFinSeance,
                            'type' => $emploi->type_cours,
                            'annule' => false
                        ]
                    );
                }
                
                $date->addDay();
            }
        }
    }

    public static function calculerTauxPresence($etudiantId, $moduleId = null, $dateDebut = null, $dateFin = null)
    {
        $query = Seance::whereHas('presences', function($q) use ($etudiantId) {
            $q->where('etudiant_id', $etudiantId);
        });

        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }

        if ($dateDebut) {
            $query->where('date_debut', '>=', $dateDebut);
        }

        if ($dateFin) {
            $query->where('date_debut', '<=', $dateFin);
        }

        $seances = $query->get();
        $totalSeances = $seances->count();
        
        if ($totalSeances === 0) {
            return 0;
        }

        $presences = 0;
        foreach ($seances as $seance) {
            $presence = $seance->presences()->where('etudiant_id', $etudiantId)->first();
            if ($presence && $presence->statut === 'present') {
                $presences++;
            }
        }

        return ($presences / $totalSeances) * 100;
    }

    public static function calculerNoteAssiduite($etudiantId, $moduleId)
    {
        $seances = Seance::where('module_id', $moduleId)
            ->whereHas('presences', function($q) use ($etudiantId) {
                $q->where('etudiant_id', $etudiantId);
            })
            ->get();

        $totalSeances = $seances->count();
        
        if ($totalSeances === 0) {
            return 0;
        }

        $presences = 0;
        foreach ($seances as $seance) {
            $presence = $seance->presences()->where('etudiant_id', $etudiantId)->first();
            if ($presence && $presence->statut === 'present') {
                $presences++;
            }
        }

        // Règle de trois pour ramener à 20/20
        return ($presences / $totalSeances) * 20;
    }

    public static function tauxPresenceParClasse($classe, $dateDebut, $dateFin)
    {
        // Implémentation du calcul...
    }
}
