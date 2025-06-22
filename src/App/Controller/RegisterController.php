<?php

namespace App\Controller;

use App\Mapper\UserMapper;
use App\Model\User;
use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegisterController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private UserMapper $userMapper
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $username = trim($data['username'] ?? '');
            $password = trim($data['password'] ?? '');

            if ($username && $password) {
                $user = new User(
                    id: null,
                    username: $username,
                    passwordHash: password_hash($password, PASSWORD_DEFAULT),
                    roles: ['user']
                );

                $this->userMapper->insert($user);

                return new Response(
                    302,
                    ['Location' => ['/login']],
                    Stream::fromString('')
                );
            }
        }

        $html = $this->view->render('register.html');
        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
