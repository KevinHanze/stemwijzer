<?php

namespace App\Controller;

use App\Mapper\UserMapper;
use App\Model\User;
use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Controller that handles user registration.
 */
class RegisterController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private UserMapper $userMapper
    ) {}

    /**
     * Shows the registration form or processes a submitted registration.
     */
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

                // Redirect to login after successful registration
                return new Response(
                    302,
                    ['Location' => ['/login']],
                    Stream::fromString('')
                );
            }
        }

        // Show registration form
        $html = $this->view->render('register.html');
        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
