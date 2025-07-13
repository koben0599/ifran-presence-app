<?php

namespace App\Services;

use App\Models\EmploiDuTemps;
use App\Models\Seance;
use App\Models\Classe;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeanceGenerationService
{
    /**
     * Générer les séances pour une semaine donnée
     */
    public function genererSeancesSemaine(Carbon $dateSemaine = null): array
    {
        $dateSemaine = $dateSemaine ?? Carbon::now();
        $lundi = $dateSemaine->copy()->startOfWeek(Carbon::MONDAY);
        $vendredi = $lundi->copy()->endOfWeek(Carbon::FRIDAY);

        $emploisDuTemps = EmploiDuTemps::with(['classe', 'module', 'enseignant'])
            ->where('est_actif', true)
            ->get();

        $seancesCreees = 0;
        $seancesExistentes = 0;
        $erreurs = [];

        foreach ($emploisDuTemps as $emploi) {
            try {
                $resultat = $this->genererSeancesPourEmploi($emploi, $lundi, $vendredi);
                $seancesCreees += $resultat['creees'];
                $seancesExistentes += $resultat['existantes'];
            } catch (\Exception $e) {
                $erreurs[] = "Erreur pour l'emploi {$emploi->id}: " . $e->getMessage();
                Log::error("Erreur génération séance", [
                    'emploi_id' => $emploi->id,
                    'erreur' => $e->getMessage()
                ]);
            }
        }

        return [
            'seances_creees' => $seancesCreees,
            'seances_existantes' => $seancesExistentes,
            'erreurs' => $erreurs,
            'periode' => $lundi->format('d/m/Y') . ' - ' . $vendredi->format('d/m/Y')
        ];
    }

    /**
     * Générer les séances pour un emploi du temps spécifique
     */
    private function genererSeancesPourEmploi(EmploiDuTemps $emploi, Carbon $lundi, Carbon $vendredi): array
    {
        $seancesCreees = 0;
        $seancesExistentes = 0;

        // Convertir le jour de la semaine en anglais pour Carbon
        $jourAnglais = $this->convertirJourFrancaisVersAnglais($emploi->jour_semaine);
        
        // Trouver la date du jour de la semaine dans la période
        $dateSeance = $lundi->copy()->next($jourAnglais);
        
        // Si la date est après vendredi, on prend le lundi suivant
        if ($dateSeance->gt($vendredi)) {
            $dateSeance = $lundi->copy()->next($jourAnglais);
        }

        // Vérifier si une séance existe déjà pour cette date et cet emploi
        $seanceExistante = Seance::where('classe_id', $emploi->classe_id)
            ->where('module_id', $emploi->module_id)
            ->where('enseignant_id', $emploi->enseignant_id)
            ->whereDate('date_debut', $dateSeance)
            ->where('heure_debut', $emploi->heure_debut)
            ->first();

        if ($seanceExistante) {
            $seancesExistentes++;
            return ['creees' => 0, 'existantes' => 1];
        }

        // Créer la nouvelle séance
        $dateDebut = $dateSeance->copy()->setTimeFromTimeString($emploi->heure_debut);
        $dateFin = $dateSeance->copy()->setTimeFromTimeString($emploi->heure_fin);

        Seance::create([
            'classe_id' => $emploi->classe_id,
            'module_id' => $emploi->module_id,
            'enseignant_id' => $emploi->enseignant_id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'type' => $emploi->type,
            'salle' => $emploi->salle,
        ]);

        $seancesCreees++;
        
        return ['creees' => 1, 'existantes' => 0];
    }

    /**
     * Convertir un jour français vers anglais pour Carbon
     */
    private function convertirJourFrancaisVersAnglais(string $jourFrancais): string
    {
        $conversion = [
            'lundi' => Carbon::MONDAY,
            'mardi' => Carbon::TUESDAY,
            'mercredi' => Carbon::WEDNESDAY,
            'jeudi' => Carbon::THURSDAY,
            'vendredi' => Carbon::FRIDAY,
            'samedi' => Carbon::SATURDAY,
            'dimanche' => Carbon::SUNDAY,
        ];

        return $conversion[strtolower($jourFrancais)] ?? Carbon::MONDAY;
    }

    /**
     * Nettoyer les séances passées (optionnel)
     */
    public function nettoyerSeancesPassees(): int
    {
        $seancesSupprimees = Seance::where('date_debut', '<', Carbon::now()->subDays(30))
            ->whereDoesntHave('presences')
            ->delete();

        return $seancesSupprimees;
    }

    /**
     * Vérifier les conflits d'emploi du temps
     */
    public function verifierConflits(): array
    {
        $conflits = [];

        $emploisDuTemps = EmploiDuTemps::with(['classe', 'enseignant'])
            ->where('est_actif', true)
            ->get()
            ->groupBy(['classe_id', 'jour_semaine']);

        foreach ($emploisDuTemps as $classeId => $emploisParJour) {
            foreach ($emploisParJour as $jour => $emplois) {
                if ($emplois->count() > 1) {
                    // Vérifier les chevauchements d'horaires
                    $emplois = $emplois->sortBy('heure_debut');
                    
                    for ($i = 0; $i < $emplois->count() - 1; $i++) {
                        $emploi1 = $emplois[$i];
                        $emploi2 = $emplois[$i + 1];
                        
                        if ($this->horairesSeChevauchent($emploi1, $emploi2)) {
                            $conflits[] = [
                                'type' => 'chevauchement',
                                'classe' => $emploi1->classe->nom,
                                'jour' => $jour,
                                'emploi1' => $emploi1,
                                'emploi2' => $emploi2,
                                'message' => "Chevauchement d'horaires détecté"
                            ];
                        }
                    }
                }
            }
        }

        return $conflits;
    }

    /**
     * Vérifier si deux horaires se chevauchent
     */
    private function horairesSeChevauchent(EmploiDuTemps $emploi1, EmploiDuTemps $emploi2): bool
    {
        $debut1 = Carbon::parse($emploi1->heure_debut);
        $fin1 = Carbon::parse($emploi1->heure_fin);
        $debut2 = Carbon::parse($emploi2->heure_debut);
        $fin2 = Carbon::parse($emploi2->heure_fin);

        return $debut1 < $fin2 && $debut2 < $fin1;
    }

    /**
     * Obtenir les statistiques de génération
     */
    public function getStatistiquesGeneration(): array
    {
        $semaineActuelle = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $semaineProchaine = $semaineActuelle->copy()->addWeek();

        return [
            'seances_semaine_actuelle' => Seance::whereBetween('date_debut', [
                $semaineActuelle, 
                $semaineActuelle->copy()->endOfWeek()
            ])->count(),
            'seances_semaine_prochaine' => Seance::whereBetween('date_debut', [
                $semaineProchaine, 
                $semaineProchaine->copy()->endOfWeek()
            ])->count(),
            'emplois_actifs' => EmploiDuTemps::where('est_actif', true)->count(),
            'derniere_generation' => Seance::max('created_at'),
        ];
    }
} 