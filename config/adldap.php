<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    */
    'default' => env('AD_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'default' => [
            'enabled' => env('AD_CONNECTION_ENABLED', false),
            
            // LDAP Connection Settings
            'auto_connect' => false,
            'connection' => LdapRecord\Connection::class,
            'settings' => [
                // LDAP Schema
                'schema' => LdapRecord\Schemas\ActiveDirectory::class,

                // Server Settings
                'hosts' => [env('AD_HOST', '127.0.0.1')],
                'port' => env('AD_PORT', 389),
                'timeout' => env('AD_TIMEOUT', 5),

                // Base DN
                'base_dn' => env('AD_BASE_DN', 'dc=local,dc=com'),

                // Authentication
                'username' => env('AD_USERNAME'),
                'password' => env('AD_PASSWORD'),

                // Connection Options
                'use_ssl' => env('AD_USE_SSL', false),
                'use_tls' => env('AD_USE_TLS', false),

                // Logging
                'logging' => env('AD_LOGGING', false),

                // Custom LDAP Options
                'custom_options' => [
                    // See: http://php.net/ldap_set_option
                    LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER,
                    LDAP_OPT_REFERRALS => 0,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Synchronization Settings
    |--------------------------------------------------------------------------
    */
    'sync' => [
        'frequency' => env('AD_SYNC_FREQUENCY', 'daily'),
        'enabled' => env('AD_CONNECTION_ENABLED', false),
        
        // Campos a sincronizar do AD para o sistema
        'attributes' => [
            'username' => 'samaccountname',
            'name' => 'displayname',
            'email' => 'mail',
            'first_name' => 'givenname',
            'last_name' => 'sn',
            'department' => 'department',
            'title' => 'title',
        ],
        
        // Configurações de log
        'log_successful_sync' => true,
        'log_failed_sync' => true,
        'keep_logs_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model Settings
    |--------------------------------------------------------------------------
    */
    'user_model' => [
        'class' => App\Models\Administracao\User::class,
        'login_type_field' => 'login_type',
        'login_type_value' => 'ad',
        'sync_field' => 'ad_sync_at',
        'ad_id_field' => 'ad_user_id',
        'ad_domain_field' => 'ad_domain',
    ],
];
