<?php

namespace Framework\Http\Middleware;

use Framework\AccessControl\AuthenticationInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware that handles user authentication.
 *
 * Adds the authenticated user to the request attributes.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    private AuthenticationInterface $authentication;

    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Authenticates the request and adds the user to the request attributes.
     *
     * Passes the request along the middleware stack.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Attempt to authenticate the incoming request
        $user = $this->authentication->authenticate($request);

        // Attach the authenticated user to the request
        $request = $request->withAttribute('user', $user);

        // Forward the request to the next middleware or handler
        return $handler->handle($request);
    }
}
