<?php

namespace Framework\AccessControl;

class User implements UserInterface
{

    private int $id;
    private string $username;
    private string $passwordHash;
    private array $roles;
    private bool $anonymous;

    public function __construct(
        int $id,
        string $username,
        string $passwordHash = '',
        array $roles = ['user'],
        bool $anonymous = false
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
        $this->anonymous = $anonymous;
    }

    public function getId(): int
    {
        return $this->id;
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
