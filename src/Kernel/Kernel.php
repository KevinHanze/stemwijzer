<?php

declare(strict_types=1);

namespace Framework\Kernel;

use Framework\Routing\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Kernel implements KernelInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->router->route($request);

        return $handler->handle($request);
    }
}