<?php

namespace Framework\Http\Middleware;

use Framework\AccessControl\AuthenticationInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private AuthenticationInterface $authentication;

    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    // Authenticate request to return user, add user to request attributes  and call next middleware or handler
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authentication->authenticate($request);

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
