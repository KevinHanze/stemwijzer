<?php

namespace Framework\AccessControl;

class User implements UserInterface
{

    private string $username;
    private string $passwordHash;
    private array $roles;
    private bool $anonymous;

    public function __construct(
        string $username,
        string $passwordHash = '',
        array $roles = ['user'],
        bool $anonymous = false
    ) {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
        $this->anonymous = $anonymous;
    }
    public function getUsername(): string {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }
}
