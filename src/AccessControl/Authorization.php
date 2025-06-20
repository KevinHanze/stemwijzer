<?php

namespace Framework\AccessControl;

class Authorization implements AuthorizationInterface {

    private array $rolePermissions;

    public function __construct(array $rolePermissions = [])
    {
        $this->rolePermissions = $rolePermissions;
    }

    public function isGranted(UserInterface $user, string $permission, ...$parameters): bool
    {
        foreach ($user->getRoles() as $role) {
            $allowed = $this->rolePermissions[$role] ?? [];

            if (\in_array($permission, $allowed, true)) {
                return true;
            }
        }
        return false;
    }

    public function denyUnlessGranted(UserInterface $user, string $permission, ...$parameters): void
    {
        if (!$this->isGranted($user,$permission, ...$parameters)) {
            throw new AccessDeniedException();
        }
    }
}
