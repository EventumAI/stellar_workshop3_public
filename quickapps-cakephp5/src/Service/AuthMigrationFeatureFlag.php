<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Psr\Log\LoggerInterface;

/**
 * Authentication Migration Feature Flag System
 *
 * Manages progressive rollout of authentication migration from CakePHP 3 to CakePHP 5
 * Supports gradual migration with rollback capabilities and monitoring
 */
class AuthMigrationFeatureFlag
{
    public const CACHE_KEY = 'auth_migration_flags';
    public const CACHE_CONFIG = 'default';

    // Migration stages
    public const STAGE_DISABLED = 'disabled';
    public const STAGE_SCHEMA_ONLY = 'schema_only';
    public const STAGE_READ_HYBRID = 'read_hybrid';
    public const STAGE_WRITE_HYBRID = 'write_hybrid';
    public const STAGE_FULL_MIGRATION = 'full_migration';
    public const STAGE_LEGACY_CLEANUP = 'legacy_cleanup';

    // Feature flags
    public const FLAG_USE_NEW_USERS_TABLE = 'use_new_users_table';
    public const FLAG_USE_NEW_AUTH_COMPONENT = 'use_new_auth_component';
    public const FLAG_USE_NEW_PASSWORD_HASHER = 'use_new_password_hasher';
    public const FLAG_USE_NEW_TOKEN_SYSTEM = 'use_new_token_system';
    public const FLAG_MIGRATE_USER_ROLES = 'migrate_user_roles';
    public const FLAG_ENABLE_LEGACY_FALLBACK = 'enable_legacy_fallback';
    public const FLAG_LOG_AUTH_OPERATIONS = 'log_auth_operations';

