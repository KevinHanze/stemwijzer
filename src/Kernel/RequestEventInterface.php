<?php

namespace Framework\Kernel;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * An event that is dispatched to handle a request in the kernel.
 */
interface RequestEventInterface extends StoppableEventInterface
{
    /**
     * Get the request that is being handled.
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface;

    /**
     * Set a new request for subsequent event handlers.
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void;

    /**
     * Set a response and stop event propagation.
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void;
}