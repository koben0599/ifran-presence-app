<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EtudiantController extends Controller
{
    public function mesAbsences()
    {
        $etudiant = Auth::user();
        $absences = $etudiant->presencesEtudiant()
            ->where('statut', 'absent')
            ->with(['seance.module', 'justification'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($presence) {
                return $presence->created_at->format('Y-m');
            });

        return view('etudiant.absences', compact('absences'));
    }

    public function statistiques()
    {
        $etudiant = Auth::user();

        // Calcul du taux de présence global
        $totalPresences = $etudiant->presencesEtudiant()->count();
        $presents = $etudiant->presencesEtudiant()->where('statut', 'present')->count();
        $tauxGlobal = $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 100;

        // Calcul par module
        $statsModules = [];
        foreach ($etudiant->presencesEtudiant()->with('seance.module')->get() as $presence) {
            $moduleId = $presence->seance->module->id;
            if (!isset($statsModules[$moduleId])) {
                $statsModules[$moduleId] = [
                    'module' => $presence->seance->module->nom,
                    'total' => 0,
                    'present' => 0
                ];
            }

            $statsModules[$moduleId]['total']++;
            if ($presence->statut === 'present') {
                $statsModules[$moduleId]['present']++;
            }
        }

        // Calcul des notes d'assiduité
        foreach ($statsModules as &$module) {
            $module['taux'] = $module['total'] > 0 ? round(($module['present'] / $module['total']) * 100, 2) : 100;
            $module['note'] = min(20, ($module['present'] / $module['total']) * 20);
        }

        return view('etudiant.statistiques', compact('tauxGlobal', 'statsModules'));
    }
}
