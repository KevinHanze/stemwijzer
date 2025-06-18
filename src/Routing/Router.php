<?php

declare(strict_types=1);

namespace Framework\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Router implements RouterInterface
{

    private array $routes = [];

    public function addRoute(string $method, string $path, RequestHandlerInterface $handler): void {
        $this->routes[] = new Route($method, $path, $handler);
    }

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

