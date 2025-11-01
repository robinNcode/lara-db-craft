<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use for generating migrations and seeders.
    | If null, the default connection will be used.
    |
    */
    'connection' => env('DB_CRAFT_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Migrations Path
    |--------------------------------------------------------------------------
    |
    | The path where generated migration files will be stored.
    |
    */
    'migrations_path' => database_path('migrations'),

    /*
    |--------------------------------------------------------------------------
    | Seeders Path
    |--------------------------------------------------------------------------
    |
    | The path where generated seeder files will be stored.
    |
    */
    'seeders_path' => database_path('seeders'),

    /*
    |--------------------------------------------------------------------------
    | Exclude Tables
    |--------------------------------------------------------------------------
    |
    | Tables to exclude from migration and seeder generation.
    |
    */
    'exclude_tables' => [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeder Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of records to insert per chunk in seeders.
    |
    */
    'seeder_chunk_size' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Ignore Migration Pattern
    |--------------------------------------------------------------------------
    |
    | Pattern to identify tables that should not have migrations generated.
    |
    */
    'ignore_migration_pattern' => null,
];