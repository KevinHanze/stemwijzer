<?php

namespace App\Mapper;

use App\Model\Stemwijzer;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\QueryInterface;

class StemwijzerMapper implements DataMapperInterface
{
    public function __construct(private ConnectionInterface $db) {}

    public function insert($object): void
    {
        $this->db->execute(
            'INSERT INTO stemwijzers (user_id, submitted_at) VALUES (?, CURRENT_TIMESTAMP)',
            $object->userId
        );
        $object->id = $this->db->getLastInsertId();
    }

    public function get(int $id): Stemwijzer
    {
        $rows = $this->db->query('SELECT * FROM stemwijzers WHERE id = ?', $id);

        if (count($rows) === 0) {
            throw new \RuntimeException("Stemwijzer met ID $id niet gevonden.");
        }

        $row = $rows[0];
        return new Stemwijzer(
            id: (int)$row['id'],
            userId: (int)$row['user_id'],
            submittedAt: $row['submitted_at']
        );
    }

    public function select(QueryInterface $query): array
    {
        $sql = 'SELECT * FROM stemwijzers WHERE 1=1';
        $params = [];

        foreach ($query->getFilter() as $column => $value) {
            $sql .= " AND $column = ?";
            $params[] = $value;
        }

        $rows = $this->db->query($sql, ...$params);

        return array_map(function ($row) {
            return new Stemwijzer(
                id: (int)$row['id'],
                userId: (int)$row['user_id'],
                submittedAt: $row['submitted_at']
            );
        }, $rows);
    }

    public function update($object): void
    {
        // TODO: Implement update() method.
    }

    public function delete($object): void
    {
        // TODO: Implement delete() method.
    }
}

