<?php

namespace App\Repository;

use App\Mapper\UserMapper;
use App\Model\User;
use Framework\Database\Query;

class UserRepository
{
    public function __construct(private UserMapper $mapper) {}

    public function findById(string $id): ?User
    {
        $users = $this->mapper->select(new Query(['id' => $id]));
        return $users[0] ?? null;
    }
    public function findByUsername(string $username): ?User
    {
        $users = $this->mapper->select(new Query(['username' => $username]));
        return $users[0] ?? null;
    }

    public function findByRole(string $role): array
    {
        return $this->mapper->select(new Query(['roles' => $role]));
    }
}
