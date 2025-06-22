<?php

declare(strict_types=1);

namespace Framework\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Represents a single route in the application.
 *
 * Holds the HTTP method, path, and handler for the route.
 */
final class Route {

    private string $method;
    private string $path;
    private RequestHandlerInterface $handler;

    public function __construct(string $method, string $path, RequestHandlerInterface $handler)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * Checks if this route matches the given request.
     */
    public function matches(ServerRequestInterface $request): bool {
        return $this->method === strtoupper($request->getMethod())
            && $this->path === $request->getUri()->getPath();
    }

    public function getHandler(): RequestHandlerInterface {

    return $this->handler;
    }
}