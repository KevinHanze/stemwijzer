<?php

namespace App\Controller;

use App\Mapper\StemwijzerMapper;
use Framework\Database\Query;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Templating\TemplateEngine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResultController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private StemwijzerMapper $stemwijzerMapper,
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');

        $stemwijzers = $this->stemwijzerMapper->select(new Query([
            'user_id' => $user->getId()
        ]));

        $html = $this->view->render('results.html',
            stemwijzers: $stemwijzers,
            name: $user->getUsername()
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
