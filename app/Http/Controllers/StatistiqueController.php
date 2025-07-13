<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\Classe;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isCoordinateur() || $user->isEnseignant()) {
            return $this->statistiquesPedagogiques();
        } elseif ($user->isEtudiant()) {
            return $this->statistiquesEtudiant();
        } elseif ($user->isParent()) {
            return $this->statistiquesParent();
        }
        
        return redirect('/');
    }

    public function statistiquesAvancees()
    {
        $user = Auth::user();
        
        if ($user->isEtudiant()) {
            return $this->statistiquesEtudiantAvancees();
        } elseif ($user->isParent()) {
            return $this->statistiquesParentAvancees();
        }
        
        return redirect('/');
    }

    private function statistiquesPedagogiques()
    {
        // Données pour le graphe de présence par étudiant
        $etudiants = User::where('role', 'etudiant')->get();
        $presenceParEtudiant = [];
        
        foreach ($etudiants as $etudiant) {
            $totalPresences = $etudiant->presencesEtudiant()->count();
            $presents = $etudiant->presencesEtudiant()->where('statut', 'present')->count();
            $taux = $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 1) : 0;
            
            $presenceParEtudiant[] = [
                'nom' => $etudiant->nom . ' ' . $etudiant->prenom,
                'taux' => $taux,
                'classe' => $etudiant->classe ?? 'N/A'
            ];
        }
        
        // Trier par taux décroissant
        usort($presenceParEtudiant, function($a, $b) {
            return $b['taux'] <=> $a['taux'];
        });

        // Données pour le graphe de présence par classe
        $classes = Classe::all();
        $presenceParClasse = [];
        
        foreach ($classes as $classe) {
            $etudiantsClasse = User::where('role', 'etudiant')->where('classe_id', $classe->id)->get();
            $totalTaux = 0;
            $count = 0;
            
            foreach ($etudiantsClasse as $etudiant) {
                $totalPresences = $etudiant->presencesEtudiant()->count();
                $presents = $etudiant->presencesEtudiant()->where('statut', 'present')->count();
                if ($totalPresences > 0) {
                    $totalTaux += ($presents / $totalPresences) * 100;
                    $count++;
                }
            }
            
            $presenceParClasse[] = [
                'classe' => $classe->nom,
                'taux' => $count > 0 ? round($totalTaux / $count, 1) : 0
            ];
        }

        // Données pour le graphe de volume de cours par type
        $seances = Seance::all();
        $volumeCours = [
            'presentiel' => 0,
            'e-learning' => 0,
            'workshop' => 0
        ];
        
        foreach ($seances as $seance) {
            $type = $seance->type_cours ?? 'presentiel';
            if (isset($volumeCours[$type])) {
                $volumeCours[$type]++;
            }
        }

        return view('admin.statistiques', compact('presenceParEtudiant', 'presenceParClasse', 'volumeCours'));
    }

    private function statistiquesEtudiant()
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

    private function statistiquesEtudiantAvancees()
    {
        $etudiant = Auth::user();
        
        // Taux de présence par séance sur une semaine
        $semaineActuelle = Carbon::now()->startOfWeek();
        $seancesSemaine = Seance::where('date_debut', '>=', $semaineActuelle)
            ->where('date_debut', '<=', $semaineActuelle->copy()->endOfWeek())
            ->get();
            
        $presenceSemaine = [];
        foreach ($seancesSemaine as $seance) {
            $presence = $etudiant->presencesEtudiant()
                ->where('seance_id', $seance->id)
                ->first();
                
            $presenceSemaine[] = [
                'date' => $seance->date_debut->format('d/m'),
                'module' => $seance->module->nom ?? 'N/A',
                'statut' => $presence ? $presence->statut : 'non_enregistre',
                'heure' => $seance->date_debut->format('H:i')
            ];
        }

        // Taux d'assiduité par trimestre
        $trimestres = $this->getTrimestres();
        $assiduiteTrimestre = [];
        
        foreach ($trimestres as $trimestre) {
            $totalPresences = $etudiant->presencesEtudiant()
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $presents = $etudiant->presencesEtudiant()
                ->where('statut', 'present')
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $assiduiteTrimestre[] = [
                'trimestre' => $trimestre['nom'],
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 1) : 0
            ];
        }

        // Taux d'assiduité de la classe par trimestre
        $classe = $etudiant->classe;
        $assiduiteClasseTrimestre = [];
        
        if ($classe) {
            foreach ($trimestres as $trimestre) {
                $etudiantsClasse = User::where('role', 'etudiant')->where('classe_id', $classe->id)->get();
                $totalTaux = 0;
                $count = 0;
                
                foreach ($etudiantsClasse as $etudiantClasse) {
                    $totalPresences = $etudiantClasse->presencesEtudiant()
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    $presents = $etudiantClasse->presencesEtudiant()
                        ->where('statut', 'present')
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    if ($totalPresences > 0) {
                        $totalTaux += ($presents / $totalPresences) * 100;
                        $count++;
                    }
                }
                
                $assiduiteClasseTrimestre[] = [
                    'trimestre' => $trimestre['nom'],
                    'taux' => $count > 0 ? round($totalTaux / $count, 1) : 0
                ];
            }
        }

        return view('etudiant.statistiques-avancees', compact('presenceSemaine', 'assiduiteTrimestre', 'assiduiteClasseTrimestre'));
    }

    private function statistiquesParentAvancees()
    {
        $parent = Auth::user();
        $enfants = $parent->enfants;
        
        if ($enfants->isEmpty()) {
            return view('parent.statistiques', ['message' => 'Aucun enfant associé à votre compte']);
        }
        
        // Prendre le premier enfant pour l'exemple
        $etudiant = $enfants->first();
        
        // Même logique que pour l'étudiant
        $semaineActuelle = Carbon::now()->startOfWeek();
        $seancesSemaine = Seance::where('date_debut', '>=', $semaineActuelle)
            ->where('date_debut', '<=', $semaineActuelle->copy()->endOfWeek())
            ->get();
            
        $presenceSemaine = [];
        foreach ($seancesSemaine as $seance) {
            $presence = $etudiant->presencesEtudiant()
                ->where('seance_id', $seance->id)
                ->first();
                
            $presenceSemaine[] = [
                'date' => $seance->date_debut->format('d/m'),
                'module' => $seance->module->nom ?? 'N/A',
                'statut' => $presence ? $presence->statut : 'non_enregistre',
                'heure' => $seance->date_debut->format('H:i')
            ];
        }

        $trimestres = $this->getTrimestres();
        $assiduiteTrimestre = [];
        
        foreach ($trimestres as $trimestre) {
            $totalPresences = $etudiant->presencesEtudiant()
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $presents = $etudiant->presencesEtudiant()
                ->where('statut', 'present')
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $assiduiteTrimestre[] = [
                'trimestre' => $trimestre['nom'],
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 1) : 0
            ];
        }

        $classe = $etudiant->classe;
        $assiduiteClasseTrimestre = [];
        
        if ($classe) {
            foreach ($trimestres as $trimestre) {
                $etudiantsClasse = User::where('role', 'etudiant')->where('classe_id', $classe->id)->get();
                $totalTaux = 0;
                $count = 0;
                
                foreach ($etudiantsClasse as $etudiantClasse) {
                    $totalPresences = $etudiantClasse->presencesEtudiant()
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    $presents = $etudiantClasse->presencesEtudiant()
                        ->where('statut', 'present')
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    if ($totalPresences > 0) {
                        $totalTaux += ($presents / $totalPresences) * 100;
                        $count++;
                    }
                }
                
                $assiduiteClasseTrimestre[] = [
                    'trimestre' => $trimestre['nom'],
                    'taux' => $count > 0 ? round($totalTaux / $count, 1) : 0
                ];
            }
        }

        return view('parent.statistiques', compact('presenceSemaine', 'assiduiteTrimestre', 'assiduiteClasseTrimestre', 'etudiant'));
    }

    private function statistiquesParent()
    {
        $parent = Auth::user();
        $enfants = $parent->enfants;
        
        if ($enfants->isEmpty()) {
            return view('parent.statistiques', ['message' => 'Aucun enfant associé à votre compte']);
        }
        
        // Prendre le premier enfant pour l'exemple
        $etudiant = $enfants->first();
        
        // Même logique que pour l'étudiant
        $semaineActuelle = Carbon::now()->startOfWeek();
        $seancesSemaine = Seance::where('date_debut', '>=', $semaineActuelle)
            ->where('date_debut', '<=', $semaineActuelle->copy()->endOfWeek())
            ->get();
            
        $presenceSemaine = [];
        foreach ($seancesSemaine as $seance) {
            $presence = $etudiant->presencesEtudiant()
                ->where('seance_id', $seance->id)
                ->first();
                
            $presenceSemaine[] = [
                'date' => $seance->date_debut->format('d/m'),
                'module' => $seance->module->nom ?? 'N/A',
                'statut' => $presence ? $presence->statut : 'non_enregistre',
                'heure' => $seance->date_debut->format('H:i')
            ];
        }

        $trimestres = $this->getTrimestres();
        $assiduiteTrimestre = [];
        
        foreach ($trimestres as $trimestre) {
            $totalPresences = $etudiant->presencesEtudiant()
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $presents = $etudiant->presencesEtudiant()
                ->where('statut', 'present')
                ->whereHas('seance', function($query) use ($trimestre) {
                    $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                })->count();
                
            $assiduiteTrimestre[] = [
                'trimestre' => $trimestre['nom'],
                'taux' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 1) : 0
            ];
        }

        $classe = $etudiant->classe;
        $assiduiteClasseTrimestre = [];
        
        if ($classe) {
            foreach ($trimestres as $trimestre) {
                $etudiantsClasse = User::where('role', 'etudiant')->where('classe_id', $classe->id)->get();
                $totalTaux = 0;
                $count = 0;
                
                foreach ($etudiantsClasse as $etudiantClasse) {
                    $totalPresences = $etudiantClasse->presencesEtudiant()
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    $presents = $etudiantClasse->presencesEtudiant()
                        ->where('statut', 'present')
                        ->whereHas('seance', function($query) use ($trimestre) {
                            $query->whereBetween('date_debut', [$trimestre['debut'], $trimestre['fin']]);
                        })->count();
                        
                    if ($totalPresences > 0) {
                        $totalTaux += ($presents / $totalPresences) * 100;
                        $count++;
                    }
                }
                
                $assiduiteClasseTrimestre[] = [
                    'trimestre' => $trimestre['nom'],
                    'taux' => $count > 0 ? round($totalTaux / $count, 1) : 0
                ];
            }
        }

        return view('parent.statistiques', compact('presenceSemaine', 'assiduiteTrimestre', 'assiduiteClasseTrimestre', 'etudiant'));
    }

    private function getTrimestres()
    {
        $annee = Carbon::now()->year;
        return [
            [
                'nom' => 'T1',
                'debut' => Carbon::create($annee, 9, 1),
                'fin' => Carbon::create($annee, 12, 31)
            ],
            [
                'nom' => 'T2', 
                'debut' => Carbon::create($annee + 1, 1, 1),
                'fin' => Carbon::create($annee + 1, 3, 31)
            ],
            [
                'nom' => 'T3',
                'debut' => Carbon::create($annee + 1, 4, 1),
                'fin' => Carbon::create($annee + 1, 6, 30)
            ]
        ];
    }
}
