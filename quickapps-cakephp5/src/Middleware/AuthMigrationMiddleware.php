<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\AuthMigrationFeatureFlag;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Authentication Migration Middleware
 *
 * Handles progressive authentication system migration with monitoring and safety checks
 */
class AuthMigrationMiddleware implements MiddlewareInterface
{
    private AuthMigrationFeatureFlag $featureFlag;
    private LoggerInterface $logger;

    public function __construct(AuthMigrationFeatureFlag $featureFlag, LoggerInterface $logger)
    {
        $this->featureFlag = $featureFlag;
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Add feature flag service to request for controllers
        $request = $request->withAttribute('authMigrationFlags', $this->featureFlag);

        // Monitor authentication attempts during migration
        if ($this->featureFlag->isEnabled(AuthMigrationFeatureFlag::FLAG_LOG_AUTH_OPERATIONS)) {
            $this->logAuthOperation($request);
        }

        // Check for emergency conditions
        if ($this->shouldTriggerEmergencyRollback($request)) {
            $this->featureFlag->emergencyRollback();
            $this->logger->critical('Emergency rollback triggered by middleware');
        }

        // Add migration status headers for debugging
        $response = $handler->handle($request);

        return $this->addMigrationHeaders($response);
    }

    /**
     * Log authentication operations for monitoring
     */
    private function logAuthOperation(ServerRequestInterface $request): void
    {
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Log auth-related endpoints
        if ($this->isAuthEndpoint($uri)) {
            $this->logger->info('Auth operation during migration', [
                'method' => $method,
                'uri' => $uri,
                'stage' => $this->featureFlag->getCurrentStage(),
                'user_agent' => $request->getHeaderLine('User-Agent'),
                'ip' => $request->clientIp()
            ]);
        }
    }

    /**
     * Check if emergency rollback should be triggered
     */
    private function shouldTriggerEmergencyRollback(ServerRequestInterface $request): bool
    {
        // Emergency rollback conditions
        $emergencyParam = $request->getQuery('emergency_rollback');
        $adminRequest = $this->isAdminRequest($request);

        return $emergencyParam === 'true' && $adminRequest;
    }

    /**
     * Add migration status headers
     */
    private function addMigrationHeaders(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('X-Auth-Migration-Stage', $this->featureFlag->getCurrentStage())
            ->withHeader('X-Auth-Migration-Progress', (string)$this->featureFlag->getMigrationProgress())
            ->withHeader('X-Auth-Rollback-Safe', $this->featureFlag->isRollbackSafe() ? 'true' : 'false');
    }

    /**
     * Check if URI is auth-related
     */
    private function isAuthEndpoint(string $uri): bool
    {
        $authPatterns = [
            '/admin/login',
            '/admin/logout',
            '/admin/user',
            '/user/login',
            '/user/logout',
            '/user/register',
            '/user/forgot-password'
        ];

        foreach ($authPatterns as $pattern) {
            if (strpos($uri, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is from admin area
     */
    private function isAdminRequest(ServerRequestInterface $request): bool
    {
        $uri = $request->getUri()->getPath();
        return strpos($uri, '/admin') === 0;
    }
}