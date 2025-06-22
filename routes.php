<?php

use App\Controller\AdminController;
use App\Controller\FormController;
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
    $router->addRoute('GET', '/form', $container->get(FormController::class));
    $router->addRoute('POST', '/form', $container->get(FormController::class));
    $router->addRoute('GET', '/admin', $container->get(AdminController::class));
    $router->addRoute('POST', '/admin/add-statement', $container->get(AdminController::class));
    $router->addRoute('POST', '/admin/delete-statement', $container->get(AdminController::class));
    $router->addRoute('POST', '/admin/add-user', $container->get(AdminController::class));
    $router->addRoute('POST', '/admin/delete-user', $container->get(AdminController::class));};