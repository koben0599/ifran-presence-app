<?php

namespace App\Services;

use App\Models\User;
use App\Models\Classe;
use App\Models\Module;
use App\Models\Seance;
use App\Models\Presence;
use App\Models\EmploiDuTemps;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatistiqueService
{
    /**
     * Obtenir les statistiques globales
     */
    public function getStatistiquesGlobales(): array
    {
        return [
            'total_etudiants' => User::where('role', 'etudiant')->count(),
            'total_enseignants' => User::where('role', 'enseignant')->count(),
            'total_coordinateurs' => User::where('role', 'coordinateur')->count(),
            'total_classes' => Classe::count(),
            'total_modules' => Module::count(),
            'total_seances' => Seance::count(),
            'total_presences' => Presence::count(),
            'taux_presence_global' => $this->calculerTauxPresenceGlobal(),
        ];
    }

    /**
     * Obtenir les statistiques par classe
     */
    public function getStatistiquesParClasse(): array
    {
        $classes = Classe::with('etudiants')->get();
        $statistiques = [];

        foreach ($classes as $classe) {
            $seances = Seance::where('classe_id', $classe->id)->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seances)->count();
            $presents = Presence::whereIn('seance_id', $seances)
                ->where('statut', 'present')
                ->count();

            $statistiques[] = [
                'classe' => $classe->nom,
                'etudiants' => $classe->etudiants->count(),
                'seances' => $seances->count(),
                'total_presences' => $totalPresences,
                'presents' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            ];
        }

        return $statistiques;
    }

    /**
     * Obtenir les statistiques par module
     */
    public function getStatistiquesParModule(): array
    {
        $modules = Module::with(['seances', 'emploisDuTemps'])->get();
        $statistiques = [];

        foreach ($modules as $module) {
            $seances = $module->seances->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seances)->count();
            $presents = Presence::whereIn('seance_id', $seances)
                ->where('statut', 'present')
                ->count();

            $statistiques[] = [
                'module' => $module->nom,
                'code' => $module->code,
                'seances' => $seances->count(),
                'heures_totales' => $module->heures_totales,
                'total_presences' => $totalPresences,
                'presents' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            ];
        }

        return $statistiques;
    }

    /**
     * Obtenir les statistiques par enseignant
     */
    public function getStatistiquesParEnseignant(): array
    {
        $enseignants = User::where('role', 'enseignant')->with('seancesEnseignant')->get();
        $statistiques = [];

        foreach ($enseignants as $enseignant) {
            $seances = $enseignant->seancesEnseignant->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seances)->count();
            $presents = Presence::whereIn('seance_id', $seances)
                ->where('statut', 'present')
                ->count();

            $statistiques[] = [
                'enseignant' => $enseignant->nom . ' ' . $enseignant->prenom,
                'seances' => $seances->count(),
                'total_presences' => $totalPresences,
                'presents' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            ];
        }

        return $statistiques;
    }

    /**
     * Obtenir l'évolution des présences sur les 4 dernières semaines
     */
    public function getEvolutionPresences(): array
    {
        $evolution = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $semaine = Carbon::now()->subWeeks($i);
            $debut = $semaine->copy()->startOfWeek(Carbon::MONDAY);
            $fin = $semaine->copy()->endOfWeek(Carbon::FRIDAY);

            $seances = Seance::whereBetween('date_debut', [$debut, $fin])->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seances)->count();
            $presents = Presence::whereIn('seance_id', $seances)
                ->where('statut', 'present')
                ->count();

            $evolution[] = [
                'semaine' => $debut->format('d/m/Y') . ' - ' . $fin->format('d/m/Y'),
                'total' => $totalPresences,
                'presents' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            ];
        }

        return $evolution;
    }

    /**
     * Obtenir les statistiques par type de cours
     */
    public function getStatistiquesParType(): array
    {
        $types = ['presentiel', 'elearning', 'workshop'];
        $statistiques = [];

        foreach ($types as $type) {
            $seances = Seance::where('type', $type)->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seances)->count();
            $presents = Presence::whereIn('seance_id', $seances)
                ->where('statut', 'present')
                ->count();

            $statistiques[] = [
                'type' => $type,
                'type_francais' => $this->getTypeFrancais($type),
                'seances' => $seances->count(),
                'total_presences' => $totalPresences,
                'presents' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            ];
        }

        return $statistiques;
    }

    /**
     * Calculer le taux de présence global
     */
    private function calculerTauxPresenceGlobal(): float
    {
        $totalPresences = Presence::count();
        $presents = Presence::where('statut', 'present')->count();

        return $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0;
    }

    /**
     * Obtenir le nom français du type
     */
    private function getTypeFrancais(string $type): string
    {
        $types = [
            'presentiel' => 'Présentiel',
            'elearning' => 'E-learning',
            'workshop' => 'Atelier'
        ];

        return $types[$type] ?? $type;
    }
} 