<?php

namespace App\Model;

use Framework\AccessControl\UserInterface;

class User implements UserInterface {
    public function __construct(
        public ?int $id,
        public string $username,
        public string $passwordHash,
        public array $roles
    ) {}

    public function getUsername(): string
    {
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
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
