<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\RequestFactory;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Kernel\Kernel;
use Framework\Routing\Router;
use Framework\Routing\NotFoundException;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$request = RequestFactory::fromGlobals();

$router = new Router();

$helloHandler = new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface {
        return new Response(
            200,
            ['Content-Type' => ['text/plain']],
            new Stream("Hello vanaf /hello")
        );
    }
};

$router->addRoute('GET', '/hello', $helloHandler);

$kernel = new Kernel($router);

try {
    $response = $kernel->handle($request);
} catch (NotFoundException $e) {
    $response = new Response(
        404,
        ['Content-Type' => ['text/plain']],
        new Stream("Pagina niet gevonden")
    );
}

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
