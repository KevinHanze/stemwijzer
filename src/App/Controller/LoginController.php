<?php

namespace App\Controller;

use Framework\AccessControl\Authentication;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Templating\TemplateEngine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Controller that handles login logic and session initialization.
 */
class LoginController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private Authentication $auth
    ) {}

    /**
     * Renders the login page (GET) or processes login credentials (POST).
     */
    public function handle(ServerRequestInterface $request): Response
    {
        if ($request->getMethod() === 'GET') {
            $html = $this->view->render('login.html');
            return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
        }

        // Handle login submission
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->auth->authenticateCredentials($username, $password);

        if (!$user->isAnonymous()) {

            // Set session user ID and redirect to homepage
            $_SESSION['user_id'] = $user->getId();
            return new Response(302, ['Location' => ['/']]);
        }

        // Login failed — re-render
        $html = $this->view->render('login.html', [
            'name' => $user->getUsername(),
            'loggedIn' => true
        ]);

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
