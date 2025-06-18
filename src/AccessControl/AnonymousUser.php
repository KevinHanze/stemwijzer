<?php

namespace Framework\AccessControl;

class AnonymousUser implements UserInterface
{
    public function getUsername(): string
    {
        return 'anonymous';
    }

    public function getPasswordHash(): string
    {
        return '';
    }

    public function getRoles(): array
    {
        return [];
    }

    public function isAnonymous(): bool
    {
        return true;
    }
}