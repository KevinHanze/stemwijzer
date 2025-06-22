<?php

use App\Controller\AdminController;
use App\Controller\FormController;
use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\PartyController;
use App\Controller\RegisterController;
use App\Controller\ResultController;
use App\Mapper\AnswerMapper;
use App\Mapper\StatementMapper;
use App\Mapper\StemwijzerMapper;
use App\Mapper\UserAnswerMapper;
use App\Mapper\UserMapper;
use App\Repository\AnswerRepository;
use App\Repository\UserAnswerRepository;
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
use App\Controller\LogoutController;


$container = new Container();

// Core services
$container->set(PdoConnection::class, fn() => new PdoConnection('sqlite:' . __DIR__ . '/database.db'), true);
$container->set(Router::class, fn() => new Router(), true);
$container->set(TemplateEngine::class, fn() => new TemplateEngine(__DIR__ . '/templates'), true);

// Add mappers and repositories
$container->set(UserMapper::class, fn($c) => new UserMapper(
    $c->get(PdoConnection::class)
));
$container->set(StatementMapper::class, fn($c) => new StatementMapper(
    $c->get(PdoConnection::class)
));
$container->set(AnswerMapper::class, fn($c) => new AnswerMapper(
    $c->get(PdoConnection::class)
));
$container->set(UserAnswerMapper::class, fn($c) => new UserAnswerMapper(
    $c->get(PdoConnection::class)
));
$container->set(StemwijzerMapper::class, fn($c) => new StemwijzerMapper(
    $c->get(PdoConnection::class)
));
$container->set(AnswerRepository::class, fn($c) => new AnswerRepository(
    $c->get(AnswerMapper::class)
));
$container->set(UserAnswerRepository::class, fn($c) => new UserAnswerRepository(
    $c->get(UserAnswerMapper::class)
));


// Access control
$container->set(UserProvider::class, fn($c) => new UserProvider($c->get(UserMapper::class)));
$container->set(Authentication::class, fn($c) => new Authentication($c->get(UserProvider::class)));
$container->set(Authorization::class, fn() => new Authorization([
    'admin' => ['admin.area'],
    'user' => ['user.area', 'form.access'],
    'party' => ['party.area'],
    'guest' => ['form.access'],
]));

// Middleware
$container->set(SessionMiddleware::class, fn() => new SessionMiddleware());
$container->set(ErrorMiddleware::class, fn() => new ErrorMiddleware());
$container->set(AuthenticationMiddleware::class, fn($c) => new AuthenticationMiddleware($c->get(Authentication::class)));
$container->set(AuthorizationMiddleware::class, fn($c) => new AuthorizationMiddleware(
    $c->get(Authorization::class),
    [
        '/admin' => 'admin.area',
        '/results' => 'user.area',
        '/party' => 'party.area',
        '/form' => 'form.access'
    ]
));

// Add controllers
$container->set(HomeController::class, fn($c) => new HomeController(
    $c->get(TemplateEngine::class)
));
$container->set(RegisterController::class, fn($c) => new RegisterController(
    $c->get(TemplateEngine::class),
    $c->get(UserMapper::class)
));
$container->set(LoginController::class, fn($c) => new LoginController(
    $c->get(TemplateEngine::class),
    $c->get(Authentication::class)
));
$container->set(LogoutController::class, fn() => new LogoutController());
$container->set(FormController::class, fn($c) => new FormController(
    $c->get(TemplateEngine::class),
    $c->get(StatementMapper::class),
    $c->get(StemwijzerMapper::class),
    $c->get(UserAnswerRepository::class),
    $c->get(AnswerMapper::class),
    $c->get(UserMapper::class)
));
$container->set(AdminController::class, fn($c) => new AdminController(
    $c->get(TemplateEngine::class),
    $c->get(StatementMapper::class),
    $c->get(UserMapper::class)
));
$container->set(PartyController::class, fn($c) => new PartyController(
    $c->get(TemplateEngine::class),
    $c->get(StatementMapper::class),
    $c->get(AnswerRepository::class)
));
$container->set(ResultController::class, fn($c) => new ResultController(
    $c->get(TemplateEngine::class),
    $c->get(StemwijzerMapper::class)
));

// return initialized container
return $container;
