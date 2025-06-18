<?php

declare(strict_types=1);

namespace Framework\Kernel;

use Framework\Routing\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Kernel implements KernelInterface
{
    private RouterInterface $router;
    private array $middleware = [];

    public function __construct(RouterInterface $router, array $middleware = [])
    {
        $this->router = $router;
        $this->middleware = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->buildMiddlewareStack($this->middleware, function (ServerRequestInterface $req) {
            return $this->router->route($req)->handle($req);
        });

        return $handler($request);
    }

    private function buildMiddlewareStack(array $middleware, callable $finalHandler): callable
    {
        //"echte" handler komt als laatste in de stack
        $handler = $finalHandler;

        //bouw middleware chain op (in reverse om volgorde te behouden) en voert proces methode aan voor alle middleware
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