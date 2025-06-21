<?php

namespace App\Controller;

use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeController implements RequestHandlerInterface {

    public function __construct(private TemplateEngine $view) {}

    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');

        $html = $this->view->render('home.html',
            name: $user?->getUsername() ?? 'guest',
            loggedIn: !$user?->isAnonymous()
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
