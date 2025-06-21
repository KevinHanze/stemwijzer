<?php

use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\RegisterController;
use Framework\DependencyInjection\Container;
use Framework\Routing\Router;
use Framework\Templating\TemplateEngine;

return function (Router $router, TemplateEngine $view, Container $container) {
    $router->addRoute('GET', '/', $container->get(HomeController::class));
    $router->addRoute('GET', '/register', $container->get(RegisterController::class));
    $router->addRoute('POST', '/register', $container->get(RegisterController::class));
    $router->addRoute('GET', '/login', $container->get(LoginController::class));
    $router->addRoute('POST', '/login', $container->get(LoginController::class));
    $router->addRoute('GET', '/logout', $container->get(LogoutController::class));
};