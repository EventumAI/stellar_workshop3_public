<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\FeatureFlagService;
use Cake\Http\MiddlewareInterface;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Feature Flag Middleware
 *
 * Injects feature flag service into request context and handles
 * migration-specific routing and authentication decisions.
 */
class FeatureFlagMiddleware implements MiddlewareInterface
{
    private FeatureFlagService $featureFlags;

    public function __construct(FeatureFlagService $featureFlags)
    {
        $this->featureFlags = $featureFlags;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Inject feature flag service into request
        $request = $request->withAttribute('featureFlags', $this->featureFlags);

        // Check if auth migration is active
        if ($this->featureFlags->isEnabled(FeatureFlagService::FLAG_AUTH_MIGRATION_ENABLED)) {
            $request = $this->handleAuthMigration($request);
        }

        // Continue with the request
        $response = $handler->handle($request);

        // Add migration status headers for debugging
        if ($this->featureFlags->isEnabled(FeatureFlagService::FLAG_MIGRATION_MONITORING)) {
            $response = $this->addMigrationHeaders($response);
        }

        return $response;
    }

    /**
     * Handle authentication migration routing
     */
    private function handleAuthMigration(ServerRequestInterface $request): ServerRequestInterface
    {
        $authFlags = $this->featureFlags->getAuthMigrationFlags();

        // Add auth migration context to request
        $request = $request->withAttribute('authMigrationFlags', $authFlags);

        // If dual write mode is enabled, flag for auth components
        if ($authFlags[FeatureFlagService::FLAG_AUTH_DUAL_WRITE_MODE]) {
            $request = $request->withAttribute('authDualWriteMode', true);
        }

        // If using CakePHP 5 auth, set the appropriate context
        if ($authFlags[FeatureFlagService::FLAG_AUTH_USE_CAKEPHP5_AUTH]) {
            $request = $request->withAttribute('useCakePHP5Auth', true);
        }

        return $request;
    }

    /**
     * Add migration status headers for monitoring
     */
    private function addMigrationHeaders(ResponseInterface $response): ResponseInterface
    {
        $authFlags = $this->featureFlags->getAuthMigrationFlags();

        $migrationStatus = [
            'auth-migration' => $authFlags[FeatureFlagService::FLAG_AUTH_MIGRATION_ENABLED] ? 'active' : 'inactive',
            'auth-system' => $authFlags[FeatureFlagService::FLAG_AUTH_USE_CAKEPHP5_AUTH] ? 'cakephp5' : 'cakephp3',
            'dual-write' => $authFlags[FeatureFlagService::FLAG_AUTH_DUAL_WRITE_MODE] ? 'enabled' : 'disabled',
        ];

        foreach ($migrationStatus as $key => $value) {
            $response = $response->withHeader("X-Migration-{$key}", $value);
        }

        return $response;
    }
}