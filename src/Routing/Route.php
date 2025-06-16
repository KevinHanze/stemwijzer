<?php

declare(strict_types=1);

namespace Framework\Routing;

use Psr\Http\Server\RequestHandlerInterface;
use Framework\Http\Request;

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

    public function matches(Request $request): bool {
        return $this->method === strtoupper($request->getMethod())
            && $this->path === $request->getUri()->getPath();
    }

 public function getHandler(): RequestHandlerInterface {

    return $this->handler;
    }
}