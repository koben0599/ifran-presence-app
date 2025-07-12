<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\Etudiant;

class EnseignantController extends Controller
{
    public function dashboard()
    {
        $enseignant = auth()->user();

        // Séances à venir
        $seances = $enseignant->seancesEnseignant()
            ->whereBetween('date_debut', [now(), now()->addDays(14)])
            ->orderBy('date_debut')
            ->get();

        // Statistiques de présence
        $stats = [];
        $classes = $enseignant->seancesEnseignant()->distinct()->pluck('classe');

        foreach ($classes as $classe) {
            $seancesClasse = $enseignant->seancesEnseignant()->where('classe', $classe)->pluck('id');
            $totalPresences = Presence::whereIn('seance_id', $seancesClasse)->count();
            $presents = Presence::whereIn('seance_id', $seancesClasse)
                ->where('statut', 'present')
                ->count();

            $stats[$classe] = [
                'total' => $totalPresences,
                'present' => $presents,
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0
            ];
        }

        // Données pour les graphiques
        $chartClasses = $classes->toArray();
        $chartData = array_map(function($stat) {
            return $stat['taux'];
        }, $stats);

        return view('enseignant.dashboard', compact('seances', 'stats', 'chartClasses', 'chartData'));
    }

    public function saisirPresence(Seance $seance)
    {
        $etudiants = Etudiant::where('classe', $seance->classe)->get();
        return view('enseignant.saisie-presence', compact('seance', 'etudiants'));
    }

    public function enregistrerPresence(Request $request, Seance $seance)
    {
        foreach ($request->presences as $etudiant_id => $statut) {
            Presence::updateOrCreate(
                ['etudiant_id' => $etudiant_id, 'seance_id' => $seance->id],
                ['statut' => $statut]
            );
        }

        return redirect()->route('enseignant.dashboard')->with('success', 'Présences enregistrées avec succès');
    }
}
