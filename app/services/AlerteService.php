<?php

namespace App\Services;

use App\Models\User;
use App\Models\Presence;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlerteService
{
    /**
     * Obtenir les alertes d'absence pour un étudiant
     */
    public function getAlertesEtudiant(int $etudiantId): array
    {
        $etudiant = User::find($etudiantId);
        if (!$etudiant || $etudiant->role !== 'etudiant') {
            return [];
        }

        $alertes = [];

        // Absences consécutives
        $absencesConsecutives = $this->getAbsencesConsecutives($etudiantId);
        if (count($absencesConsecutives) >= 3) {
            $alertes[] = [
                'type' => 'absence_consecutive',
                'niveau' => 'danger',
                'message' => 'Absences consécutives détectées (' . count($absencesConsecutives) . ' séances)',
                'seances' => $absencesConsecutives
            ];
        }

        // Taux de présence faible
        $tauxPresence = $this->calculerTauxPresenceEtudiant($etudiantId);
        if ($tauxPresence < 70) {
            $alertes[] = [
                'type' => 'taux_faible',
                'niveau' => 'warning',
                'message' => 'Taux de présence faible (' . $tauxPresence . '%)',
                'taux' => $tauxPresence
            ];
        }

        // Retards fréquents
        $retards = $this->getRetardsFrequents($etudiantId);
        if (count($retards) >= 5) {
            $alertes[] = [
                'type' => 'retards_frequents',
                'niveau' => 'warning',
                'message' => 'Retards fréquents détectés (' . count($retards) . ' retards)',
                'retards' => $retards
            ];
        }

        return $alertes;
    }

    /**
     * Obtenir les alertes pour une classe
     */
    public function getAlertesClasse(int $classeId): array
    {
        $alertes = [];
        $etudiants = User::where('role', 'etudiant')
            ->where('classe_id', $classeId)
            ->get();

        foreach ($etudiants as $etudiant) {
            $alertesEtudiant = $this->getAlertesEtudiant($etudiant->id);
            if (!empty($alertesEtudiant)) {
                $alertes[] = [
                    'etudiant' => $etudiant,
                    'alertes' => $alertesEtudiant
                ];
            }
        }

        return $alertes;
    }

    /**
     * Obtenir les alertes globales pour les coordinateurs
     */
    public function getAlertesGlobales(): array
    {
        $alertes = [];

        // Étudiants avec taux de présence < 60%
        $etudiantsFaibleTaux = $this->getEtudiantsFaibleTaux();
        if (!empty($etudiantsFaibleTaux)) {
            $alertes[] = [
                'type' => 'etudiants_faible_taux',
                'niveau' => 'danger',
                'message' => count($etudiantsFaibleTaux) . ' étudiant(s) avec un taux de présence < 60%',
                'etudiants' => $etudiantsFaibleTaux
            ];
        }

        // Classes avec taux de présence < 70%
        $classesFaibleTaux = $this->getClassesFaibleTaux();
        if (!empty($classesFaibleTaux)) {
            $alertes[] = [
                'type' => 'classes_faible_taux',
                'niveau' => 'warning',
                'message' => count($classesFaibleTaux) . ' classe(s) avec un taux de présence < 70%',
                'classes' => $classesFaibleTaux
            ];
        }

        return $alertes;
    }

    /**
     * Obtenir les absences consécutives d'un étudiant
     */
    private function getAbsencesConsecutives(int $etudiantId): Collection
    {
        $seances = Seance::whereHas('presences', function($query) use ($etudiantId) {
            $query->where('etudiant_id', $etudiantId)
                  ->where('statut', 'absent');
        })
        ->with(['presences' => function($query) use ($etudiantId) {
            $query->where('etudiant_id', $etudiantId);
        }])
        ->orderBy('date_debut')
        ->get();

        $absencesConsecutives = collect();
        $consecutiveCount = 0;

        foreach ($seances as $seance) {
            $presence = $seance->presences->first();
            if ($presence && $presence->statut === 'absent') {
                $consecutiveCount++;
                if ($consecutiveCount >= 3) {
                    $absencesConsecutives->push($seance);
                }
            } else {
                $consecutiveCount = 0;
            }
        }

        return $absencesConsecutives;
    }

    /**
     * Calculer le taux de présence d'un étudiant
     */
    private function calculerTauxPresenceEtudiant(int $etudiantId): float
    {
        $totalPresences = Presence::where('etudiant_id', $etudiantId)->count();
        $presents = Presence::where('etudiant_id', $etudiantId)
            ->where('statut', 'present')
            ->count();

        return $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0;
    }

    /**
     * Obtenir les retards fréquents d'un étudiant
     */
    private function getRetardsFrequents(int $etudiantId): Collection
    {
        return Presence::where('etudiant_id', $etudiantId)
            ->where('statut', 'retard')
            ->with('seance')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtenir les étudiants avec un taux de présence faible
     */
    private function getEtudiantsFaibleTaux(): Collection
    {
        $etudiants = User::where('role', 'etudiant')->get();
        
        return $etudiants->filter(function($etudiant) {
            $taux = $this->calculerTauxPresenceEtudiant($etudiant->id);
            return $taux < 60;
        });
    }

    /**
     * Obtenir les classes avec un taux de présence faible
     */
    private function getClassesFaibleTaux(): Collection
    {
        $classes = \App\Models\Classe::all();
        
        return $classes->filter(function($classe) {
            return $classe->taux_presence < 70;
        });
    }
} 