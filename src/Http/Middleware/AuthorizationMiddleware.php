<?php

namespace Framework\Http\Middleware;

use Framework\AccessControl\AuthorizationInterface;
use Framework\AccessControl\AccessDeniedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware that checks if the user has permission to access the route.
 *
 * If a permission is defined for the current path, it ensures the user is authorized.
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthorizationInterface $auth,
        private array $routePermissions = []
    )
    {}

    /**
     * Checks route-level permissions and denies access if not granted.
     *
     * @throws AccessDeniedException If the user lacks the required permission
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $permission = $this->routePermissions[$path] ?? null;

        // Only check authorization if a permission is configured for the route
        if ($permission) {
            $user = $request->getAttribute('user');
            $this->auth->denyUnlessGranted($user, $permission);
        }

        return $handler->handle($request);
    }
}
