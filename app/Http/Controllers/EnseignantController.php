<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\User;

class EnseignantController extends Controller
{
    public function dashboard()
    {
        $enseignant = auth()->user();

        // Séances à venir
        $seances = $enseignant->seancesEnseignant()
            ->with(['module', 'classe'])
            ->whereBetween('date_debut', [now(), now()->addDays(14)])
            ->orderBy('date_debut')
            ->get();

        // Statistiques de présence
        $stats = [];
        $classes = $enseignant->seancesEnseignant()
            ->with('classe')
            ->get()
            ->pluck('classe.nom')
            ->unique();

        foreach ($classes as $classeNom) {
            $seancesClasse = $enseignant->seancesEnseignant()
                ->whereHas('classe', function($query) use ($classeNom) {
                    $query->where('nom', $classeNom);
                })
                ->pluck('id');
                
            $totalPresences = Presence::whereIn('seance_id', $seancesClasse)->count();
            $presents = Presence::whereIn('seance_id', $seancesClasse)
                ->where('statut', 'present')
                ->count();

            $stats[$classeNom] = [
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

    public function presences()
    {
        $enseignant = auth()->user();

        // Récupérer les séances présentiel récentes et à venir avec pagination
        $seances = $enseignant->seancesEnseignant()
            ->with(['module', 'classe'])
            ->where('type', 'presentiel')
            ->whereBetween('date_debut', [now()->subWeeks(4), now()->addWeeks(4)])
            ->orderBy('date_debut', 'desc')
            ->paginate(12);

        return view('enseignant.presences', compact('seances'));
    }

    public function seances()
    {
        $enseignant = auth()->user();

        // Récupérer toutes les séances de l'enseignant avec pagination
        $seances = $enseignant->seancesEnseignant()
            ->with(['module', 'classe'])
            ->orderBy('date_debut', 'desc')
            ->paginate(10);

        return view('enseignant.seances', compact('seances'));
    }

    public function statistiques()
    {
        $enseignant = auth()->user();

        // Statistiques de présence par classe
        $stats = [];
        $classes = $enseignant->seancesEnseignant()
            ->with('classe')
            ->get()
            ->pluck('classe.nom')
            ->unique();

        foreach ($classes as $classeNom) {
            $seancesClasse = $enseignant->seancesEnseignant()
                ->whereHas('classe', function($query) use ($classeNom) {
                    $query->where('nom', $classeNom);
                })
                ->pluck('id');
                
            $totalPresences = Presence::whereIn('seance_id', $seancesClasse)->count();
            $presents = Presence::whereIn('seance_id', $seancesClasse)
                ->where('statut', 'present')
                ->count();

            $stats[$classeNom] = [
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

        // Générer les couleurs dans le contrôleur
        $backgroundColors = array_map(function($taux) {
            if ($taux >= 80) return 'rgba(34, 197, 94, 0.8)';
            if ($taux >= 60) return 'rgba(245, 158, 11, 0.8)';
            return 'rgba(239, 68, 68, 0.8)';
        }, $chartData);

        $borderColors = array_map(function($taux) {
            if ($taux >= 80) return 'rgb(34, 197, 94)';
            if ($taux >= 60) return 'rgb(245, 158, 11)';
            return 'rgb(239, 68, 68)';
        }, $chartData);

        return view('enseignant.statistiques', compact('stats', 'chartClasses', 'chartData', 'backgroundColors', 'borderColors'));
    }

    public function saisirPresence(Seance $seance)
    {
        // Vérifier que l'enseignant connecté est bien l'enseignant de cette séance
        if (auth()->user()->id !== $seance->enseignant_id) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Accès non autorisé');
        }

        $etudiants = User::where('role', 'etudiant')
            ->where('classe_id', $seance->classe_id)
            ->get();
            
        return view('enseignant.saisie-presences', compact('seance', 'etudiants'));
    }

    public function enregistrerPresence(Request $request, Seance $seance)
    {
        // Vérifier que l'enseignant connecté est bien l'enseignant de cette séance
        if (auth()->user()->id !== $seance->enseignant_id) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Accès non autorisé');
        }

        foreach ($request->presences as $etudiant_id => $statut) {
            Presence::updateOrCreate(
                ['etudiant_id' => $etudiant_id, 'seance_id' => $seance->id],
                ['statut' => $statut]
            );
        }

        return redirect()->route('enseignant.dashboard')->with('success', 'Présences enregistrées avec succès');
    }
}
