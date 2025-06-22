<?php


namespace Framework\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Framework\Http\Response;
use Framework\Http\Stream;

/**
 * Middleware for catching unhandled exceptions and returning a generic error response.
 */
class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * Wraps the request handling in a try-catch block to handle any exceptions.
     *
     * Returns a 500 response with the error message if something goes wrong.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $body = Stream::fromString("Internal server error: " . $e->getMessage());
            return new Response(500, ['Content-Type' => ['text/plain']], $body);
        }
    }
}




