<?php

namespace Framework\AccessControl;

use Psr\Http\Message\ServerRequestInterface;

class Authentication implements AuthenticationInterface
{
    private UserProviderInterface $provider;

    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    // Used for middleware
    public function authenticate(ServerRequestInterface $request): UserInterface
    {
        if (!isset($_SESSION['user_id'])) {
            return new AnonymousUser();
        }

        $user = $this->provider->getById($_SESSION['user_id']);

        return $user ?? new AnonymousUser();
    }

    // Used for login
    public function authenticateCredentials(string $username, string $password): UserInterface
    {
        $user = $this->provider->get($username);

        if ($user->isAnonymous()) {
            return $user;
        }

        if (password_verify($password, $user->getPasswordHash())) {
            return $user;
        }

        return new AnonymousUser();
    }
}