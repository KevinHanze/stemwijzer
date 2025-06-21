<?php


namespace App\Controller;

use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): Response
    {
        unset($_SESSION['user_id']);

        return new Response(
            302,
            ['Location' => ['/']],
            Stream::fromString("Redirecting...")
        );
    }
}