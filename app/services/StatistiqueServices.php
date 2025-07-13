<?php

namespace App\Services;

use App\Models\Seance;
use Carbon\Carbon;

class StatistiqueService
{
    public static function calculerTauxPresence($etudiantId, $moduleId = null, $periode = null)
    {
        $query = Seance::whereHas('presences', function($q) use ($etudiantId) {
            $q->where('etudiant_id', $etudiantId);
        });

        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }

        if ($periode) {
            $dates = self::getDatesForPeriode($periode);
            $query->whereBetween('date_debut', $dates);
        }

        $seances = $query->get();
        $total = $seances->count();
        
        if ($total === 0) return 0;

        $present = $seances->filter(function($seance) use ($etudiantId) {
            return $seance->presences->where('etudiant_id', $etudiantId)->first()->statut === 'present';
        })->count();

        return round(($present / $total) * 100, 2);
    }

    private static function getDatesForPeriode($periode)
    {
        switch ($periode) {
            case 'semaine':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'mois':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'annee':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::create(1900), Carbon::now()];
        }
    }
}
