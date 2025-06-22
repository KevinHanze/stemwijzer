<?php

namespace App\Controller;

use App\Mapper\StatementMapper;
use App\Mapper\UserMapper;
use App\Model\Statement;
use App\Model\User;
use Framework\Database\Query;
use Framework\Http\Response;
use Framework\Http\Stream;
use Framework\Templating\TemplateEngine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Controller that handles basic admin actions:
 * - Manage statements and users (add/delete)
 * - Render the admin dashboard
 */
class AdminController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private StatementMapper $statements,
        private UserMapper $users
    ) {}

    /**
     * Handles GET and POST requests to the /admin dashboard.
     *
     * POST routes:
     * - /admin/add-statement
     * - /admin/delete-statement
     * - /admin/add-user
     * - /admin/delete-user
     */
    public function handle(ServerRequestInterface $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $path = $request->getUri()->getPath();

            // Match route to form handler
            return match ($path) {
                '/admin/add-statement' => $this->addStatement($data),
                '/admin/delete-statement' => $this->deleteStatement($data),
                '/admin/add-user' => $this->addUser($data),
                '/admin/delete-user' => $this->deleteUser($data),
                default => new Response(404, ['Content-Type' => ['text/plain']], Stream::fromString("Not found"))
            };
        }

        // Render admin dashboard
        $statements = $this->statements->select(new Query([]));
        $users = $this->users->select(new Query([]));
        $user = $request->getAttribute('user');
        $loggedIn = !$user?->isAnonymous();
        $isAdmin = in_array('admin', $user?->getRoles() ?? []);

        $html = $this->view->render('admin.html',
            statements: $statements,
            users: $users,
            loggedIn: $loggedIn,
            isAdmin: $isAdmin
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }

    private function addStatement(array $data): Response
    {
        $text = trim($data['text'] ?? '');
        if ($text !== '') {
            $this->statements->insert(new Statement(null, $text, 'admin'));
        }

        return new Response(302, ['Location' => ['/admin']]);
    }

    private function deleteStatement(array $data): Response
    {
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            $statement = $this->statements->get($id);
            $this->statements->delete($statement);
        }

        return new Response(302, ['Location' => ['/admin']]);
    }

    private function addUser(array $data): Response
    {
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');
        $role = trim($data['role'] ?? 'user');

        if ($username && $password) {
            $user = new User(null, $username, password_hash($password, PASSWORD_DEFAULT), [$role]);
            $this->users->insert($user);
        }

        return new Response(302, ['Location' => ['/admin']]);
    }

    private function deleteUser(array $data): Response
    {
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            $user = $this->users->get($id);
            $this->users->delete($user);
        }

        return new Response(302, ['Location' => ['/admin']]);
    }
}
