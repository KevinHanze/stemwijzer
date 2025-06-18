<?php

namespace Framework\Http\Middleware;

use Framework\AccessControl\AuthorizationInterface;
use Framework\AccessControl\AccessDeniedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthorizationInterface $auth,
        private array $routePermissions = []
    )
    {}

    /**
     * @throws AccessDeniedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $permission = $this->routePermissions[$path] ?? null;

        if ($permission) {
            $user = $request->getAttribute('user');
            $this->auth->denyUnlessGranted($user, $permission);
        }

        return $handler->handle($request);
    }
}
