<?php

namespace Framework\AccessControl;

class UserProvider implements UserProviderInterface
{
    private array $usersById = [];

    private array $usersByUsername = [];

    public function __construct(array $users = [])
    {
        foreach ($users as $id => $user) {
            $this->usersById[$id] = $user;
            $this->usersByUsername[$user->getUsername()] = $user;
        }
    }

    public function get(string $username): UserInterface
    {
        return $this->usersByUsername[$username] ?? new AnonymousUser();
    }

    public function getById(int|string $id): ?UserInterface
    {
        return $this->usersById[$id] ?? null;
    }
}