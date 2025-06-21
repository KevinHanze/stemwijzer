<?php

use App\Controller\HomeController;
use Framework\AccessControl\User;
use Framework\DependencyInjection\Container;
use Framework\Database\PdoConnection;
use Framework\Http\Middleware\ErrorMiddleware;
use Framework\Routing\Router;
use Framework\AccessControl\Authentication;
use Framework\AccessControl\Authorization;
use Framework\AccessControl\UserProvider;
use Framework\Http\Middleware\SessionMiddleware;
use Framework\Http\Middleware\AuthenticationMiddleware;
use Framework\Http\Middleware\AuthorizationMiddleware;
use Framework\Templating\TemplateEngine;

$container = new Container();

// Core services
$container->set(PdoConnection::class, fn() => new PdoConnection('sqlite:' . __DIR__ . '/database.db'), true);
$container->set(Router::class, fn() => new Router(), true);
$container->set(TemplateEngine::class, fn() => new TemplateEngine(__DIR__ . '/templates'), true);

// Voeg controler toe
$container->set(HomeController::class, fn($c) => new HomeController(
    $c->get(TemplateEngine::class)
));

// Access control
$container->set(UserProvider::class, fn($c) => new UserProvider([
    1 => new User('kevin', password_hash('test', PASSWORD_DEFAULT), ['user']),
    2 => new User('admin', password_hash('admin', PASSWORD_DEFAULT), ['admin']),
    3 => new User('party', password_hash('party', PASSWORD_DEFAULT), ['party'])
]));
$container->set(Authentication::class, fn($c) => new Authentication($c->get(UserProvider::class)));
$container->set(Authorization::class, fn() => new Authorization([
    'admin' => ['admin.area', 'user.manage'],
    'user' => ['user.area'],
    'party' => ['party.area'],
    'guest' => [],
]));

// Middleware
$container->set(SessionMiddleware::class, fn() => new SessionMiddleware());
$container->set(ErrorMiddleware::class, fn() => new ErrorMiddleware());
$container->set(AuthenticationMiddleware::class, fn($c) => new AuthenticationMiddleware($c->get(Authentication::class)));
$container->set(AuthorizationMiddleware::class, fn($c) => new AuthorizationMiddleware(
    $c->get(Authorization::class),
    [
        '/admin' => 'admin.area',
        '/user' => 'user.area',
        '/party' => 'party.area'
    ]
));

return $container;
