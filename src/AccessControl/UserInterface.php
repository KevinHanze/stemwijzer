<?php

namespace Framework\AccessControl;

/**
 * A user, either a logged-in user or an anonymous user.
 */
interface UserInterface
{
    /**
     * Get the user's username.
     * @return string
     */
    public function getUsername(): string;

    /**
     * Get the user's hashed password.
     * @return string
     */
    public function getPasswordHash(): string;

    /**
     * Get the user's roles.
     * @return array<string>
     */
    public function getRoles(): array;

    /**
     * Get whether the user is an anonymous user, as opposed to a logged in user.
     * @return bool
     */
    public function isAnonymous(): bool;
}