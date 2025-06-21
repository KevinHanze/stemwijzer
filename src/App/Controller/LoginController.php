<?php

namespace App\Controller;

use Framework\AccessControl\Authentication;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Templating\TemplateEngine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private Authentication $auth
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        if ($request->getMethod() === 'GET') {
            $html = $this->view->render('login.html');
            return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
        }
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->auth->authenticateCredentials($username, $password);

        if (!$user->isAnonymous()) {
            $_SESSION['user_id'] = $user->getUsername();
            return new Response(302, ['Location' => ['/']]);
        }

        $html = $this->view->render('login.html', [
            'error' => 'Ongeldige gebruikersnaam of wachtwoord.',
        ]);

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
