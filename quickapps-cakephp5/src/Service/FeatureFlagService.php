<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;
use InvalidArgumentException;

/**
 * Feature Flag Service for QuickAppsCMS Migration
 *
 * Provides safe feature flag system for gradual migration from CakePHP 3 to 5.
 * Integrates with QuickAppsCMS snapshot configuration system for persistence.
 */
class FeatureFlagService
{
    use LogTrait;

    private Connection $connection;
    private string $cacheConfig = 'default';
    private string $cachePrefix = 'feature_flags';
    private array $flags = [];

    // Authentication migration feature flags
    public const FLAG_AUTH_MIGRATION_ENABLED = 'auth.migration.enabled';
    public const FLAG_AUTH_USE_CAKEPHP5_AUTH = 'auth.use_cakephp5_auth';
    public const FLAG_AUTH_DUAL_WRITE_MODE = 'auth.dual_write_mode';
    public const FLAG_AUTH_COOKIE_MIGRATION = 'auth.cookie_migration';
    public const FLAG_AUTH_TOKEN_MIGRATION = 'auth.token_migration';
    public const FLAG_AUTH_PERMISSION_CACHE = 'auth.permission_cache';

    // Database migration feature flags
    public const FLAG_DB_USE_MYSQL8_FEATURES = 'db.use_mysql8_features';
    public const FLAG_DB_ENFORCE_FK_CONSTRAINTS = 'db.enforce_fk_constraints';

    // Safety feature flags
    public const FLAG_MIGRATION_ROLLBACK_ENABLED = 'migration.rollback_enabled';
    public const FLAG_MIGRATION_MONITORING = 'migration.monitoring';

    public function __construct(?Connection $connection = null)
    {
        $this->connection = $connection ?: ConnectionManager::get('default');
        $this->loadFlags();
    }

    /**
     * Check if a feature flag is enabled
     */
    public function isEnabled(string $flag): bool
    {
        $cacheKey = $this->cachePrefix . '.' . $flag;

        $value = Cache::read($cacheKey, $this->cacheConfig);
        if ($value !== false) {
            return (bool) $value;
        }

        // Fallback to database/configuration
        $value = $this->getFromSource($flag);
        Cache::write($cacheKey, $value, $this->cacheConfig);

        return (bool) $value;
    }

    /**
     * Enable a feature flag
     */
    public function enable(string $flag): bool
    {
        return $this->setFlag($flag, true);
    }

    /**
     * Disable a feature flag
     */
    public function disable(string $flag): bool
    {
        return $this->setFlag($flag, false);
    }

