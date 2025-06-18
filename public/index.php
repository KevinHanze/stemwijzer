<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\RequestFactory;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Http\Middleware\SessionMiddleware;
use Framework\Routing\Router;
use Framework\Kernel\Kernel;
use Framework\Routing\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

$request = RequestFactory::fromGlobals();

$router = new Router();

$middleware = [
    new SessionMiddleware(),
];

// Route 1: sets a value in $_SESSION
$router->addRoute('GET', '/set-session', new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $_SESSION['test'] = 'Hello from session!';
        return new Response(
            200,
            ['Content-Type' => ['text/plain']],
            Stream::fromString('Session value set.')
        );
    }
});

// Route 2: reads the value from $_SESSION
$router->addRoute('GET', '/get-session', new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $value = $_SESSION['test'] ?? 'No value in session.';
        return new Response(
            200,
            ['Content-Type' => ['text/plain']],
            Stream::fromString("Session value: {$value}")
        );
    }
});

$kernel = new Kernel($router, $middleware);

try {
    $response = $kernel->handle($request);
} catch (NotFoundException $e) {
    $response = new Response(
        404,
        ['Content-Type' => ['text/plain']],
        Stream::fromString("404 Not Found")
    );
} catch (\Throwable $e) {
    $response = new Response(
        500,
        ['Content-Type' => ['text/plain']],
        Stream::fromString("Internal Server Error:\n" . $e->getMessage())
    );
}

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}
echo $response->getBody();
