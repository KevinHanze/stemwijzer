<?php

declare(strict_types=1);

namespace Framework\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Simple router that matches requests to registered routes.
 */
final class Router implements RouterInterface
{

    private array $routes = [];

    /**
     * Registers a new route.
     *
     * @param string $method HTTP method (e.g. GET, POST)
     * @param string $path URI path (e.g. /users)
     * @param RequestHandlerInterface $handler The handler for this route
     */
    public function addRoute(string $method, string $path, RequestHandlerInterface $handler): void {
        $this->routes[] = new Route($method, $path, $handler);
    }

    /**
     * Resolves a matching route for the given request.
     *
     * @throws NotFoundException If no matching route is found
     */
    public function route(ServerRequestInterface $request): RequestHandlerInterface
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $route->getHandler();
            }
        }
        throw new NotFoundException();
    }
}

