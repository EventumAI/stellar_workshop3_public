<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\AuthMigrationFeatureFlag;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Authentication Migration Command
 *
 * Command line interface for managing authentication migration stages and feature flags
 */
class AuthMigrationCommand extends Command
{
    private AuthMigrationFeatureFlag $featureFlag;

    public function initialize(): void
    {
        parent::initialize();
        $this->featureFlag = new AuthMigrationFeatureFlag($this->getLogger());
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->setDescription('Manage authentication migration from CakePHP 3 to CakePHP 5');

        $parser->addSubcommand('status', [
            'help' => 'Show current migration status and progress'
        ]);

        $parser->addSubcommand('stage', [
            'help' => 'Set migration stage',
            'parser' => [
                'arguments' => [
                    'stage' => [
                        'help' => 'Migration stage to set',
                        'required' => true,
                        'choices' => [
                            AuthMigrationFeatureFlag::STAGE_DISABLED,
                            AuthMigrationFeatureFlag::STAGE_SCHEMA_ONLY,
                            AuthMigrationFeatureFlag::STAGE_READ_HYBRID,
                            AuthMigrationFeatureFlag::STAGE_WRITE_HYBRID,
                            AuthMigrationFeatureFlag::STAGE_FULL_MIGRATION,
                            AuthMigrationFeatureFlag::STAGE_LEGACY_CLEANUP
                        ]
                    ]
                ]
            ]
        ]);

        $parser->addSubcommand('flag', [
            'help' => 'Manage individual feature flags',
            'parser' => [
                'arguments' => [
                    'flag' => [
                        'help' => 'Feature flag name',
                        'required' => true
                    ],
                    'value' => [
                        'help' => 'true or false',
                        'required' => true,
                        'choices' => ['true', 'false']
                    ]
                ]
            ]
        ]);

        $parser->addSubcommand('rollback', [
            'help' => 'Emergency rollback to disabled state',
            'parser' => [
                'options' => [
                    'confirm' => [
                        'help' => 'Confirm emergency rollback',
                        'boolean' => true,
                        'required' => true
                    ]
                ]
            ]
        ]);

        $parser->addSubcommand('readiness', [
            'help' => 'Check migration readiness'
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $subcommand = $args->getArgumentAt(0);

        switch ($subcommand) {
            case 'status':
                return $this->showStatus($io);

            case 'stage':
                $stage = $args->getArgumentAt(1);
                return $this->setStage($stage, $io);

            case 'flag':
                $flag = $args->getArgumentAt(1);
                $value = $args->getArgumentAt(2) === 'true';
                return $this->setFlag($flag, $value, $io);

            case 'rollback':
                $confirm = $args->getOption('confirm');
                return $this->emergencyRollback($confirm, $io);

            case 'readiness':
                return $this->checkReadiness($io);

            default:
                $io->error('Unknown subcommand. Use --help for usage information.');
                return static::CODE_ERROR;
        }
    }

    /**
     * Show current migration status
     */
    private function showStatus(ConsoleIo $io): int
    {
        $io->out('<info>Authentication Migration Status</info>');
        $io->hr();

        $stage = $this->featureFlag->getCurrentStage();
        $progress = $this->featureFlag->getMigrationProgress();
        $rollbackSafe = $this->featureFlag->isRollbackSafe();

        $io->out("Current Stage: <warning>{$stage}</warning>");
        $io->out("Progress: <info>{$progress}%</info>");
        $io->out("Rollback Safe: " . ($rollbackSafe ? '<success>Yes</success>' : '<error>No</error>'));

        $io->out("\n<info>Feature Flags:</info>");
        $flags = [
            AuthMigrationFeatureFlag::FLAG_USE_NEW_USERS_TABLE,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_AUTH_COMPONENT,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_PASSWORD_HASHER,
            AuthMigrationFeatureFlag::FLAG_USE_NEW_TOKEN_SYSTEM,
            AuthMigrationFeatureFlag::FLAG_MIGRATE_USER_ROLES,
            AuthMigrationFeatureFlag::FLAG_ENABLE_LEGACY_FALLBACK,
            AuthMigrationFeatureFlag::FLAG_LOG_AUTH_OPERATIONS
        ];

        foreach ($flags as $flag) {
            $enabled = $this->featureFlag->isEnabled($flag);
            $status = $enabled ? '<success>enabled</success>' : '<error>disabled</error>';
            $io->out("  {$flag}: {$status}");
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Set migration stage
     */
    private function setStage(string $stage, ConsoleIo $io): int
    {
        try {
            $currentStage = $this->featureFlag->getCurrentStage();

            if ($currentStage === $stage) {
                $io->warning("Already at stage: {$stage}");
                return static::CODE_SUCCESS;
            }

            $io->warning("Changing migration stage from '{$currentStage}' to '{$stage}'");

            if (!$io->askChoice('Continue?', ['y', 'n'], 'n') === 'y') {
                $io->out('Cancelled.');
                return static::CODE_SUCCESS;
            }

            $this->featureFlag->setStage($stage);
            $io->success("Migration stage set to: {$stage}");

            // Show updated status
            return $this->showStatus($io);

        } catch (\Exception $e) {
            $io->error("Failed to set stage: " . $e->getMessage());
            return static::CODE_ERROR;
        }
    }

    /**
     * Set individual feature flag
     */
    private function setFlag(string $flag, bool $value, ConsoleIo $io): int
    {
        $this->featureFlag->setFlag($flag, $value);
        $status = $value ? 'enabled' : 'disabled';
        $io->success("Feature flag '{$flag}' {$status}");

        return static::CODE_SUCCESS;
    }

    /**
     * Emergency rollback
     */
    private function emergencyRollback(bool $confirm, ConsoleIo $io): int
    {
        if (!$confirm) {
            $io->error('Emergency rollback requires --confirm flag');
            return static::CODE_ERROR;
        }

        $io->warning('EMERGENCY ROLLBACK: This will disable all authentication migration features');

        if (!$io->askChoice('Are you absolutely sure?', ['YES', 'no'], 'no') === 'YES') {
            $io->out('Cancelled.');
            return static::CODE_SUCCESS;
        }

        $this->featureFlag->emergencyRollback();
        $io->success('Emergency rollback completed. All migration features disabled.');

        return $this->showStatus($io);
    }

    /**
     * Check migration readiness
     */
    private function checkReadiness(ConsoleIo $io): int
    {
        $io->out('<info>Migration Readiness Check</info>');
        $io->hr();

        $checklist = $this->featureFlag->getReadinessChecklist();
        $allReady = true;

        foreach ($checklist as $check => $ready) {
            $status = $ready ? '<success>✓</success>' : '<error>✗</error>';
            $io->out("  {$status} {$check}");
            if (!$ready) {
                $allReady = false;
            }
        }

        $io->hr();
        if ($allReady) {
            $io->success('All readiness checks passed. Migration can proceed.');
        } else {
            $io->error('Some readiness checks failed. Address issues before migration.');
        }

        return $allReady ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}