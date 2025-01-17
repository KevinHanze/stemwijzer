<?php

namespace Framework\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A service that maps requests to request handlers.
 */
interface RouterInterface
{
    /**
     * Find a suitable request handler for the current request.
     * @return RequestHandlerInterface A request handler configured to correctly pass any routing parameters.
     * @throws NotFoundException if no route was found for the request.
     */
    public function route(ServerRequestInterface $request): RequestHandlerInterface;
}