<?php

namespace Framework\AccessControl;

use Psr\Http\Message\ServerRequestInterface;

/**
 * A service that can authenticate a user based on information in the current request.
 */
interface AuthenticationInterface
{

    /**
     * Authenticate the given request.
     * @param ServerRequestInterface $request
     * @return UserInterface The user that was found in the request, or an anonymous user if no user was found.
     */
    public function authenticate(ServerRequestInterface $request): UserInterface;
}
