<?php

namespace Framework\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $handler->handle($request);

    }
}

