<?php

namespace Framework\AccessControl;

use App\Mapper\UserMapper;
use Framework\Database\NotFoundException;
use Framework\Database\Query;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserMapper $mapper) {}

    public function get(string $username): UserInterface
    {
        $list = $this->mapper->select(new Query(['username' => $username]));
        return $list[0] ?? new AnonymousUser();
    }

    public function getById(int|string $id): ?UserInterface
    {
        try {
            return $this->mapper->get((int)$id);
        } catch (NotFoundException) {
            return null;
        }
    }
}