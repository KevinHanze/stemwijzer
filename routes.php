<?php

use App\Controller\HomeController;
use Framework\Routing\Router;
use Framework\Templating\TemplateEngine;

return function (Router $router, TemplateEngine $view) {
    $router->addRoute('GET', '/', new HomeController($view));
};