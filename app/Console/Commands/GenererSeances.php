<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmploiDuTemps;
use App\Models\Seance;
use App\Models\Classe;
use Carbon\Carbon;

class GenererSeances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seances:generer {--semaine= : Semaine à générer (format: Y-m-d)} {--force : Forcer la régénération}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les séances à partir de l\'emploi du temps pour une semaine donnée';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateSemaine = $this->option('semaine') ? Carbon::parse($this->option('semaine')) : Carbon::now();
        $force = $this->option('force');

        // Déterminer le lundi de la semaine
        $lundi = $dateSemaine->copy()->startOfWeek(Carbon::MONDAY);
        $vendredi = $lundi->copy()->endOfWeek(Carbon::FRIDAY);

        $this->info("Génération des séances pour la semaine du {$lundi->format('d/m/Y')} au {$vendredi->format('d/m/Y')}");

        // Récupérer tous les emplois du temps actifs
        $emploisDuTemps = EmploiDuTemps::with(['module', 'enseignant', 'classe'])
            ->where('est_actif', true)
            ->get();

        if ($emploisDuTemps->isEmpty()) {
            $this->error('Aucun emploi du temps actif trouvé !');
            return 1;
        }

        $this->info("Trouvé {$emploisDuTemps->count()} emplois du temps actifs");

        $seancesCreees = 0;
        $seancesExistant = 0;

        // Parcourir chaque jour de la semaine (lundi à vendredi)
        for ($jour = 0; $jour < 5; $jour++) {
            $date = $lundi->copy()->addDays($jour);
            $jourSemaine = $this->getJourSemaine($date->dayOfWeek);

            $this->info("Traitement du {$jourSemaine} {$date->format('d/m/Y')}");

            // Récupérer les emplois du temps pour ce jour
            $emploisJour = $emploisDuTemps->where('jour_semaine', $jourSemaine);

            foreach ($emploisJour as $emploi) {
                // Vérifier si une séance existe déjà pour cette date et cet emploi
                $seanceExistante = Seance::where('module_id', $emploi->module_id)
                    ->where('enseignant_id', $emploi->enseignant_id)
                    ->where('classe_id', $emploi->classe_id)
                    ->whereDate('date_debut', $date)
                    ->first();

                if ($seanceExistante && !$force) {
                    $seancesExistant++;
                    $this->line("  - Séance existante pour {$emploi->module->nom} ({$emploi->classe->nom})");
                    continue;
                }

                // Créer la date de début et de fin
                $heureDebut = Carbon::parse($emploi->heure_debut);
                $heureFin = Carbon::parse($emploi->heure_fin);

                $dateDebut = $date->copy()->setTime($heureDebut->hour, $heureDebut->minute);
                $dateFin = $date->copy()->setTime($heureFin->hour, $heureFin->minute);

                // Créer ou mettre à jour la séance
                $seance = Seance::updateOrCreate(
                    [
                        'module_id' => $emploi->module_id,
                        'enseignant_id' => $emploi->enseignant_id,
                        'classe_id' => $emploi->classe_id,
                        'date_debut' => $dateDebut,
                    ],
                    [
                        'date_fin' => $dateFin,
                        'type' => $emploi->type,
                        'salle' => $emploi->salle,
                    ]
                );

                $seancesCreees++;
                $this->line("  ✓ Séance créée : {$emploi->module->nom} ({$emploi->classe->nom}) - {$dateDebut->format('H:i')}-{$dateFin->format('H:i')}");
            }
        }

        $this->info("\nRésumé :");
        $this->info("- Séances créées/mises à jour : {$seancesCreees}");
        $this->info("- Séances existantes (ignorées) : {$seancesExistant}");

        return 0;
    }

    /**
     * Convertit le jour de la semaine en français
     */
    private function getJourSemaine($dayOfWeek)
    {
        $jours = [
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
            0 => 'dimanche'
        ];

        return $jours[$dayOfWeek] ?? 'inconnu';
    }
}
