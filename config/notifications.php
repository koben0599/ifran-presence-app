<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des notifications
    |--------------------------------------------------------------------------
    |
    | Paramètres pour les différents types de notifications
    |
    */

    // Notifications par email
    'email' => env('NOTIFICATIONS_EMAIL', true),

    // Notifications par SMS
    'sms' => env('NOTIFICATIONS_SMS', false),

    // Notifications push
    'push' => env('NOTIFICATIONS_PUSH', true),

    // Alertes d'absence
    'alerte_absence' => env('NOTIFICATIONS_ALERTE_ABSENCE', true),

    // Alertes de retard
    'alerte_retard' => env('NOTIFICATIONS_ALERTE_RETARD', true),

    // Alertes de faible taux de présence
    'alerte_faible_taux' => env('NOTIFICATIONS_ALERTE_FAIBLE_TAUX', true),

    // Fréquence de vérification des alertes (en minutes)
    'frequence_verification' => env('NOTIFICATIONS_FREQUENCE', 30),

    // Rétention des notifications (en jours)
    'retention_jours' => env('NOTIFICATIONS_RETENTION', 30),

    // Types de notifications
    'types' => [
        'system' => [
            'icon' => 'fas fa-cog',
            'color' => 'text-gray-500',
            'bg_color' => 'bg-gray-50'
        ],
        'alerte' => [
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'text-orange-500',
            'bg_color' => 'bg-orange-50'
        ],
        'seance' => [
            'icon' => 'fas fa-calendar',
            'color' => 'text-purple-500',
            'bg_color' => 'bg-purple-50'
        ],
        'presence' => [
            'icon' => 'fas fa-user-check',
            'color' => 'text-indigo-500',
            'bg_color' => 'bg-indigo-50'
        ],
        'info' => [
            'icon' => 'fas fa-info-circle',
            'color' => 'text-blue-500',
            'bg_color' => 'bg-blue-50'
        ],
        'warning' => [
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'text-yellow-500',
            'bg_color' => 'bg-yellow-50'
        ],
        'danger' => [
            'icon' => 'fas fa-times-circle',
            'color' => 'text-red-500',
            'bg_color' => 'bg-red-50'
        ],
        'success' => [
            'icon' => 'fas fa-check-circle',
            'color' => 'text-green-500',
            'bg_color' => 'bg-green-50'
        ]
    ],

    // Configuration des templates d'email
    'templates' => [
        'absence' => [
            'sujet' => 'Alerte d\'absence - {etudiant}',
            'template' => 'emails.alertes.absence'
        ],
        'retard' => [
            'sujet' => 'Alerte de retard - {etudiant}',
            'template' => 'emails.alertes.retard'
        ],
        'faible_taux' => [
            'sujet' => 'Taux de présence faible - {etudiant}',
            'template' => 'emails.alertes.faible_taux'
        ],
        'nouvelle_seance' => [
            'sujet' => 'Nouvelle séance programmée',
            'template' => 'emails.seances.nouvelle'
        ],
        'rappel_seance' => [
            'sujet' => 'Rappel de séance',
            'template' => 'emails.seances.rappel'
        ]
    ],

    // Configuration des canaux
    'canaux' => [
        'mail' => [
            'actif' => true,
            'priorite' => 'high'
        ],
        'database' => [
            'actif' => true,
            'table' => 'notifications'
        ],
        'broadcast' => [
            'actif' => false,
            'channel' => 'notifications'
        ],
        'slack' => [
            'actif' => false,
            'webhook' => env('SLACK_WEBHOOK_URL')
        ]
    ],

    // Configuration des destinataires par type
    'destinataires' => [
        'absence' => ['etudiant', 'coordinateur', 'parent'],
        'retard' => ['etudiant', 'coordinateur'],
        'faible_taux' => ['etudiant', 'coordinateur', 'parent'],
        'nouvelle_seance' => ['enseignant', 'etudiant'],
        'rappel_seance' => ['enseignant', 'etudiant'],
        'system' => ['admin']
    ],

    // Configuration des délais
    'delais' => [
        'absence' => 0, // Immédiat
        'retard' => 5, // 5 minutes après le début de la séance
        'faible_taux' => 1440, // 24 heures
        'rappel_seance' => 60 // 1 heure avant la séance
    ]
]; 