    /**
     * Set flag value with validation and logging
     */
    public function setFlag(string $flag, bool $value): bool
    {
        $this->validateFlag($flag);

        try {
            // Persist to database via QuickAppsCMS options table
            $this->persistFlag($flag, $value);

            // Update cache
            $cacheKey = $this->cachePrefix . '.' . $flag;
            Cache::write($cacheKey, $value, $this->cacheConfig);

            // Update local flags array
            $this->flags[$flag] = $value;

            $this->log('info', "Feature flag '{$flag}' set to " . ($value ? 'enabled' : 'disabled'));

            return true;

        } catch (\Exception $e) {
            $this->log('error', "Failed to set feature flag '{$flag}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all feature flags and their states
     */
    public function getAllFlags(): array
    {
        return $this->flags;
    }

    /**
     * Get feature flags for authentication migration specifically
     */
    public function getAuthMigrationFlags(): array
    {
        return [
            self::FLAG_AUTH_MIGRATION_ENABLED => $this->isEnabled(self::FLAG_AUTH_MIGRATION_ENABLED),
            self::FLAG_AUTH_USE_CAKEPHP5_AUTH => $this->isEnabled(self::FLAG_AUTH_USE_CAKEPHP5_AUTH),
            self::FLAG_AUTH_DUAL_WRITE_MODE => $this->isEnabled(self::FLAG_AUTH_DUAL_WRITE_MODE),
            self::FLAG_AUTH_COOKIE_MIGRATION => $this->isEnabled(self::FLAG_AUTH_COOKIE_MIGRATION),
            self::FLAG_AUTH_TOKEN_MIGRATION => $this->isEnabled(self::FLAG_AUTH_TOKEN_MIGRATION),
            self::FLAG_AUTH_PERMISSION_CACHE => $this->isEnabled(self::FLAG_AUTH_PERMISSION_CACHE),
        ];
    }

    /**
     * Initialize default flags for auth migration
     */
    public function initializeAuthMigrationFlags(): void
    {
        $defaults = [
            self::FLAG_AUTH_MIGRATION_ENABLED => false,     // Master switch - MUST be false initially
            self::FLAG_AUTH_USE_CAKEPHP5_AUTH => false,     // Use new auth system
            self::FLAG_AUTH_DUAL_WRITE_MODE => false,       // Write to both old/new tables
            self::FLAG_AUTH_COOKIE_MIGRATION => false,      // Migrate remember-me cookies
            self::FLAG_AUTH_TOKEN_MIGRATION => false,       // Migrate API tokens
            self::FLAG_AUTH_PERMISSION_CACHE => true,       // Enable permission caching (safe)
            self::FLAG_DB_USE_MYSQL8_FEATURES => false,     // Use MySQL 8.0 specific features
            self::FLAG_DB_ENFORCE_FK_CONSTRAINTS => false,  // Enforce foreign key constraints
            self::FLAG_MIGRATION_ROLLBACK_ENABLED => true,  // Allow rollback (SAFETY)
            self::FLAG_MIGRATION_MONITORING => true,        // Enable migration monitoring
        ];

        foreach ($defaults as $flag => $value) {
            if (!$this->flagExists($flag)) {
                $this->setFlag($flag, $value);
            }
        }

        $this->log('info', 'Authentication migration feature flags initialized');
    }

    /**
     * Safe migration progression for authentication
     */
    public function progressAuthMigration(int $phase): bool
    {
        $this->log('info', "Starting auth migration phase {$phase}");

        switch ($phase) {
            case 1: // Enable monitoring and dual-write mode
                return $this->setFlag(self::FLAG_AUTH_DUAL_WRITE_MODE, true) &&
                       $this->setFlag(self::FLAG_MIGRATION_MONITORING, true);

            case 2: // Enable cookie migration
                if (!$this->isEnabled(self::FLAG_AUTH_DUAL_WRITE_MODE)) {
                    throw new InvalidArgumentException('Cannot progress to phase 2 without dual-write mode');
                }
                return $this->setFlag(self::FLAG_AUTH_COOKIE_MIGRATION, true);

            case 3: // Enable token migration
                return $this->setFlag(self::FLAG_AUTH_TOKEN_MIGRATION, true);

            case 4: // Switch to CakePHP 5 auth (CRITICAL PHASE)
                return $this->setFlag(self::FLAG_AUTH_USE_CAKEPHP5_AUTH, true);

            case 5: // Complete migration - disable dual write
                if (!$this->isEnabled(self::FLAG_AUTH_USE_CAKEPHP5_AUTH)) {
                    throw new InvalidArgumentException('Cannot complete migration without CakePHP 5 auth enabled');
                }
                return $this->setFlag(self::FLAG_AUTH_DUAL_WRITE_MODE, false) &&
                       $this->setFlag(self::FLAG_AUTH_MIGRATION_ENABLED, false);

            default:
                throw new InvalidArgumentException("Invalid migration phase: {$phase}");
        }
    }

    /**
     * Emergency rollback for authentication migration
     */
    public function emergencyRollback(): bool
    {
        if (!$this->isEnabled(self::FLAG_MIGRATION_ROLLBACK_ENABLED)) {
            $this->log('error', 'Emergency rollback attempted but rollback is disabled');
            return false;
        }

        $this->log('warning', 'EMERGENCY ROLLBACK: Reverting all auth migration flags');

        // Disable all migration features immediately
        $rollbackFlags = [
            self::FLAG_AUTH_USE_CAKEPHP5_AUTH => false,
            self::FLAG_AUTH_DUAL_WRITE_MODE => false,
            self::FLAG_AUTH_COOKIE_MIGRATION => false,
            self::FLAG_AUTH_TOKEN_MIGRATION => false,
        ];

        $success = true;
        foreach ($rollbackFlags as $flag => $value) {
            if (!$this->setFlag($flag, $value)) {
                $success = false;
            }
        }

        if ($success) {
            $this->log('info', 'Emergency rollback completed successfully');
        } else {
            $this->log('error', 'Emergency rollback partially failed - manual intervention required');
        }

        return $success;
    }

    /**
     * Load all flags from persistent storage
     */
    private function loadFlags(): void
    {
        try {
            // Check if options table exists (QuickAppsCMS pattern)
            $query = $this->connection->selectQuery()
                ->select(['name', 'value'])
                ->from('options')
                ->where(['name LIKE' => 'feature_flag_%']);

            $results = $query->execute()->fetchAll('assoc');

            foreach ($results as $row) {
                $flag = str_replace('feature_flag_', '', $row['name']);
                $this->flags[$flag] = (bool) json_decode($row['value']);
            }

        } catch (\Exception $e) {
            $this->log('warning', 'Could not load feature flags from database: ' . $e->getMessage());
            $this->flags = [];
        }
    }

    /**
     * Get flag value from database or configuration
     */
    private function getFromSource(string $flag): bool
    {
        // Try database first (QuickAppsCMS pattern)
        try {
            $query = $this->connection->selectQuery()
                ->select(['value'])
                ->from('options')
                ->where(['name' => 'feature_flag_' . $flag]);

            $result = $query->execute()->fetch('assoc');
            if ($result) {
                return (bool) json_decode($result['value']);
            }
        } catch (\Exception $e) {
            $this->log('debug', "Could not read flag '{$flag}' from database: " . $e->getMessage());
        }

        // Fallback to Configure (snapshot integration)
        return (bool) Configure::read("FeatureFlags.{$flag}", false);
    }

    /**
     * Persist flag to database
     */
    private function persistFlag(string $flag, bool $value): void
    {
        $data = [
            'name' => 'feature_flag_' . $flag,
            'value' => json_encode($value),
            'autoload' => 0, // Don't autoload feature flags
        ];

        // Use UPSERT pattern for options table
        $query = $this->connection->insertQuery('options', $data);
        $query->clause('ON DUPLICATE KEY UPDATE value = VALUES(value)');
        $query->execute();
    }

    /**
     * Check if flag exists in storage
     */
    private function flagExists(string $flag): bool
    {
        try {
            $query = $this->connection->selectQuery()
                ->select(['COUNT(*) as count'])
                ->from('options')
                ->where(['name' => 'feature_flag_' . $flag]);

            $result = $query->execute()->fetch('assoc');
            return $result['count'] > 0;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate flag name
     */
    private function validateFlag(string $flag): void
    {
        if (empty($flag)) {
            throw new InvalidArgumentException('Flag name cannot be empty');
        }

        if (strlen($flag) > 100) {
            throw new InvalidArgumentException('Flag name too long');
        }

        if (!preg_match('/^[a-z0-9._]+$/', $flag)) {
            throw new InvalidArgumentException('Flag name contains invalid characters');
        }
    }
}