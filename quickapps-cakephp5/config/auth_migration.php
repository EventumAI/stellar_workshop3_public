<?php
/**
 * Authentication Migration Configuration
 *
 * Configuration file for managing the progressive migration from CakePHP 3 to CakePHP 5 authentication
 */

use App\Service\AuthMigrationFeatureFlag;

return [
    'AuthMigration' => [
        // Default migration stage (can be overridden by cache/database)
        'stage' => AuthMigrationFeatureFlag::STAGE_DISABLED,

        // Default feature flags (can be overridden by cache/database)
        'flags' => [
            AuthMigrationFeatureFlag::FLAG_USE_NEW_USERS_TABLE => false,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_AUTH_COMPONENT => false,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_PASSWORD_HASHER => false,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_TOKEN_SYSTEM => false,
            AuthMigrationFeatureFlag::FLAG_MIGRATE_USER_ROLES => false,
            AuthMigrationFeatureFlag::FLAG_ENABLE_LEGACY_FALLBACK => true,
            AuthMigrationFeatureFlag::FLAG_LOG_AUTH_OPERATIONS => true,
        ],

        // Migration monitoring settings
        'monitoring' => [
            'log_level' => 'info',
            'alert_thresholds' => [
                'failed_logins_per_minute' => 10,
                'response_time_ms' => 1000,
                'error_rate_percent' => 5,
            ],
            'emergency_rollback_triggers' => [
                'consecutive_auth_failures' => 50,
                'error_rate_threshold' => 25, // percentage
                'response_time_threshold' => 5000, // milliseconds
            ]
        ],

        // Legacy CakePHP 3 compatibility settings
        'legacy' => [
            'database_connection' => 'legacy_auth',
            'users_table' => 'users',
            'roles_table' => 'roles',
            'users_roles_table' => 'users_roles',
            'password_hasher' => 'Legacy', // CakePHP 3 compatible hasher
            'session_key' => 'Auth.User', // CakePHP 3 session key
        ],

        // New CakePHP 5 settings
        'new' => [
            'database_connection' => 'default',
            'users_table' => 'users_v5',
            'roles_table' => 'roles_v5',
            'users_roles_table' => 'users_roles_v5',
            'password_hasher' => 'Default', // CakePHP 5 hasher
            'session_key' => 'Auth.User', // Updated session key
            'identity_resolver' => 'Authentication.Orm',
        ],

        // Data migration settings
        'migration' => [
            'batch_size' => 100, // Users to migrate per batch
            'backup_enabled' => true,
            'validation_enabled' => true,
            'rollback_enabled' => true,
            'timeout_seconds' => 300,
        ],

        // Security settings during migration
        'security' => [
            'require_secure_connection' => true,
            'log_sensitive_operations' => true,
            'emergency_admin_account' => [
                'username' => 'emergency_admin',
                'enabled' => false, // Only enable during emergencies
            ],
            'session_rotation_during_migration' => true,
        ],

        // Testing and validation
        'testing' => [
            'test_user_credentials' => [
                'username' => 'test_migration_user',
                'password' => 'test_password_123',
                'enabled' => false, // Only enable in test environments
            ],
            'validate_password_hashes' => true,
            'compare_legacy_vs_new' => true,
        ]
    ]
];