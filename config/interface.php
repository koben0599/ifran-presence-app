<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de l'interface
    |--------------------------------------------------------------------------
    |
    | Paramètres pour l'interface utilisateur
    |
    */

    // Thème de l'application
    'theme' => env('INTERFACE_THEME', 'light'), // light, dark, auto

    // Sidebar repliée par défaut
    'sidebar_collapsed' => env('INTERFACE_SIDEBAR_COLLAPSED', false),

    // Animations activées
    'animations' => env('INTERFACE_ANIMATIONS', true),

    // Position des notifications
    'notifications_position' => env('INTERFACE_NOTIFICATIONS_POSITION', 'top-right'), // top-right, top-left, bottom-right, bottom-left

    // Configuration des couleurs
    'colors' => [
        'primary' => '#3B82F6',
        'secondary' => '#6B7280',
        'success' => '#10B981',
        'warning' => '#F59E0B',
        'danger' => '#EF4444',
        'info' => '#06B6D4',
        'light' => '#F9FAFB',
        'dark' => '#111827'
    ],

    // Configuration des icônes
    'icons' => [
        'provider' => 'fontawesome', // fontawesome, heroicons, feather
        'size' => 'lg', // xs, sm, md, lg, xl
        'style' => 'solid' // solid, regular, light, duotone
    ],

    // Configuration des tableaux
    'tables' => [
        'pagination' => [
            'per_page' => 15,
            'per_page_options' => [10, 15, 25, 50, 100],
            'show_info' => true,
            'show_navigation' => true
        ],
        'sorting' => [
            'enabled' => true,
            'multi_sort' => false
        ],
        'filtering' => [
            'enabled' => true,
            'global_search' => true,
            'column_filters' => true
        ],
        'export' => [
            'enabled' => true,
            'formats' => ['pdf', 'excel', 'csv']
        ]
    ],

    // Configuration des graphiques
    'charts' => [
        'provider' => 'chartjs', // chartjs, apexcharts, highcharts
        'theme' => 'light',
        'colors' => [
            '#3B82F6',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#06B6D4',
            '#F97316',
            '#84CC16'
        ],
        'animations' => true,
        'responsive' => true
    ],

    // Configuration des formulaires
    'forms' => [
        'validation' => [
            'show_errors' => true,
            'show_success' => true,
            'auto_focus' => true
        ],
        'layout' => [
            'label_position' => 'top', // top, left, floating
            'field_spacing' => 'md', // sm, md, lg
            'group_fields' => true
        ],
        'components' => [
            'datepicker' => [
                'format' => 'd/m/Y',
                'locale' => 'fr',
                'first_day' => 1
            ],
            'timepicker' => [
                'format' => 'H:i',
                'step' => 15
            ],
            'select' => [
                'search' => true,
                'clear' => true,
                'placeholder' => 'Sélectionner...'
            ]
        ]
    ],

    // Configuration des modales
    'modals' => [
        'backdrop' => true,
        'keyboard' => true,
        'focus' => true,
        'size' => 'md', // sm, md, lg, xl
        'animation' => 'fade'
    ],

    // Configuration des notifications toast
    'toasts' => [
        'position' => 'top-right',
        'duration' => 5000,
        'dismissible' => true,
        'stack' => true,
        'max' => 5
    ],

    // Configuration du menu
    'menu' => [
        'style' => 'sidebar', // sidebar, topbar, both
        'collapsible' => true,
        'responsive' => true,
        'search' => true
    ],

    // Configuration du footer
    'footer' => [
        'visible' => true,
        'links' => [
            'aide' => '/aide',
            'contact' => '/contact',
            'mentions' => '/mentions-legales'
        ],
        'copyright' => '© ' . date('Y') . ' IFran Presence. Tous droits réservés.'
    ],

    // Configuration de la page d'accueil
    'dashboard' => [
        'widgets' => [
            'statistiques' => true,
            'graphiques' => true,
            'activites' => true,
            'alertes' => true,
            'calendrier' => true
        ],
        'layout' => 'grid', // grid, list
        'columns' => 3
    ],

    // Configuration de l'accessibilité
    'accessibility' => [
        'high_contrast' => false,
        'large_text' => false,
        'reduced_motion' => false,
        'screen_reader' => true
    ],

    // Configuration de la localisation
    'localization' => [
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'datetime_format' => 'd/m/Y H:i',
        'currency' => 'EUR',
        'timezone' => 'Europe/Paris'
    ]
]; 