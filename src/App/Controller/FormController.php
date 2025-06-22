<?php

namespace App\Controller;

use App\Mapper\StatementMapper;
use Framework\Database\Query;
use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private StatementMapper $statements
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        $statements = $this->statements->select(new Query([]));
        $user = $request->getAttribute('user');
        $options = [
            'Helemaal oneens',
            'Oneens',
            'Neutraal',
            'Eens',
            'Helemaal eens'
        ];

        $html = $this->view->render('form.html',
            statements: $statements,
            loggedIn: !$user?->isAnonymous(),
            options: $options
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
