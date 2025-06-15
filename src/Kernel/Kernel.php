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
        // Vraag router om juiste controller (doet nog niks tot router is geimplementeerd)
        $handler = $this->router->route($request);

        // Roep controller aan en geef response terug
        return $handler->handle($request);
    }
}