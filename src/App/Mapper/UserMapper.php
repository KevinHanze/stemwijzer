<?php

namespace App\Mapper;

use App\Model\User;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\NotFoundException;
use Framework\Database\QueryInterface;

class UserMapper implements DataMapperInterface {

    public function __construct(private ConnectionInterface $db)
    {}

    public function get(int $id): User
    {
        $rows = $this->db->query('SELECT * FROM users WHERE id = ?', $id);

        if (count($rows) === 0) {
            throw new NotFoundException("User with ID $id not found");
        }

        return $this->hydrate($rows[0]);
    }

    public function select(QueryInterface $query): array
    {
        $sql = 'SELECT * FROM users WHERE 1=1';
        $params = [];

        foreach ($query->getFilter() as $column => $value) {
            $sql .= " AND $column = ?";
            $params[] = $value;
        }

        $rows = $this->db->query($sql, ...$params);
        return array_map([$this, 'hydrate'], $rows);
    }

    public function insert($object): void
    {
        $this->db->execute(
            'INSERT INTO users (username, password, roles) VALUES (?, ?, ?)',
            $object->username,
            $object->passwordHash,
            json_encode($object->roles)
        );

        $object->id = $this->db->getLastInsertId();
    }

    public function update($object): void
    {
        $this->db->execute(
            'UPDATE users SET username = ?, password = ?, roles = ? WHERE id = ?',
            $object->username,
            $object->password,
            json_encode($object->roles),
            $object->id
        );
    }

    public function delete($object): void
    {
        $this->db->execute('DELETE FROM users WHERE id = ?', $object->id);
    }

    private function hydrate(array $row): User
    {
        return new User(
            id: (int)$row['id'],
            username: $row['username'],
            passwordHash: $row['password'],
            roles: json_decode($row['roles'], true) ?? []
        );
    }
}
