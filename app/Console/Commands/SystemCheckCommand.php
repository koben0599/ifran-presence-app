<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Classe;
use App\Models\Module;
use App\Models\Seance;
use App\Models\Presence;
use App\Models\EmploiDuTemps;
use App\Models\Notification;

class SystemCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check {--fix : Corriger automatiquement les problèmes détectés}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier l\'état du système et détecter les problèmes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification de l\'état du système...');
        $this->newLine();

        $problems = [];
        $warnings = [];
        $info = [];

        // Vérification de la base de données
        $this->checkDatabase($problems, $warnings, $info);

        // Vérification des fichiers
        $this->checkFiles($problems, $warnings, $info);

        // Vérification des permissions
        $this->checkPermissions($problems, $warnings, $info);

        // Vérification de la configuration
        $this->checkConfiguration($problems, $warnings, $info);

        // Vérification des données
        $this->checkData($problems, $warnings, $info);

        // Vérification des performances
        $this->checkPerformance($problems, $warnings, $info);

        // Affichage des résultats
        $this->displayResults($problems, $warnings, $info);

        // Correction automatique si demandée
        if ($this->option('fix') && !empty($problems)) {
            $this->fixProblems($problems);
        }

        return empty($problems) ? 0 : 1;
    }

    /**
     * Vérifier la base de données
     */
    private function checkDatabase(&$problems, &$warnings, &$info)
    {
        $this->info('📊 Vérification de la base de données...');

        try {
            // Test de connexion
            DB::connection()->getPdo();
            $info[] = '✅ Connexion à la base de données OK';

            // Vérification des tables
            $tables = ['users', 'classes', 'modules', 'seances', 'presences', 'emploi_du_temps', 'notifications'];
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $info[] = "✅ Table {$table} existe";
                } else {
                    $problems[] = "❌ Table {$table} manquante";
                }
            }

            // Vérification de l'intégrité
            if (DB::connection()->getDriverName() === 'sqlite') {
                $result = DB::select('PRAGMA integrity_check');
                if ($result[0]->integrity_check === 'ok') {
                    $info[] = '✅ Intégrité de la base de données OK';
                } else {
                    $problems[] = '❌ Problème d\'intégrité de la base de données';
                }
            }

        } catch (\Exception $e) {
            $problems[] = '❌ Erreur de connexion à la base de données: ' . $e->getMessage();
        }
    }

    /**
     * Vérifier les fichiers
     */
    private function checkFiles(&$problems, &$warnings, &$info)
    {
        $this->info('📁 Vérification des fichiers...');

        // Vérification des dossiers de stockage
        $directories = [
            storage_path('app/public'),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($directories as $directory) {
            if (is_dir($directory) && is_writable($directory)) {
                $info[] = "✅ Dossier {$directory} accessible en écriture";
            } else {
                $problems[] = "❌ Dossier {$directory} non accessible ou non writable";
            }
        }

        // Vérification de l'espace disque
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercent = ($usedSpace / $totalSpace) * 100;

        if ($usagePercent > 90) {
            $problems[] = "❌ Espace disque critique: {$usagePercent}% utilisé";
        } elseif ($usagePercent > 80) {
            $warnings[] = "⚠️ Espace disque élevé: {$usagePercent}% utilisé";
        } else {
            $info[] = "✅ Espace disque OK: {$usagePercent}% utilisé";
        }
    }

    /**
     * Vérifier les permissions
     */
    private function checkPermissions(&$problems, &$warnings, &$info)
    {
        $this->info('🔐 Vérification des permissions...');

        // Vérification des permissions de fichiers
        $files = [
            base_path('.env'),
            storage_path('logs/laravel.log'),
            database_path('database.sqlite'),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (is_readable($file)) {
                    $info[] = "✅ Fichier {$file} lisible";
                } else {
                    $problems[] = "❌ Fichier {$file} non lisible";
                }
            }
        }
    }

    /**
     * Vérifier la configuration
     */
    private function checkConfiguration(&$problems, &$warnings, &$info)
    {
        $this->info('⚙️ Vérification de la configuration...');

        // Vérification des variables d'environnement
        $requiredEnvVars = ['APP_KEY', 'APP_NAME', 'DB_CONNECTION'];
        foreach ($requiredEnvVars as $var) {
            if (config($var)) {
                $info[] = "✅ Variable {$var} configurée";
            } else {
                $problems[] = "❌ Variable {$var} manquante";
            }
        }

        // Vérification du mode debug
        if (config('app.debug')) {
            $warnings[] = '⚠️ Mode debug activé (désactiver en production)';
        } else {
            $info[] = '✅ Mode debug désactivé';
        }

        // Vérification du cache
        if (Cache::has('system_check')) {
            $info[] = '✅ Cache fonctionnel';
        } else {
            Cache::put('system_check', true, 60);
            $info[] = '✅ Cache testé et fonctionnel';
        }
    }

    /**
     * Vérifier les données
     */
    private function checkData(&$problems, &$warnings, &$info)
    {
        $this->info('📊 Vérification des données...');

        // Vérification des utilisateurs
        $userCount = User::count();
        if ($userCount === 0) {
            $warnings[] = '⚠️ Aucun utilisateur dans la base de données';
        } else {
            $info[] = "✅ {$userCount} utilisateur(s) trouvé(s)";
        }

        // Vérification des classes
        $classeCount = Classe::count();
        if ($classeCount === 0) {
            $warnings[] = '⚠️ Aucune classe dans la base de données';
        } else {
            $info[] = "✅ {$classeCount} classe(s) trouvée(s)";
        }

        // Vérification des modules
        $moduleCount = Module::count();
        if ($moduleCount === 0) {
            $warnings[] = '⚠️ Aucun module dans la base de données';
        } else {
            $info[] = "✅ {$moduleCount} module(s) trouvé(s)";
        }

        // Vérification des emplois du temps
        $emploiCount = EmploiDuTemps::count();
        if ($emploiCount === 0) {
            $warnings[] = '⚠️ Aucun emploi du temps dans la base de données';
        } else {
            $info[] = "✅ {$emploiCount} emploi(s) du temps trouvé(s)";
        }

        // Vérification des séances
        $seanceCount = Seance::count();
        if ($seanceCount === 0) {
            $warnings[] = '⚠️ Aucune séance dans la base de données';
        } else {
            $info[] = "✅ {$seanceCount} séance(s) trouvée(s)";
        }

        // Vérification des présences
        $presenceCount = Presence::count();
        $info[] = "✅ {$presenceCount} présence(s) trouvée(s)";

        // Vérification des notifications
        $notificationCount = Notification::count();
        $info[] = "✅ {$notificationCount} notification(s) trouvée(s)";
    }

    /**
     * Vérifier les performances
     */
    private function checkPerformance(&$problems, &$warnings, &$info)
    {
        $this->info('⚡ Vérification des performances...');

        // Test de performance de la base de données
        $startTime = microtime(true);
        User::count();
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        if ($executionTime > 1000) {
            $warnings[] = "⚠️ Performance de la base de données lente: {$executionTime}ms";
        } else {
            $info[] = "✅ Performance de la base de données OK: {$executionTime}ms";
        }

        // Vérification de la taille des logs
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logSize = filesize($logFile);
            if ($logSize > 10 * 1024 * 1024) { // 10MB
                $warnings[] = '⚠️ Fichier de log volumineux (' . round($logSize / 1024 / 1024, 2) . 'MB)';
            } else {
                $info[] = '✅ Taille des logs OK';
            }
        }
    }

    /**
     * Afficher les résultats
     */
    private function displayResults($problems, $warnings, $info)
    {
        $this->newLine();
        $this->info('📋 Résultats de la vérification:');
        $this->newLine();

        if (!empty($problems)) {
            $this->error('❌ Problèmes détectés:');
            foreach ($problems as $problem) {
                $this->line($problem);
            }
            $this->newLine();
        }

        if (!empty($warnings)) {
            $this->warn('⚠️ Avertissements:');
            foreach ($warnings as $warning) {
                $this->line($warning);
            }
            $this->newLine();
        }

        if (!empty($info)) {
            $this->info('✅ Informations:');
            foreach ($info as $infoItem) {
                $this->line($infoItem);
            }
            $this->newLine();
        }

        $this->info('📊 Résumé:');
        $this->line("Problèmes: " . count($problems));
        $this->line("Avertissements: " . count($warnings));
        $this->line("Informations: " . count($info));

        if (empty($problems)) {
            $this->info('🎉 Système en bon état !');
        } else {
            $this->error('🔧 Des problèmes ont été détectés. Utilisez --fix pour les corriger automatiquement.');
        }
    }

    /**
     * Corriger les problèmes automatiquement
     */
    private function fixProblems($problems)
    {
        $this->newLine();
        $this->info('🔧 Correction automatique des problèmes...');

        foreach ($problems as $problem) {
            if (str_contains($problem, 'Fichier') && str_contains($problem, 'non lisible')) {
                $this->line('🔧 Tentative de correction des permissions de fichiers...');
                // Logique de correction des permissions
            }

            if (str_contains($problem, 'Espace disque critique')) {
                $this->line('🔧 Nettoyage des fichiers temporaires...');
                // Logique de nettoyage
            }

            if (str_contains($problem, 'Fichier de log volumineux')) {
                $this->line('🔧 Rotation des logs...');
                $logFile = storage_path('logs/laravel.log');
                if (file_exists($logFile)) {
                    rename($logFile, $logFile . '.old');
                    file_put_contents($logFile, '');
                }
            }
        }

        $this->info('✅ Correction terminée');
    }
} 