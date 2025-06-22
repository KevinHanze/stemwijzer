<?php

namespace App\Mapper;

use App\Model\Stemwijzer;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\NotFoundException;
use Framework\Database\QueryInterface;

class StemwijzerMapper implements DataMapperInterface
{
    public function __construct(private ConnectionInterface $db) {}

    public function insert($stemwijzer): void
    {
        $this->db->execute(
            'INSERT INTO stemwijzers (user_id, submitted_at, matchedParties) VALUES (?, CURRENT_TIMESTAMP, ?)',
            $stemwijzer->userId,
            $stemwijzer->matchedParties
        );
        $stemwijzer->id = $this->db->getLastInsertId();
    }

    public function get(int $id): Stemwijzer
    {
        $rows = $this->db->query('SELECT * FROM stemwijzers WHERE id = ?', $id);
        if (count($rows) === 0) {
            throw new NotFoundException("Stemwijzer not found");
        }
        return $this->hydrate($rows[0]);
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
        return array_map([$this, 'hydrate'], $rows);
    }

    public function update($object): void
    {
        $this->db->execute(
            'UPDATE stemwijzers SET user_id = ?, matchedParties = ? WHERE id = ?',
            $object->userId,
            json_encode($object->matchedParties),
            $object->id
        );
    }

    public function delete($object): void
    {
        $this->db->execute('DELETE FROM stemwijzers WHERE id = ?', $object->id);
    }

    private function hydrate(array $row): Stemwijzer
    {
        return new Stemwijzer(
            id: (int)$row['id'],
            userId: (int)$row['user_id'],
            submittedAt: $row['submitted_at'],
            matchedParties: $row['matchedParties'] ?? ''
        );
    }
}
