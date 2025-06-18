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

    public function authenticate(ServerRequestInterface $request): UserInterface
    {
        if (!isset($_SESSION['user_id'])) {
            return new AnonymousUser();
        }

        $user = $this->provider->getById($_SESSION['user_id']);

        return $user ?? new AnonymousUser();
    }
}