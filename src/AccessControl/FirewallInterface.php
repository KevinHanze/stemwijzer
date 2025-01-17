<?php

namespace Framework\AccessControl;

use Psr\Http\Message\RequestInterface;

/**
 * A service that can block unauthorized users from accessing parts of the website.
 */
interface FirewallInterface
{
    /**
     * Checks whether the user is allowed to perform the request.
     * @param RequestInterface $request
     * @param UserInterface $user
     * @return bool
     */
    public function accepts(RequestInterface $request, UserInterface $user): bool;
}