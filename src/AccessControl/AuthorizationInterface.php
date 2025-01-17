<?php

namespace Framework\AccessControl;

/**
 * A service that can authorize access to permissions to certain users.
 */
interface AuthorizationInterface
{
    /**
     * Checks whether the user has the given permission, optionally with parameters.
     * @param UserInterface $user
     * @param string $permission
     * @param mixed ...$parameters
     * @return bool
     */
    public function isGranted(UserInterface $user, string $permission, mixed ...$parameters): bool;

    /**
     * Throws an exception unless the user has the given permission, optionally with parameters.
     * @param UserInterface $user
     * @param string $permission
     * @param mixed ...$parameters
     * @throws AccessDeniedException
     */
    public function denyUnlessGranted(UserInterface $user, string $permission, mixed ...$parameters): void;
}