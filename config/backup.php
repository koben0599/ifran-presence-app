<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des backups
    |--------------------------------------------------------------------------
    |
    | Paramètres pour la sauvegarde automatique
    |
    */

    // Backup automatique
    'auto' => env('BACKUP_AUTO', true),

    // Fréquence de backup
    'frequency' => env('BACKUP_FREQUENCY', 'daily'), // hourly, daily, weekly, monthly

    // Rétention des backups (en jours)
    'retention' => env('BACKUP_RETENTION', 7),

    // Compression des backups
    'compression' => env('BACKUP_COMPRESSION', true),

    // Chiffrement des backups
    'encryption' => env('BACKUP_ENCRYPTION', false),

    // Stockage des backups
    'storage' => [
        'disk' => 'local',
        'path' => 'backups',
        'filename_prefix' => 'backup_',
        'filename_suffix' => '.sql'
    ],

    // Configuration de la base de données
    'database' => [
        'tables' => [
            'users',
            'classes',
            'modules',
            'seances',
            'presences',
            'justifications',
            'emploi_du_temps',
            'notifications'
        ],
        'exclude_tables' => [
            'migrations',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens'
        ]
    ],

    // Configuration des fichiers
    'files' => [
        'include' => [
            'storage/app/public',
            'storage/logs'
        ],
        'exclude' => [
            'storage/app/public/temp',
            'storage/logs/*.log'
        ]
    ],

    // Configuration des notifications
    'notifications' => [
        'success' => true,
        'failure' => true,
        'email' => env('BACKUP_NOTIFICATION_EMAIL'),
        'slack' => env('BACKUP_NOTIFICATION_SLACK')
    ],

    // Configuration de la planification
    'schedule' => [
        'time' => '02:00', // Heure de backup (format 24h)
        'timezone' => 'Europe/Paris'
    ],

    // Configuration de la restauration
    'restore' => [
        'enabled' => true,
        'confirmation_required' => true,
        'backup_required' => true
    ],

    // Configuration de la vérification
    'verification' => [
        'enabled' => true,
        'check_integrity' => true,
        'test_restore' => false
    ],

    // Configuration des métadonnées
    'metadata' => [
        'include_version' => true,
        'include_timestamp' => true,
        'include_size' => true,
        'include_checksum' => true
    ]
]; 