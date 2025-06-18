<?php

declare(strict_types=1);

// Autoload composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\RequestFactory;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Templating\TemplateEngine;
use Framework\Routing\Router;
use Framework\Kernel\Kernel;
use Framework\Routing\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

$request = RequestFactory::fromGlobals();

// Initialiseert de template engine
$view = new TemplateEngine(__DIR__ . '/../templates');

$router = new Router();

$router->addRoute('GET', '/hello', new class($view) implements RequestHandlerInterface {
    private TemplateEngine $view;

    public function __construct(TemplateEngine $view)
    {
        $this->view = $view;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Render de HTML uit het template
        $html = $this->view->render('test.html',
            name: 'Kevin',
            loggedIn: false,
            partijen: ['Partij A', 'Partij B', 'Partij C']
        );

        return new Response(
            200,
            ['Content-Type' => ['text/html']],
            Stream::fromString($html)
        );
    }
});

$kernel = new Kernel($router);

try {
    $response = $kernel->handle($request);
} catch (NotFoundException $e) {
    $response = new Response(
        404,
        ['Content-Type' => ['text/plain']],
        Stream::fromString("Pagina niet gevonden")
    );
} catch (\Throwable $e) {
    $response = new Response(
        500,
        ['Content-Type' => ['text/plain']],
        Stream::fromString("Interne serverfout:\n" . $e->getMessage())
    );
}

http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
