<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ParametreController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $parametres = $this->getParametres();

        return view('parametres.index', compact('parametres'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'seuil_absence' => 'required|integer|min:1|max:100',
            'seuil_retard' => 'required|integer|min:1|max:100',
            'duree_session' => 'required|integer|min:15|max:480',
            'timezone' => 'required|string',
            'locale' => 'required|string|in:fr,en',
            'maintenance_mode' => 'boolean',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
            'export_auto' => 'boolean',
            'backup_auto' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Mettre à jour les paramètres
            $this->updateParametres($request->all());

            // Gérer l'upload du logo
            if ($request->hasFile('logo')) {
                $this->uploadLogo($request->file('logo'));
            }

            // Vider le cache
            Cache::flush();

            return back()->with('success', 'Paramètres mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour des paramètres : ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les paramètres actuels
     */
    private function getParametres(): array
    {
        return [
            'general' => [
                'app_name' => config('app.name', 'IFran Presence'),
                'app_description' => config('app.description', 'Système de gestion des présences'),
                'timezone' => config('app.timezone', 'Europe/Paris'),
                'locale' => config('app.locale', 'fr'),
                'maintenance_mode' => app()->isDownForMaintenance(),
            ],
            'presence' => [
                'seuil_absence' => config('presence.seuil_absence', 70),
                'seuil_retard' => config('presence.seuil_retard', 5),
                'duree_session' => config('presence.duree_session', 120),
                'tolerance_retard' => config('presence.tolerance_retard', 15),
                'auto_justification' => config('presence.auto_justification', false),
            ],
            'notifications' => [
                'notifications_email' => config('notifications.email', true),
                'notifications_sms' => config('notifications.sms', false),
                'notifications_push' => config('notifications.push', true),
                'alerte_absence' => config('notifications.alerte_absence', true),
                'alerte_retard' => config('notifications.alerte_retard', true),
                'alerte_faible_taux' => config('notifications.alerte_faible_taux', true),
            ],
            'export' => [
                'export_auto' => config('export.auto', false),
                'export_frequency' => config('export.frequency', 'weekly'),
                'export_format' => config('export.format', 'pdf'),
                'export_retention' => config('export.retention', 30),
            ],
            'backup' => [
                'backup_auto' => config('backup.auto', true),
                'backup_frequency' => config('backup.frequency', 'daily'),
                'backup_retention' => config('backup.retention', 7),
                'backup_compression' => config('backup.compression', true),
            ],
            'securite' => [
                'session_lifetime' => config('session.lifetime', 120),
                'password_expiry' => config('auth.password_expiry', 90),
                'max_login_attempts' => config('auth.max_login_attempts', 5),
                'lockout_duration' => config('auth.lockout_duration', 15),
                'two_factor_auth' => config('auth.two_factor', false),
            ],
            'interface' => [
                'theme' => config('interface.theme', 'light'),
                'sidebar_collapsed' => config('interface.sidebar_collapsed', false),
                'animations' => config('interface.animations', true),
                'notifications_position' => config('interface.notifications_position', 'top-right'),
            ]
        ];
    }

    /**
     * Mettre à jour les paramètres
     */
    private function updateParametres(array $data): void
    {
        // Paramètres généraux
        $this->updateConfig('app.name', $data['app_name']);
        $this->updateConfig('app.description', $data['app_description']);
        $this->updateConfig('app.timezone', $data['timezone']);
        $this->updateConfig('app.locale', $data['locale']);

        // Mode maintenance
        if ($data['maintenance_mode'] ?? false) {
            if (!app()->isDownForMaintenance()) {
                \Artisan::call('down');
            }
        } else {
            if (app()->isDownForMaintenance()) {
                \Artisan::call('up');
            }
        }

        // Paramètres de présence
        $this->updateConfig('presence.seuil_absence', $data['seuil_absence']);
        $this->updateConfig('presence.seuil_retard', $data['seuil_retard']);
        $this->updateConfig('presence.duree_session', $data['duree_session']);

        // Paramètres de notifications
        $this->updateConfig('notifications.email', $data['notifications_email'] ?? false);
        $this->updateConfig('notifications.sms', $data['notifications_sms'] ?? false);

        // Paramètres d'export
        $this->updateConfig('export.auto', $data['export_auto'] ?? false);

        // Paramètres de backup
        $this->updateConfig('backup.auto', $data['backup_auto'] ?? false);

        // Sauvegarder dans la base de données ou le cache
        $this->saveParametresToCache($data);
    }

    /**
     * Mettre à jour un paramètre de configuration
     */
    private function updateConfig(string $key, $value): void
    {
        // Cette méthode mettrait à jour le fichier de configuration
        // En production, on utiliserait une table de paramètres
        Cache::put('config.' . $key, $value, now()->addYear());
    }

    /**
     * Sauvegarder les paramètres dans le cache
     */
    private function saveParametresToCache(array $data): void
    {
        foreach ($data as $key => $value) {
            Cache::put('parametres.' . $key, $value, now()->addYear());
        }
    }

    /**
     * Upload du logo
     */
    private function uploadLogo($file): void
    {
        $filename = 'logo.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/images', $filename);
        
        // Mettre à jour le chemin du logo dans la configuration
        $this->updateConfig('app.logo', Storage::url($path));
    }

    /**
     * Afficher les logs système
     */
    public function logs()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $logs = $this->getSystemLogs();

        return view('parametres.logs', compact('logs'));
    }

    /**
     * Obtenir les logs système
     */
    private function getSystemLogs(): array
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $lines = file($logFile);
            $logs = array_slice($lines, -100); // Dernières 100 lignes
        }

        return $logs;
    }

    /**
     * Vider les logs
     */
    public function clearLogs()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }

            return back()->with('success', 'Logs vidés avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du vidage des logs : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les informations système
     */
    public function system()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $systemInfo = $this->getSystemInfo();

        return view('parametres.system', compact('systemInfo'));
    }

    /**
     * Obtenir les informations système
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'disk_free_space' => $this->formatBytes(disk_free_space(storage_path())),
            'disk_total_space' => $this->formatBytes(disk_total_space(storage_path())),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    /**
     * Formater les bytes en format lisible
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Sauvegarder la base de données
     */
    public function backup()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('backups/' . $filename);

            // Créer le dossier si il n'existe pas
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            // Commande de sauvegarde (exemple pour MySQL)
            $command = sprintf(
                'mysqldump -u%s -p%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $path
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                return response()->download($path)->deleteFileAfterSend();
            } else {
                return back()->with('error', 'Erreur lors de la sauvegarde');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }
} 