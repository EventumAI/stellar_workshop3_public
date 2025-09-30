<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\FeatureFlagService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Feature Flag Command
 *
 * CLI interface for managing feature flags during migration.
 * Provides safe controls for authentication migration phases.
 */
class FeatureFlagCommand extends Command
{
    private FeatureFlagService $featureFlags;

    public function initialize(): void
    {
        parent::initialize();
        $this->featureFlags = new FeatureFlagService();
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Manage feature flags for QuickAppsCMS migration')
            ->addSubcommand('list', [
                'help' => 'List all feature flags and their status',
            ])
            ->addSubcommand('enable', [
                'help' => 'Enable a feature flag',
                'parser' => [
                    'arguments' => [
                        'flag' => ['help' => 'Feature flag name', 'required' => true],
                    ],
                ],
            ])
            ->addSubcommand('disable', [
                'help' => 'Disable a feature flag',
                'parser' => [
                    'arguments' => [
                        'flag' => ['help' => 'Feature flag name', 'required' => true],
                    ],
                ],
            ])
            ->addSubcommand('auth-init', [
                'help' => 'Initialize authentication migration feature flags',
            ])
            ->addSubcommand('auth-progress', [
                'help' => 'Progress authentication migration to next phase',
                'parser' => [
                    'arguments' => [
                        'phase' => ['help' => 'Migration phase (1-5)', 'required' => true],
                    ],
                ],
            ])
            ->addSubcommand('auth-status', [
                'help' => 'Show authentication migration status',
            ])
            ->addSubcommand('emergency-rollback', [
                'help' => 'Emergency rollback of authentication migration',
                'parser' => [
                    'options' => [
                        'confirm' => [
                            'help' => 'Confirm the rollback action',
                            'boolean' => true,
                        ],
                    ],
                ],
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $subcommand = $args->getArgumentAt(0);

        switch ($subcommand) {
            case 'list':
                return $this->listFlags($io);

            case 'enable':
                return $this->enableFlag($args, $io);

            case 'disable':
                return $this->disableFlag($args, $io);

            case 'auth-init':
                return $this->initAuthMigration($io);

            case 'auth-progress':
                return $this->progressAuthMigration($args, $io);

            case 'auth-status':
                return $this->showAuthStatus($io);

            case 'emergency-rollback':
                return $this->emergencyRollback($args, $io);

            default:
                $io->error('Invalid subcommand. Use --help for available commands.');
                return static::CODE_ERROR;
        }
    }

    protected function listFlags(ConsoleIo $io): int
    {
        $flags = $this->featureFlags->getAllFlags();

        if (empty($flags)) {
            $io->info('No feature flags configured.');
            return static::CODE_SUCCESS;
        }

        $io->out('<info>Feature Flags Status:</info>');
        $io->hr();

        foreach ($flags as $flag => $enabled) {
            $status = $enabled ? '<success>ENABLED</success>' : '<warning>DISABLED</warning>';
            $io->out(sprintf('%-40s %s', $flag, $status));
        }

        return static::CODE_SUCCESS;
    }

    protected function enableFlag(Arguments $args, ConsoleIo $io): int
    {
        $flag = $args->getArgument('flag');

        if ($this->featureFlags->enable($flag)) {
            $io->success("Feature flag '{$flag}' enabled successfully.");
            return static::CODE_SUCCESS;
        } else {
            $io->error("Failed to enable feature flag '{$flag}'.");
            return static::CODE_ERROR;
        }
    }

    protected function disableFlag(Arguments $args, ConsoleIo $io): int
    {
        $flag = $args->getArgument('flag');

        if ($this->featureFlags->disable($flag)) {
            $io->success("Feature flag '{$flag}' disabled successfully.");
            return static::CODE_SUCCESS;
        } else {
            $io->error("Failed to disable feature flag '{$flag}'.");
            return static::CODE_ERROR;
        }
    }

    protected function initAuthMigration(ConsoleIo $io): int
    {
        $io->out('<info>Initializing authentication migration feature flags...</info>');

        $this->featureFlags->initializeAuthMigrationFlags();

        $io->success('Authentication migration feature flags initialized successfully.');
        $this->showAuthStatus($io);

        return static::CODE_SUCCESS;
    }

    protected function progressAuthMigration(Arguments $args, ConsoleIo $io): int
    {
        $phase = (int) $args->getArgument('phase');

        if ($phase < 1 || $phase > 5) {
            $io->error('Phase must be between 1 and 5.');
            return static::CODE_ERROR;
        }

        $io->out("<warning>Progressing authentication migration to phase {$phase}...</warning>");

        // Show current status first
        $this->showAuthStatus($io);

        $io->out('');
        $phaseDescriptions = [
            1 => 'Enable monitoring and dual-write mode',
            2 => 'Enable cookie migration',
            3 => 'Enable token migration',
            4 => 'Switch to CakePHP 5 authentication (CRITICAL)',
            5 => 'Complete migration - disable dual write',
        ];

        $io->out("<info>Phase {$phase}: {$phaseDescriptions[$phase]}</info>");

        if ($phase === 4) {
            $io->warning('⚠️  CRITICAL PHASE: This will switch to CakePHP 5 authentication!');
            if (!$io->askChoice('Are you sure you want to proceed?', ['y', 'n'], 'n') === 'y') {
                $io->info('Migration phase cancelled.');
                return static::CODE_SUCCESS;
            }
        }

        try {
            if ($this->featureFlags->progressAuthMigration($phase)) {
                $io->success("Successfully progressed to migration phase {$phase}.");
                $this->showAuthStatus($io);
                return static::CODE_SUCCESS;
            } else {
                $io->error("Failed to progress to migration phase {$phase}.");
                return static::CODE_ERROR;
            }
        } catch (\Exception $e) {
            $io->error("Migration phase failed: " . $e->getMessage());
            return static::CODE_ERROR;
        }
    }

    protected function showAuthStatus(ConsoleIo $io): int
    {
        $flags = $this->featureFlags->getAuthMigrationFlags();

        $io->out('<info>Authentication Migration Status:</info>');
        $io->hr();

        foreach ($flags as $flag => $enabled) {
            $status = $enabled ? '<success>✓ ENABLED</success>' : '<error>✗ DISABLED</error>';
            $cleanFlag = str_replace('auth.', '', $flag);
            $io->out(sprintf('%-25s %s', $cleanFlag, $status));
        }

        // Determine current phase
        $phase = $this->determineCurrentPhase($flags);
        $io->out('');
        $io->out("<info>Current Phase: {$phase}</info>");

        return static::CODE_SUCCESS;
    }

    protected function emergencyRollback(Arguments $args, ConsoleIo $io): int
    {
        if (!$args->getOption('confirm')) {
            $io->error('Emergency rollback requires --confirm flag for safety.');
            $io->warning('This will immediately disable all authentication migration features.');
            $io->out('Use: bin/cake feature_flag emergency-rollback --confirm');
            return static::CODE_ERROR;
        }

        $io->warning('🚨 EMERGENCY ROLLBACK: Reverting authentication migration...');

        if ($this->featureFlags->emergencyRollback()) {
            $io->success('Emergency rollback completed successfully.');
            $this->showAuthStatus($io);
            return static::CODE_SUCCESS;
        } else {
            $io->error('Emergency rollback failed - check logs and consider manual intervention.');
            return static::CODE_ERROR;
        }
    }

    private function determineCurrentPhase(array $flags): string
    {
        if (!$flags[FeatureFlagService::FLAG_AUTH_DUAL_WRITE_MODE]) {
            return '0 - Not Started';
        }

        if (!$flags[FeatureFlagService::FLAG_AUTH_COOKIE_MIGRATION]) {
            return '1 - Dual Write Enabled';
        }

        if (!$flags[FeatureFlagService::FLAG_AUTH_TOKEN_MIGRATION]) {
            return '2 - Cookie Migration Enabled';
        }

        if (!$flags[FeatureFlagService::FLAG_AUTH_USE_CAKEPHP5_AUTH]) {
            return '3 - Token Migration Enabled';
        }

        if ($flags[FeatureFlagService::FLAG_AUTH_DUAL_WRITE_MODE]) {
            return '4 - CakePHP 5 Auth Active (Dual Write)';
        }

        return '5 - Migration Complete';
    }
}