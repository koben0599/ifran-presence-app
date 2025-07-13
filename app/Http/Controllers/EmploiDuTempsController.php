<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmploiDuTemps;
use App\Models\Module;
use App\Models\Classe;
use App\Models\Seance;
use App\Models\User;
use Carbon\Carbon;

class EmploiDuTempsController extends Controller
{
    /**
     * Affiche la liste des emplois du temps pour les coordinateurs/admins
     */
    public function index()
    {
        $emploisDuTemps = EmploiDuTemps::with(['module', 'enseignant', 'classe'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('classe_id');

        $classes = Classe::all();
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();

        return view('admin.emplois.index', compact('emploisDuTemps', 'classes', 'modules', 'enseignants'));
    }

    /**
     * Affiche le formulaire de création d'emploi du temps
     */
    public function create()
    {
        $classes = Classe::all();
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();
        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

        return view('admin.emplois.create', compact('classes', 'modules', 'enseignants', 'joursSemaine'));
    }

    /**
     * Enregistre un nouvel emploi du temps
     */
    public function store(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:users,id',
            'jour_semaine' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:presentiel,elearning,workshop',
            'salle' => 'required|string|max:255',
        ]);

        EmploiDuTemps::create($request->all());

        return redirect()->route('emplois.index')->with('success', 'Emploi du temps créé avec succès !');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(EmploiDuTemps $emploi)
    {
        $classes = Classe::all();
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();
        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

        return view('admin.emplois.edit', compact('emploi', 'classes', 'modules', 'enseignants', 'joursSemaine'));
    }

    /**
     * Met à jour un emploi du temps
     */
    public function update(Request $request, EmploiDuTemps $emploi)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:users,id',
            'jour_semaine' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:presentiel,elearning,workshop',
            'salle' => 'required|string|max:255',
        ]);

        $emploi->update($request->all());

        return redirect()->route('emplois.index')->with('success', 'Emploi du temps mis à jour avec succès !');
    }

    /**
     * Supprime un emploi du temps
     */
    public function destroy(EmploiDuTemps $emploi)
    {
        $emploi->delete();

        return redirect()->route('emplois.index')->with('success', 'Emploi du temps supprimé avec succès !');
    }

    /**
     * Génère les séances pour la semaine suivante (vendredi au dimanche)
     */
    public function genererSeancesSemaine(Request $request)
    {
        $dateSemaine = $request->input('date_semaine') ? Carbon::parse($request->input('date_semaine')) : Carbon::now();
        
        try {
            \Artisan::call('seances:generer', ['--semaine' => $dateSemaine->format('Y-m-d')]);
            
            return redirect()->back()->with('success', 'Séances générées avec succès pour la semaine du ' . $dateSemaine->startOfWeek()->format('d/m/Y') . ' au ' . $dateSemaine->endOfWeek()->format('d/m/Y'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération des séances : ' . $e->getMessage());
        }
    }

    /**
     * Affiche l'emploi du temps public pour les étudiants
     */
    public function emploiDuTempsPublic($classeId = null)
    {
        $classes = Classe::all();
        $classeSelectionnee = $classeId ? Classe::find($classeId) : $classes->first();

        if ($classeSelectionnee) {
            $emploisDuTemps = EmploiDuTemps::with(['module', 'enseignant'])
                ->where('classe_id', $classeSelectionnee->id)
                ->where('est_actif', true)
                ->orderByRaw("FIELD(jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi')")
                ->orderBy('heure_debut')
                ->get()
                ->groupBy('jour_semaine');
        } else {
            $emploisDuTemps = collect();
        }

        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

        return view('emplois.public', compact('emploisDuTemps', 'classes', 'classeSelectionnee', 'joursSemaine'));
    }

    /**
     * Affiche l'emploi du temps de l'enseignant connecté
     */
    public function emploiDuTempsEnseignant()
    {
        $enseignant = auth()->user();
        
        // Récupérer l'emploi du temps de l'enseignant
        $emploisDuTemps = EmploiDuTemps::with(['module', 'classe'])
            ->where('enseignant_id', $enseignant->id)
            ->where('est_actif', true)
            ->orderByRaw("FIELD(jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi')")
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour_semaine');

        // Organiser par jour de la semaine
        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        $emploiOrganise = [];
        
        foreach ($joursSemaine as $jour) {
            $emploiOrganise[$jour] = $emploisDuTemps->get($jour, collect());
        }

        return view('enseignant.emploi-du-temps', compact('emploiOrganise', 'joursSemaine'));
    }

    /**
     * Affiche le planning de la semaine courante
     */
    public function planningSemaine()
    {
        $enseignant = auth()->user();
        
        // Déterminer la semaine courante
        $lundi = now()->startOfWeek(Carbon::MONDAY);
        $vendredi = $lundi->copy()->endOfWeek(Carbon::FRIDAY);
        
        // Récupérer les séances de la semaine
        $seances = Seance::with(['module', 'classe'])
            ->where('enseignant_id', $enseignant->id)
            ->whereBetween('date_debut', [$lundi, $vendredi])
            ->orderBy('date_debut')
            ->get()
            ->groupBy(function($seance) {
                // Correction : s'assurer que date_debut est un objet Carbon
                return ($seance->date_debut instanceof \Carbon\Carbon ? $seance->date_debut : \Carbon\Carbon::parse($seance->date_debut))->format('l');
            });

        // Organiser par jour
        $joursSemaine = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $planningOrganise = [];
        
        foreach ($joursSemaine as $jour) {
            $planningOrganise[$jour] = $seances->get($jour, collect());
        }

        return view('enseignant.planning-semaine', compact('planningOrganise', 'joursSemaine', 'lundi', 'vendredi'));
    }

    /**
     * Génère les séances pour la semaine courante
     */
    public function genererSeances()
    {
        try {
            \Artisan::call('seances:generer');
            $output = \Artisan::output();
            
            return redirect()->back()->with('success', 'Séances générées avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération des séances : ' . $e->getMessage());
        }
    }
}
