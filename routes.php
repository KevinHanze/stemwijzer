<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Routing\Router;
use Framework\Templating\TemplateEngine;

return function (Router $router, TemplateEngine $view) {

    $router->addRoute('GET', '/', new class($view) implements RequestHandlerInterface {
        private TemplateEngine $view;

        public function __construct(TemplateEngine $view)
        {
            $this->view = $view;
        }

        public function handle(ServerRequestInterface $request): Response
        {
            $html = $this->view->render('home.html', name: 'World');
            return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
        }
    });
};