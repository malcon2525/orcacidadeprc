<?php

return [
    /*
    |--------------------------------------------------------------------------
    | EXEMPLO DE CONFIGURAÇÃO DO ACTIVE DIRECTORY
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações necessárias para conectar
    | ao Active Directory. Copie este arquivo para .env e configure
    | as variáveis conforme seu ambiente.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Variáveis de Ambiente Necessárias (.env)
    |--------------------------------------------------------------------------
    |
    | AD_CONNECTION_ENABLED=true
    | AD_HOST=seu-servidor-ad.com
    | AD_PORT=389
    | AD_BASE_DN=OU=Empregados,DC=empresa,DC=com
    | AD_USERNAME=usuario-ad
    | AD_PASSWORD=senha-ad
    | AD_USE_SSL=false
    | AD_USE_TLS=false
    | AD_LOGGING=true
    | AD_SYNC_FREQUENCY=daily
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuração Atual
    |--------------------------------------------------------------------------
    */
    'default' => env('AD_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'enabled' => env('AD_CONNECTION_ENABLED', false),
            'auto_connect' => false,
            'connection' => LdapRecord\Connection::class,
            'settings' => [
                'schema' => LdapRecord\Schemas\ActiveDirectory::class,
                'hosts' => [env('AD_HOST', '127.0.0.1')],
                'port' => env('AD_PORT', 389),
                'timeout' => env('AD_TIMEOUT', 5),
                'base_dn' => env('AD_BASE_DN', 'dc=local,dc=com'),
                'username' => env('AD_USERNAME'),
                'password' => env('AD_PASSWORD'),
                'use_ssl' => env('AD_USE_SSL', false),
                'use_tls' => env('AD_USE_TLS', false),
                'logging' => env('AD_LOGGING', false),
                'custom_options' => [
                    LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER,
                    LDAP_OPT_REFERRALS => 0,
                ],
            ],
        ],
    ],

    'sync' => [
        'frequency' => env('AD_SYNC_FREQUENCY', 'daily'),
        'enabled' => env('AD_CONNECTION_ENABLED', false),
        'attributes' => [
            'username' => 'samaccountname',
            'name' => 'displayname',
            'email' => 'mail',
            'first_name' => 'givenname',
            'last_name' => 'sn',
            'department' => 'department',
            'title' => 'title',
        ],
        'log_successful_sync' => true,
        'log_failed_sync' => true,
        'keep_logs_days' => 30,
    ],

    'user_model' => [
        'class' => App\Models\Administracao\User::class,
        'login_type_field' => 'login_type',
        'login_type_value' => 'ad',
        'sync_field' => 'ad_sync_at',
        'ad_id_field' => 'ad_user_id',
        'ad_domain_field' => 'ad_domain',
    ],
];
