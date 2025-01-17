<?php

namespace Framework\Kernel;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An event that is dispatched when an exception occurs while handling a request in the kernel.
 */
interface ExceptionEventInterface extends StoppableEventInterface
{
    /**
     * Get the exception that was thrown.
     * @return \Throwable
     */
    public function getThrowable(): \Throwable;

    /**
     * Set a response and stop event propagation.
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void;
}