<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classe;
use App\Models\Module;
use App\Models\Seance;
use App\Models\Presence;
use App\Models\EmploiDuTemps;
use App\Services\StatistiqueService;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class RapportController extends Controller
{
    protected $statistiqueService;
    protected $alerteService;

    public function __construct(StatistiqueService $statistiqueService, AlerteService $alerteService)
    {
        $this->statistiqueService = $statistiqueService;
        $this->alerteService = $alerteService;
    }

    /**
     * Afficher la page des rapports
     */
    public function index()
    {
        $user = auth()->user();
        
        $rapports = [
            'presence' => [
                'titre' => 'Rapport de présence',
                'description' => 'Statistiques détaillées des présences',
                'icon' => 'fas fa-user-check',
                'roles' => ['admin', 'coordinateur', 'enseignant']
            ],
            'classe' => [
                'titre' => 'Rapport par classe',
                'description' => 'Analyse des performances par classe',
                'icon' => 'fas fa-users',
                'roles' => ['admin', 'coordinateur']
            ],
            'module' => [
                'titre' => 'Rapport par module',
                'description' => 'Statistiques par module et enseignant',
                'icon' => 'fas fa-book',
                'roles' => ['admin', 'coordinateur', 'enseignant']
            ],
            'etudiant' => [
                'titre' => 'Rapport étudiant',
                'description' => 'Suivi individuel des étudiants',
                'icon' => 'fas fa-user-graduate',
                'roles' => ['admin', 'coordinateur', 'enseignant']
            ],
            'alerte' => [
                'titre' => 'Rapport d\'alertes',
                'description' => 'Synthèse des alertes et problèmes',
                'icon' => 'fas fa-exclamation-triangle',
                'roles' => ['admin', 'coordinateur']
            ],
            'evolution' => [
                'titre' => 'Rapport d\'évolution',
                'description' => 'Évolution des présences dans le temps',
                'icon' => 'fas fa-chart-line',
                'roles' => ['admin', 'coordinateur']
            ]
        ];

        // Filtrer les rapports selon le rôle
        $rapports = array_filter($rapports, function($rapport) use ($user) {
            return in_array($user->role, $rapport['roles']);
        });

        return view('rapports.index', compact('rapports'));
    }

    /**
     * Générer un rapport de présence
     */
    public function rapportPresence(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'classe_id' => 'nullable|exists:classes,id',
            'module_id' => 'nullable|exists:modules,id',
            'format' => 'in:html,pdf,excel'
        ]);

        $dateDebut = Carbon::parse($request->date_debut);
        $dateFin = Carbon::parse($request->date_fin);

        $query = Seance::with(['classe', 'module', 'enseignant', 'presences.etudiant'])
            ->whereBetween('date_debut', [$dateDebut, $dateFin]);

        if ($request->classe_id) {
            $query->where('classe_id', $request->classe_id);
        }

        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }

        $seances = $query->get();

        $statistiques = $this->calculerStatistiquesPresence($seances);

        $data = [
            'seances' => $seances,
            'statistiques' => $statistiques,
            'periode' => $dateDebut->format('d/m/Y') . ' - ' . $dateFin->format('d/m/Y'),
            'filtres' => $request->only(['classe_id', 'module_id'])
        ];

        if ($request->format === 'pdf') {
            return $this->genererPDF('rapports.presence', $data, 'rapport_presence.pdf');
        }

        if ($request->format === 'excel') {
            return $this->genererExcel($data, 'rapport_presence.xlsx');
        }

        return view('rapports.presence', $data);
    }

    /**
     * Générer un rapport par classe
     */
    public function rapportClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'in:semaine,mois,trimestre,annee',
            'format' => 'in:html,pdf,excel'
        ]);

        $classe = Classe::with(['etudiants', 'emploisDuTemps.module'])->findOrFail($request->classe_id);
        
        $periode = $request->periode ?? 'mois';
        $dateDebut = $this->getDateDebutPeriode($periode);
        $dateFin = Carbon::now();

        $seances = Seance::with(['presences.etudiant', 'module'])
            ->where('classe_id', $classe->id)
            ->whereBetween('date_debut', [$dateDebut, $dateFin])
            ->get();

        $statistiques = $this->calculerStatistiquesClasse($classe, $seances);

        $data = [
            'classe' => $classe,
            'seances' => $seances,
            'statistiques' => $statistiques,
            'periode' => $periode
        ];

        if ($request->format === 'pdf') {
            return $this->genererPDF('rapports.classe', $data, 'rapport_classe_' . $classe->nom . '.pdf');
        }

        if ($request->format === 'excel') {
            return $this->genererExcel($data, 'rapport_classe_' . $classe->nom . '.xlsx');
        }

        return view('rapports.classe', $data);
    }

    /**
     * Générer un rapport par module
     */
    public function rapportModule(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'nullable|exists:users,id',
            'format' => 'in:html,pdf,excel'
        ]);

        $module = Module::with(['seances.classe', 'seances.enseignant'])->findOrFail($request->module_id);
        
        $query = $module->seances()->with(['classe', 'enseignant', 'presences.etudiant']);

        if ($request->enseignant_id) {
            $query->where('enseignant_id', $request->enseignant_id);
        }

        $seances = $query->get();

        $statistiques = $this->calculerStatistiquesModule($module, $seances);

        $data = [
            'module' => $module,
            'seances' => $seances,
            'statistiques' => $statistiques,
            'enseignant_id' => $request->enseignant_id
        ];

        if ($request->format === 'pdf') {
            return $this->genererPDF('rapports.module', $data, 'rapport_module_' . $module->code . '.pdf');
        }

        if ($request->format === 'excel') {
            return $this->genererExcel($data, 'rapport_module_' . $module->code . '.xlsx');
        }

        return view('rapports.module', $data);
    }

    /**
     * Générer un rapport étudiant
     */
    public function rapportEtudiant(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'format' => 'in:html,pdf,excel'
        ]);

        $etudiant = User::with(['classe', 'presences.seance.module'])->findOrFail($request->etudiant_id);
        
        $presences = $etudiant->presences()
            ->with(['seance.module', 'seance.classe', 'justification'])
            ->orderBy('created_at', 'desc')
            ->get();

        $statistiques = $this->calculerStatistiquesEtudiant($etudiant, $presences);
        $alertes = $this->alerteService->getAlertesEtudiant($etudiant->id);

        $data = [
            'etudiant' => $etudiant,
            'presences' => $presences,
            'statistiques' => $statistiques,
            'alertes' => $alertes
        ];

        if ($request->format === 'pdf') {
            return $this->genererPDF('rapports.etudiant', $data, 'rapport_etudiant_' . $etudiant->nom . '.pdf');
        }

        if ($request->format === 'excel') {
            return $this->genererExcel($data, 'rapport_etudiant_' . $etudiant->nom . '.xlsx');
        }

        return view('rapports.etudiant', $data);
    }

    /**
     * Générer un rapport d'alertes
     */
    public function rapportAlertes(Request $request)
    {
        $request->validate([
            'type' => 'in:toutes,absences,retards,faible_taux',
            'format' => 'in:html,pdf,excel'
        ]);

        $user = auth()->user();
        $alertes = [];

        if ($user->role === 'admin') {
            $alertes = $this->alerteService->getAlertesGlobales();
        } elseif ($user->role === 'coordinateur') {
            $alertes = $this->alerteService->getAlertesClasse($user->classe_id);
        }

        $data = [
            'alertes' => $alertes,
            'type' => $request->type ?? 'toutes',
            'user' => $user
        ];

        if ($request->format === 'pdf') {
            return $this->genererPDF('rapports.alertes', $data, 'rapport_alertes.pdf');
        }

        if ($request->format === 'excel') {
            return $this->genererExcel($data, 'rapport_alertes.xlsx');
        }

        return view('rapports.alertes', $data);
    }

    /**
     * Calculer les statistiques de présence
     */
    private function calculerStatistiquesPresence($seances): array
    {
        $totalSeances = $seances->count();
        $totalPresences = 0;
        $presents = 0;
        $absents = 0;
        $retards = 0;

        foreach ($seances as $seance) {
            $totalPresences += $seance->presences->count();
            $presents += $seance->presences->where('statut', 'present')->count();
            $absents += $seance->presences->where('statut', 'absent')->count();
            $retards += $seance->presences->where('statut', 'retard')->count();
        }

        return [
            'total_seances' => $totalSeances,
            'total_presences' => $totalPresences,
            'presents' => $presents,
            'absents' => $absents,
            'retards' => $retards,
            'taux_presence' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            'taux_absence' => $totalPresences > 0 ? round(($absents / $totalPresences) * 100, 2) : 0,
            'taux_retard' => $totalPresences > 0 ? round(($retards / $totalPresences) * 100, 2) : 0,
        ];
    }

    /**
     * Calculer les statistiques par classe
     */
    private function calculerStatistiquesClasse($classe, $seances): array
    {
        $statistiques = $this->calculerStatistiquesPresence($seances);
        
        $statistiques['nombre_etudiants'] = $classe->etudiants->count();
        $statistiques['modules'] = $classe->emploisDuTemps->groupBy('module.nom')->count();
        $statistiques['heures_totales'] = $seances->sum(function($seance) {
            return Carbon::parse($seance->date_debut)->diffInMinutes(Carbon::parse($seance->date_fin)) / 60;
        });

        return $statistiques;
    }

    /**
     * Calculer les statistiques par module
     */
    private function calculerStatistiquesModule($module, $seances): array
    {
        $statistiques = $this->calculerStatistiquesPresence($seances);
        
        $statistiques['classes'] = $seances->pluck('classe.nom')->unique()->count();
        $statistiques['enseignants'] = $seances->pluck('enseignant.nom')->unique()->count();
        $statistiques['heures_totales'] = $seances->sum(function($seance) {
            return Carbon::parse($seance->date_debut)->diffInMinutes(Carbon::parse($seance->date_fin)) / 60;
        });

        return $statistiques;
    }

    /**
     * Calculer les statistiques étudiant
     */
    private function calculerStatistiquesEtudiant($etudiant, $presences): array
    {
        $totalPresences = $presences->count();
        $presents = $presences->where('statut', 'present')->count();
        $absents = $presences->where('statut', 'absent')->count();
        $retards = $presences->where('statut', 'retard')->count();
        $justifications = $presences->whereNotNull('justification_id')->count();

        return [
            'total_presences' => $totalPresences,
            'presents' => $presents,
            'absents' => $absents,
            'retards' => $retards,
            'justifications' => $justifications,
            'taux_presence' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0,
            'modules_suivis' => $presences->pluck('seance.module.nom')->unique()->count(),
            'seances_suivies' => $presences->pluck('seance_id')->unique()->count(),
        ];
    }

    /**
     * Obtenir la date de début selon la période
     */
    private function getDateDebutPeriode(string $periode): Carbon
    {
        return match($periode) {
            'semaine' => Carbon::now()->startOfWeek(),
            'mois' => Carbon::now()->startOfMonth(),
            'trimestre' => Carbon::now()->startOfQuarter(),
            'annee' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    /**
     * Générer un PDF
     */
    private function genererPDF(string $view, array $data, string $filename)
    {
        $pdf = PDF::loadView($view, $data);
        return $pdf->download($filename);
    }

    /**
     * Générer un fichier Excel
     */
    private function genererExcel(array $data, string $filename)
    {
        // Implémentation pour Excel (nécessite un package comme Maatwebsite/Excel)
        return response()->json(['message' => 'Export Excel non implémenté'], 501);
    }
} 