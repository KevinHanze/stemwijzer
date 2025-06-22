<?php

declare(strict_types=1);

namespace Framework\Kernel;

use Framework\Routing\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Core framework kernel that handles incoming HTTP requests.
 *
 * Builds and executes the middleware stack, ending with routing resolution.
 */
final class Kernel implements KernelInterface
{
    private RouterInterface $router;
    private array $middleware = [];

    public function __construct(RouterInterface $router, array $middleware = [])
    {
        $this->router = $router;
        $this->middleware = $middleware;
    }

    /**
     * Entry point for handling a request.
     *
     * Builds the middleware stack and passes the request through it.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->buildMiddlewareStack($this->middleware, function (ServerRequestInterface $req) {
            return $this->router->route($req)->handle($req);
        });

        return $handler($request);
    }

    /**
     * Wraps all middleware around the final handler in reverse order.
     *
     * Each middleware gets a RequestHandler that continues the chain.
     */
    private function buildMiddlewareStack(array $middleware, callable $finalHandler): callable
    {
        $handler = $finalHandler;

        foreach (array_reverse($middleware) as $mw) {
            $next = $handler;
            $handler = fn(ServerRequestInterface $req) => $mw->process($req, new class($next) implements RequestHandlerInterface {
                private $next;
                public function __construct(callable $next) { $this->next = $next; }
                public function handle(ServerRequestInterface $req): ResponseInterface {
                    return ($this->next)($req);
                }
            });
        }

        return $handler;
    }
}