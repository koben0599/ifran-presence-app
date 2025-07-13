<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmploiDuTemps;
use App\Models\Seance;
use App\Models\Presence;
use App\Models\Module;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CoordinateurController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('coordinateur');
    }

    /**
     * Afficher la liste des emplois du temps
     */
    public function index()
    {
        $emplois = EmploiDuTemps::with(['module', 'enseignant', 'classe'])->get();
        return view('coordinateur.index', compact('emplois'));
    }

    /**
     * Afficher le formulaire de création d'emploi du temps
     */
    public function create()
    {
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();
        $classes = Classe::all();
        
        return view('coordinateur.create', compact('modules', 'enseignants', 'classes'));
    }

    /**
     * Créer un nouvel emploi du temps
     */
    public function store(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:users,id',
            'classe_id' => 'required|exists:classes,id',
            'jour' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:learning,workshop',
        ]);

        EmploiDuTemps::create($request->all());

        return redirect()->route('coordinateur.index')
            ->with('success', 'Emploi du temps créé avec succès.');
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(EmploiDuTemps $emploi)
    {
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();
        $classes = Classe::all();
        
        return view('coordinateur.edit', compact('emploi', 'modules', 'enseignants', 'classes'));
    }

    /**
     * Modifier un emploi du temps
     */
    public function update(Request $request, EmploiDuTemps $emploi)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:users,id',
            'classe_id' => 'required|exists:classes,id',
            'jour' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:learning,workshop',
        ]);

        $emploi->update($request->all());

        return redirect()->route('coordinateur.index')
            ->with('success', 'Emploi du temps modifié avec succès.');
    }

    /**
     * Annuler un emploi du temps
     */
    public function annuler(EmploiDuTemps $emploi)
    {
        $emploi->update(['statut' => 'annule']);
        
        return redirect()->route('coordinateur.index')
            ->with('success', 'Emploi du temps annulé avec succès.');
    }

    /**
     * Reporter un emploi du temps
     */
    public function reporter(Request $request, EmploiDuTemps $emploi)
    {
        $request->validate([
            'nouvelle_date' => 'required|date|after:today',
            'nouvelle_heure_debut' => 'required|date_format:H:i',
            'nouvelle_heure_fin' => 'required|date_format:H:i|after:nouvelle_heure_debut',
        ]);

        // Créer une nouvelle séance reportée
        Seance::create([
            'emploi_du_temps_id' => $emploi->id,
            'date' => $request->nouvelle_date,
            'heure_debut' => $request->nouvelle_heure_debut,
            'heure_fin' => $request->nouvelle_heure_fin,
            'statut' => 'reporte',
        ]);

        return redirect()->route('coordinateur.index')
            ->with('success', 'Séance reportée avec succès.');
    }

    /**
     * Afficher la liste des absences
     */
    public function absences()
    {
        $absences = Presence::with(['etudiant', 'seance.module'])
            ->where('statut', 'absent')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('coordinateur.absences', compact('absences'));
    }

    /**
     * Saisir les présences pour les séances Learning
     */
    public function saisirPresencesLearning()
    {
        $seances = Seance::with(['emploiDuTemps.module', 'emploiDuTemps.classe.etudiants'])
            ->whereHas('emploiDuTemps', function($query) {
                $query->where('type', 'learning');
            })
            ->where('date', '>=', now()->startOfWeek())
            ->where('date', '<=', now()->endOfWeek())
            ->get();
            
        return view('coordinateur.saisir-presences-learning', compact('seances'));
    }

    /**
     * Saisir les présences pour les séances Workshop
     */
    public function saisirPresencesWorkshop()
    {
        $seances = Seance::with(['emploiDuTemps.module', 'emploiDuTemps.classe.etudiants'])
            ->whereHas('emploiDuTemps', function($query) {
                $query->where('type', 'workshop');
            })
            ->where('date', '>=', now()->startOfWeek())
            ->where('date', '<=', now()->endOfWeek())
            ->get();
            
        return view('coordinateur.saisir-presences-workshop', compact('seances'));
    }

    /**
     * Enregistrer les présences
     */
    public function enregistrerPresences(Request $request)
    {
        $request->validate([
            'seance_id' => 'required|exists:seances,id',
            'presences' => 'required|array',
            'presences.*.etudiant_id' => 'required|exists:users,id',
            'presences.*.statut' => 'required|in:present,absent,retard',
        ]);

        foreach ($request->presences as $presenceData) {
            Presence::updateOrCreate(
                [
                    'seance_id' => $request->seance_id,
                    'etudiant_id' => $presenceData['etudiant_id'],
                ],
                [
                    'statut' => $presenceData['statut'],
                    'coordinateur_id' => Auth::id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Présences enregistrées avec succès.');
    }

    /**
     * Justifier une absence
     */
    public function justifierAbsence(Request $request, Presence $presence)
    {
        $request->validate([
            'motif' => 'required|string|max:500',
            'piece_jointe' => 'nullable|file|max:2048',
        ]);

        $presence->update([
            'justifie' => true,
            'motif_justification' => $request->motif,
            'coordinateur_id' => Auth::id(),
        ]);

        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('justifications', 'public');
            $presence->update(['piece_jointe' => $path]);
        }

        return redirect()->route('coordinateur.absences')
            ->with('success', 'Absence justifiée avec succès.');
    }

    /**
     * Statistiques des présences
     */
    public function statistiques()
    {
        $stats = [
            'total_seances' => Seance::count(),
            'seances_this_week' => Seance::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'taux_presence' => $this->calculerTauxPresence(),
            'absences_non_justifiees' => Presence::where('statut', 'absent')->where('justifie', false)->count(),
        ];

        return view('coordinateur.statistiques', compact('stats'));
    }

    /**
     * Calculer le taux de présence global
     */
    private function calculerTauxPresence()
    {
        $total = Presence::count();
        if ($total === 0) return 0;
        
        $present = Presence::where('statut', 'present')->count();
        return round(($present / $total) * 100, 2);
    }
}
