<?php

namespace Framework\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware that ensures a PHP session is started before handling the request.
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * Starts the session if one isn't already active, then continues request handling.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $handler->handle($request);
    }
}

