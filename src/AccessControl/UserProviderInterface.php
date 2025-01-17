<?php

namespace Framework\AccessControl;

/**
 * A service which can find users by their username.
 */
interface UserProviderInterface
{
    /**
     * Get a user by their username.
     * @param string $username
     * @return UserInterface The user matching the username, or an anonymous user if none could be found.
     */
    public function get(string $username): UserInterface;
}