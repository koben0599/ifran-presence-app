<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de la sécurité
    |--------------------------------------------------------------------------
    |
    | Paramètres de sécurité de l'application
    |
    */

    // Durée de vie de la session (en minutes)
    'session_lifetime' => env('SESSION_LIFETIME', 120),

    // Expiration du mot de passe (en jours)
    'password_expiry' => env('PASSWORD_EXPIRY', 90),

    // Nombre maximum de tentatives de connexion
    'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),

    // Durée de verrouillage (en minutes)
    'lockout_duration' => env('LOCKOUT_DURATION', 15),

    // Authentification à deux facteurs
    'two_factor_auth' => env('TWO_FACTOR_AUTH', false),

    // Configuration des mots de passe
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'prevent_common' => true,
        'history_count' => 5
    ],

    // Configuration des sessions
    'session' => [
        'secure' => env('SESSION_SECURE', false),
        'http_only' => true,
        'same_site' => 'lax',
        'regenerate' => true,
        'expire_on_close' => false
    ],

    // Configuration des cookies
    'cookies' => [
        'secure' => env('COOKIE_SECURE', false),
        'http_only' => true,
        'same_site' => 'lax',
        'expire' => 0
    ],

    // Configuration CSRF
    'csrf' => [
        'enabled' => true,
        'token_length' => 32,
        'expire' => 60
    ],

    // Configuration des en-têtes de sécurité
    'headers' => [
        'x_frame_options' => 'SAMEORIGIN',
        'x_content_type_options' => 'nosniff',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'content_security_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;"
    ],

    // Configuration de la validation des données
    'validation' => [
        'sanitize_input' => true,
        'validate_file_uploads' => true,
        'max_file_size' => 2048, // KB
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'scan_uploads' => false // Antivirus scan
    ],

    // Configuration de l'audit
    'audit' => [
        'enabled' => true,
        'log_events' => [
            'login',
            'logout',
            'password_change',
            'data_access',
            'data_modification',
            'export',
            'backup'
        ],
        'retention_days' => 365
    ],

    // Configuration des permissions
    'permissions' => [
        'role_based' => true,
        'resource_based' => true,
        'hierarchical' => true,
        'caching' => true
    ],

    // Configuration de la limitation de débit
    'rate_limiting' => [
        'enabled' => true,
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15
        ],
        'api' => [
            'max_requests' => 60,
            'decay_minutes' => 1
        ],
        'export' => [
            'max_requests' => 10,
            'decay_minutes' => 60
        ]
    ],

    // Configuration de la surveillance
    'monitoring' => [
        'failed_logins' => true,
        'suspicious_activity' => true,
        'data_access_patterns' => true,
        'performance_metrics' => true
    ],

    // Configuration de la récupération
    'recovery' => [
        'backup_verification' => true,
        'auto_restore' => false,
        'manual_restore' => true,
        'point_in_time_recovery' => false
    ],

    // Configuration de la conformité
    'compliance' => [
        'gdpr' => [
            'enabled' => true,
            'data_retention' => 365,
            'right_to_forget' => true,
            'data_portability' => true
        ],
        'rgpd' => [
            'enabled' => true,
            'consent_management' => true,
            'data_minimization' => true
        ]
    ],

    // Configuration de la confidentialité
    'privacy' => [
        'anonymization' => false,
        'pseudonymization' => true,
        'encryption_at_rest' => false,
        'encryption_in_transit' => true
    ],

    // Configuration des alertes de sécurité
    'alerts' => [
        'failed_login' => true,
        'suspicious_activity' => true,
        'data_breach' => true,
        'system_compromise' => true,
        'notification_channels' => ['email', 'slack']
    ]
]; 