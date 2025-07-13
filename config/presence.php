<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres de présence
    |--------------------------------------------------------------------------
    |
    | Configuration des seuils et règles de présence
    |
    */

    // Seuil de présence en pourcentage
    'seuil_absence' => env('PRESENCE_SEUIL_ABSENCE', 70),

    // Seuil de retard en minutes
    'seuil_retard' => env('PRESENCE_SEUIL_RETARD', 5),

    // Durée d'une session en minutes
    'duree_session' => env('PRESENCE_DUREE_SESSION', 120),

    // Tolérance de retard en minutes
    'tolerance_retard' => env('PRESENCE_TOLERANCE_RETARD', 15),

    // Justification automatique
    'auto_justification' => env('PRESENCE_AUTO_JUSTIFICATION', false),

    // Types de cours autorisés pour la saisie par les enseignants
    'types_enseignant' => ['presentiel'],

    // Types de cours gérés par les coordinateurs
    'types_coordinateur' => ['elearning', 'workshop'],

    // Jours de la semaine
    'jours_semaine' => [
        'monday' => 'Lundi',
        'tuesday' => 'Mardi',
        'wednesday' => 'Mercredi',
        'thursday' => 'Jeudi',
        'friday' => 'Vendredi',
        'saturday' => 'Samedi',
        'sunday' => 'Dimanche'
    ],

    // Types de cours
    'types_cours' => [
        'presentiel' => 'Présentiel',
        'elearning' => 'E-learning',
        'workshop' => 'Atelier'
    ],

    // Statuts de présence
    'statuts' => [
        'present' => 'Présent',
        'absent' => 'Absent',
        'retard' => 'Retard',
        'justifie' => 'Justifié'
    ],

    // Couleurs pour les types de cours
    'couleurs_types' => [
        'presentiel' => 'bg-blue-100 text-blue-800',
        'elearning' => 'bg-green-100 text-green-800',
        'workshop' => 'bg-purple-100 text-purple-800'
    ],

    // Configuration des alertes
    'alertes' => [
        'absences_consecutives' => 3,
        'seuil_taux_faible' => 70,
        'seuil_taux_critique' => 60,
        'retards_frequents' => 5
    ],

    // Configuration des exports
    'exports' => [
        'formats_disponibles' => ['pdf', 'excel', 'csv'],
        'format_defaut' => 'pdf',
        'retention_jours' => 30
    ],

    // Configuration des notifications
    'notifications' => [
        'email' => true,
        'sms' => false,
        'push' => true,
        'alerte_absence' => true,
        'alerte_retard' => true,
        'alerte_faible_taux' => true
    ],

    // Configuration des rapports
    'rapports' => [
        'periodes' => [
            'semaine' => 'Semaine',
            'mois' => 'Mois',
            'trimestre' => 'Trimestre',
            'annee' => 'Année'
        ],
        'groupements' => [
            'jour' => 'Par jour',
            'semaine' => 'Par semaine',
            'mois' => 'Par mois',
            'module' => 'Par module',
            'classe' => 'Par classe',
            'enseignant' => 'Par enseignant'
        ]
    ]
]; 