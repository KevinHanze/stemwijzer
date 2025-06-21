<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\Middleware\AuthenticationMiddleware;
use Framework\Http\Middleware\AuthorizationMiddleware;
use Framework\Http\Middleware\ErrorMiddleware;
use Framework\Http\Middleware\SessionMiddleware;
use Framework\Http\RequestFactory;
use Framework\Kernel\Kernel;
use Framework\Routing\Router;
use Framework\Templating\TemplateEngine;

// Boot
$container = require __DIR__ . '/../bootstrap.php';
$request = RequestFactory::fromGlobals();

// Bouw middleware pipeline
$middleware = [
    $container->get(ErrorMiddleware::class),
    $container->get(SessionMiddleware::class),
    $container->get(AuthenticationMiddleware::class),
    $container->get(AuthorizationMiddleware::class)
];

// Set up router
$router = $container->get(Router::class);
$view = $container->get(TemplateEngine::class);

// Register routes
$routes = require __DIR__ . '/../routes.php';
$routes($router, $view);

// Kernel
$kernel = new Kernel($router, $middleware);

// Run
$response = $kernel->handle($request);

// Stuur response
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}
echo $response->getBody();