    private LoggerInterface $logger;
    private array $flags = [];
    private string $currentStage = self::STAGE_DISABLED;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->loadFlags();
    }

    /**
     * Check if a specific feature flag is enabled
     */
    public function isEnabled(string $flag): bool
    {
        $value = $this->flags[$flag] ?? false;

        if ($this->isFlag(self::FLAG_LOG_AUTH_OPERATIONS)) {
            $this->logger->debug('Feature flag checked', [
                'flag' => $flag,
                'enabled' => $value,
                'stage' => $this->currentStage
            ]);
        }

        return $value;
    }

    /**
     * Set a feature flag value
     */
    public function setFlag(string $flag, bool $enabled): void
    {
        $this->flags[$flag] = $enabled;
        $this->saveFlags();

        $this->logger->info('Feature flag updated', [
            'flag' => $flag,
            'enabled' => $enabled,
            'stage' => $this->currentStage
        ]);
    }

    /**
     * Get current migration stage
     */
    public function getCurrentStage(): string
    {
        return $this->currentStage;
    }

    /**
     * Set migration stage and update related flags
     */
    public function setStage(string $stage): void
    {
        if (!$this->isValidStage($stage)) {
            throw new \InvalidArgumentException("Invalid migration stage: {$stage}");
        }

        $previousStage = $this->currentStage;
        $this->currentStage = $stage;
        $this->updateFlagsForStage($stage);
        $this->saveFlags();

        $this->logger->warning('Migration stage changed', [
            'previous_stage' => $previousStage,
            'new_stage' => $stage,
            'flags' => $this->flags
        ]);
    }

    /**
     * Get migration progress percentage
     */
    public function getMigrationProgress(): int
    {
        $stages = [
            self::STAGE_DISABLED => 0,
            self::STAGE_SCHEMA_ONLY => 15,
            self::STAGE_READ_HYBRID => 35,
            self::STAGE_WRITE_HYBRID => 60,
            self::STAGE_FULL_MIGRATION => 85,
            self::STAGE_LEGACY_CLEANUP => 100
        ];

        return $stages[$this->currentStage] ?? 0;
    }

    /**
     * Check if rollback is safe for current stage
     */
    public function isRollbackSafe(): bool
    {
        // Rollback is safe until we reach full migration
        return in_array($this->currentStage, [
            self::STAGE_DISABLED,
            self::STAGE_SCHEMA_ONLY,
            self::STAGE_READ_HYBRID,
            self::STAGE_WRITE_HYBRID
        ]);
    }

    /**
     * Emergency rollback to disabled state
     */
    public function emergencyRollback(): void
    {
        $this->logger->critical('Emergency rollback initiated', [
            'previous_stage' => $this->currentStage,
            'flags_before_rollback' => $this->flags
        ]);

        $this->currentStage = self::STAGE_DISABLED;
        $this->updateFlagsForStage(self::STAGE_DISABLED);
        $this->saveFlags();

        $this->logger->critical('Emergency rollback completed');
    }

    /**
     * Get migration readiness checklist
     */
    public function getReadinessChecklist(): array
    {
        return [
            'database_backup_verified' => $this->checkDatabaseBackup(),
            'legacy_auth_functional' => $this->checkLegacyAuth(),
            'new_schema_created' => $this->checkNewSchema(),
            'data_migration_ready' => $this->checkDataMigration(),
            'rollback_script_tested' => $this->checkRollbackScript(),
            'monitoring_configured' => $this->checkMonitoring()
        ];
    }

    /**
     * Load flags from cache or configuration
     */
    private function loadFlags(): void
    {
        // Try cache first
        $cached = Cache::read(self::CACHE_KEY, self::CACHE_CONFIG);
        if ($cached !== null) {
            $this->flags = $cached['flags'] ?? [];
            $this->currentStage = $cached['stage'] ?? self::STAGE_DISABLED;
            return;
        }

        // Fallback to configuration
        $config = Configure::read('AuthMigration.flags', []);
        $this->flags = $config;
        $this->currentStage = Configure::read('AuthMigration.stage', self::STAGE_DISABLED);
    }

    /**
     * Save flags to cache
     */
    private function saveFlags(): void
    {
        $data = [
            'flags' => $this->flags,
            'stage' => $this->currentStage,
            'updated' => time()
        ];

        Cache::write(self::CACHE_KEY, $data, self::CACHE_CONFIG);
    }

    /**
     * Update flags based on migration stage
     */
    private function updateFlagsForStage(string $stage): void
    {
        switch ($stage) {
            case self::STAGE_DISABLED:
                $this->flags = [
                    self::FLAG_USE_NEW_USERS_TABLE => false,
                    self::FLAG_USE_NEW_AUTH_COMPONENT => false,
                    self::FLAG_USE_NEW_PASSWORD_HASHER => false,
                    self::FLAG_USE_NEW_TOKEN_SYSTEM => false,
                    self::FLAG_MIGRATE_USER_ROLES => false,
                    self::FLAG_ENABLE_LEGACY_FALLBACK => true,
                    self::FLAG_LOG_AUTH_OPERATIONS => true
                ];
                break;

            case self::STAGE_SCHEMA_ONLY:
                $this->flags[self::FLAG_USE_NEW_USERS_TABLE] = false;
                $this->flags[self::FLAG_LOG_AUTH_OPERATIONS] = true;
                break;

            case self::STAGE_READ_HYBRID:
                $this->flags[self::FLAG_USE_NEW_USERS_TABLE] = true;
                $this->flags[self::FLAG_USE_NEW_AUTH_COMPONENT] = false;
                $this->flags[self::FLAG_ENABLE_LEGACY_FALLBACK] = true;
                break;

            case self::STAGE_WRITE_HYBRID:
                $this->flags[self::FLAG_USE_NEW_AUTH_COMPONENT] = true;
                $this->flags[self::FLAG_USE_NEW_PASSWORD_HASHER] = true;
                break;

            case self::STAGE_FULL_MIGRATION:
                $this->flags[self::FLAG_USE_NEW_TOKEN_SYSTEM] = true;
                $this->flags[self::FLAG_MIGRATE_USER_ROLES] = true;
                $this->flags[self::FLAG_ENABLE_LEGACY_FALLBACK] = false;
                break;

            case self::STAGE_LEGACY_CLEANUP:
                // All new flags enabled, legacy disabled
                foreach ($this->flags as $flag => $value) {
                    if ($flag !== self::FLAG_ENABLE_LEGACY_FALLBACK && $flag !== self::FLAG_LOG_AUTH_OPERATIONS) {
                        $this->flags[$flag] = true;
                    }
                }
                $this->flags[self::FLAG_ENABLE_LEGACY_FALLBACK] = false;
                break;
        }
    }

    /**
     * Check if stage is valid
     */
    private function isValidStage(string $stage): bool
    {
        return in_array($stage, [
            self::STAGE_DISABLED,
            self::STAGE_SCHEMA_ONLY,
            self::STAGE_READ_HYBRID,
            self::STAGE_WRITE_HYBRID,
            self::STAGE_FULL_MIGRATION,
            self::STAGE_LEGACY_CLEANUP
        ]);
    }

    // Readiness check methods
    private function checkDatabaseBackup(): bool
    {
        // Implementation would check for recent backup
        return true; // Placeholder
    }

    private function checkLegacyAuth(): bool
    {
        // Implementation would test CakePHP 3 auth system
        return true; // Placeholder
    }

    private function checkNewSchema(): bool
    {
        // Implementation would verify new schema exists
        return true; // Placeholder
    }

    private function checkDataMigration(): bool
    {
        // Implementation would verify migration scripts
        return true; // Placeholder
    }

    private function checkRollbackScript(): bool
    {
        // Implementation would verify rollback procedures
        return true; // Placeholder
    }

    private function checkMonitoring(): bool
    {
        // Implementation would check monitoring setup
        return true; // Placeholder
    }
}