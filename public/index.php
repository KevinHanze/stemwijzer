<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\AccessControl\User;
use Framework\AccessControl\UserProvider;
use Framework\AccessControl\Authentication;
use Framework\AccessControl\Authorization;
use Framework\Http\Middleware\SessionMiddleware;
use Framework\Http\Middleware\AuthenticationMiddleware;
use Framework\Http\Middleware\AuthorizationMiddleware;
use Framework\Http\RequestFactory;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Kernel\Kernel;
use Framework\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

// create users
$users = [
1 => new User('kevin', password_hash('test', PASSWORD_DEFAULT), ['user']),
2 => new User('admin', password_hash('admin', PASSWORD_DEFAULT), ['admin']),
];

$userProvider = new UserProvider($users);
$authentication = new Authentication($userProvider);

// Define roles
$authorization = new Authorization([
    'admin' => ['admin.area', 'user.manage'],
    'user' => ['user.area'],
    'guest' => [],
]);

$router = new Router();

// set middleware pipeline
$kernel = new Kernel($router, [
    new SessionMiddleware(),
    new AuthenticationMiddleware($authentication),
    new AuthorizationMiddleware($authorization, [
        '/admin' => 'admin.area',
        '/user'  => 'user.area',
    ])
]);

// Add test routes
$router->addRoute('GET', '/login-as-admin', new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): Response
    {
        $_SESSION['user_id'] = 2; // Admin
        return new Response(200, ['Content-Type' => ['text/plain']], new Stream("Logged in as admin"));
    }
});

$router->addRoute('GET', '/login-as-user', new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): Response
    {
        $_SESSION['user_id'] = 1; // Normal user
        return new Response(200, ['Content-Type' => ['text/plain']], new Stream("Logged in as user"));
    }
});

// Protected route
$router->addRoute('GET', '/admin', new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');
        return new Response(200, ['Content-Type' => ['text/plain']], new Stream("Welkom, " . $user->getUsername()));
    }
});

$request = RequestFactory::fromGlobals();

try {
    $response = $kernel->handle($request);
} catch (\Framework\AccessControl\AccessDeniedException $e) {
    $response = new Response(403, ['Content-Type' => ['text/plain']], Stream::fromString("Access denied: " . $e->getMessage()));
} catch (\Throwable $e) {
    $response = new Response(500, ['Content-Type' => ['text/plain']], Stream::fromString("Server error: " . $e->getMessage()));
}

http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
