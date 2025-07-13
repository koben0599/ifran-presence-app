<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des exports
    |--------------------------------------------------------------------------
    |
    | Paramètres pour l'export des données
    |
    */

    // Export automatique
    'auto' => env('EXPORT_AUTO', false),

    // Fréquence d'export automatique
    'frequency' => env('EXPORT_FREQUENCY', 'weekly'), // daily, weekly, monthly

    // Format d'export par défaut
    'format' => env('EXPORT_FORMAT', 'pdf'), // pdf, excel, csv

    // Rétention des exports (en jours)
    'retention' => env('EXPORT_RETENTION', 30),

    // Formats disponibles
    'formats' => [
        'pdf' => [
            'extension' => '.pdf',
            'mime_type' => 'application/pdf',
            'actif' => true
        ],
        'excel' => [
            'extension' => '.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'actif' => true
        ],
        'csv' => [
            'extension' => '.csv',
            'mime_type' => 'text/csv',
            'actif' => true
        ]
    ],

    // Types d'export disponibles
    'types' => [
        'presence' => [
            'nom' => 'Rapport de présence',
            'description' => 'Statistiques détaillées des présences',
            'formats' => ['pdf', 'excel', 'csv'],
            'colonnes' => [
                'date' => 'Date',
                'classe' => 'Classe',
                'module' => 'Module',
                'enseignant' => 'Enseignant',
                'etudiant' => 'Étudiant',
                'statut' => 'Statut',
                'heure_arrivee' => 'Heure d\'arrivée',
                'justification' => 'Justification'
            ]
        ],
        'classe' => [
            'nom' => 'Rapport par classe',
            'description' => 'Analyse des performances par classe',
            'formats' => ['pdf', 'excel'],
            'colonnes' => [
                'classe' => 'Classe',
                'etudiants' => 'Nombre d\'étudiants',
                'seances' => 'Nombre de séances',
                'taux_presence' => 'Taux de présence',
                'absences' => 'Nombre d\'absences',
                'retards' => 'Nombre de retards'
            ]
        ],
        'module' => [
            'nom' => 'Rapport par module',
            'description' => 'Statistiques par module et enseignant',
            'formats' => ['pdf', 'excel'],
            'colonnes' => [
                'module' => 'Module',
                'enseignant' => 'Enseignant',
                'classes' => 'Classes',
                'seances' => 'Séances',
                'taux_presence' => 'Taux de présence',
                'heures_totales' => 'Heures totales'
            ]
        ],
        'etudiant' => [
            'nom' => 'Rapport étudiant',
            'description' => 'Suivi individuel des étudiants',
            'formats' => ['pdf', 'excel'],
            'colonnes' => [
                'etudiant' => 'Étudiant',
                'classe' => 'Classe',
                'modules' => 'Modules suivis',
                'seances_suivies' => 'Séances suivies',
                'taux_presence' => 'Taux de présence',
                'absences' => 'Absences',
                'retards' => 'Retards',
                'justifications' => 'Justifications'
            ]
        ],
        'alerte' => [
            'nom' => 'Rapport d\'alertes',
            'description' => 'Synthèse des alertes et problèmes',
            'formats' => ['pdf', 'excel'],
            'colonnes' => [
                'type' => 'Type d\'alerte',
                'etudiant' => 'Étudiant',
                'classe' => 'Classe',
                'description' => 'Description',
                'date' => 'Date',
                'statut' => 'Statut'
            ]
        ],
        'evolution' => [
            'nom' => 'Rapport d\'évolution',
            'description' => 'Évolution des présences dans le temps',
            'formats' => ['pdf', 'excel'],
            'colonnes' => [
                'periode' => 'Période',
                'classe' => 'Classe',
                'taux_presence' => 'Taux de présence',
                'evolution' => 'Évolution',
                'tendance' => 'Tendance'
            ]
        ]
    ],

    // Configuration des templates PDF
    'templates' => [
        'presence' => 'exports.presence',
        'classe' => 'exports.classe',
        'module' => 'exports.module',
        'etudiant' => 'exports.etudiant',
        'alerte' => 'exports.alerte',
        'evolution' => 'exports.evolution'
    ],

    // Configuration des en-têtes
    'headers' => [
        'logo' => true,
        'titre' => 'IFran Presence',
        'sous_titre' => 'Système de gestion des présences',
        'date_generation' => true,
        'page_numbers' => true
    ],

    // Configuration des styles
    'styles' => [
        'primary_color' => '#3B82F6',
        'secondary_color' => '#6B7280',
        'success_color' => '#10B981',
        'warning_color' => '#F59E0B',
        'danger_color' => '#EF4444',
        'font_family' => 'Arial, sans-serif',
        'font_size' => 12
    ],

    // Configuration du stockage
    'storage' => [
        'disk' => 'local',
        'path' => 'exports',
        'compression' => false,
        'encryption' => false
    ],

    // Configuration des permissions
    'permissions' => [
        'admin' => ['*'],
        'coordinateur' => ['presence', 'classe', 'module', 'etudiant', 'alerte'],
        'enseignant' => ['presence', 'module', 'etudiant'],
        'etudiant' => ['presence']
    ]
]; 