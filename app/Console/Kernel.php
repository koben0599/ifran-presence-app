<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\SeanceGenerationService;
use App\Services\AlerteService;
use App\Services\StatistiqueService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Génération automatique des séances (tous les dimanches à 18h)
        $schedule->call(function () {
            $service = app(SeanceGenerationService::class);
            $service->genererSeancesSemaine();
        })->weekly()->sundays()->at('18:00')
          ->appendOutputTo(storage_path('logs/seances-generation.log'));

        // Vérification des alertes (toutes les heures)
        $schedule->call(function () {
            $service = app(AlerteService::class);
            // Logique de vérification des alertes
        })->hourly()
          ->appendOutputTo(storage_path('logs/alertes.log'));

        // Nettoyage des anciennes séances (tous les jours à 2h)
        $schedule->call(function () {
            $service = app(SeanceGenerationService::class);
            $service->nettoyerSeancesPassees();
        })->daily()->at('02:00')
          ->appendOutputTo(storage_path('logs/nettoyage.log'));

        // Sauvegarde automatique (tous les jours à 3h)
        $schedule->command('backup:run')
                ->daily()->at('03:00')
                ->appendOutputTo(storage_path('logs/backup.log'));

        // Nettoyage des logs (tous les dimanches à 4h)
        $schedule->call(function () {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
                file_put_contents($logFile, '');
            }
        })->weekly()->sundays()->at('04:00');

        // Nettoyage des exports anciens (tous les jours à 5h)
        $schedule->call(function () {
            $retention = config('export.retention', 30);
            $cutoffDate = now()->subDays($retention);
            
            $exportsPath = storage_path('app/exports');
            if (is_dir($exportsPath)) {
                $files = glob($exportsPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoffDate->timestamp) {
                        unlink($file);
                    }
                }
            }
        })->daily()->at('05:00');

        // Nettoyage des notifications anciennes (tous les jours à 6h)
        $schedule->call(function () {
            $retention = config('notifications.retention_jours', 30);
            \App\Models\Notification::where('created_at', '<', now()->subDays($retention))
                ->delete();
        })->daily()->at('06:00');

        // Génération des statistiques quotidiennes (tous les jours à 7h)
        $schedule->call(function () {
            $service = app(StatistiqueService::class);
            // Logique de génération des statistiques
        })->daily()->at('07:00')
          ->appendOutputTo(storage_path('logs/statistiques.log'));

        // Vérification de l'intégrité de la base de données (tous les samedis à 8h)
        $schedule->call(function () {
            // Vérification des contraintes de clés étrangères
            \DB::statement('PRAGMA foreign_key_check');
        })->weekly()->saturdays()->at('08:00')
          ->appendOutputTo(storage_path('logs/integrity.log'));

        // Optimisation de la base de données (tous les samedis à 9h)
        $schedule->call(function () {
            \DB::statement('VACUUM');
            \DB::statement('ANALYZE');
        })->weekly()->saturdays()->at('09:00')
          ->appendOutputTo(storage_path('logs/optimization.log'));

        // Envoi des rapports hebdomadaires (tous les vendredis à 17h)
        $schedule->call(function () {
            // Logique d'envoi des rapports
        })->weekly()->fridays()->at('17:00')
          ->appendOutputTo(storage_path('logs/rapports.log'));

        // Vérification des mises à jour (tous les lundis à 10h)
        $schedule->call(function () {
            // Logique de vérification des mises à jour
        })->weekly()->mondays()->at('10:00')
          ->appendOutputTo(storage_path('logs/updates.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 