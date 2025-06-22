<?php

namespace App\Mapper;

use App\Model\Answer;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\NotFoundException;
use Framework\Database\QueryInterface;

class AnswerMapper implements DataMapperInterface
{
    public function __construct(private ConnectionInterface $db) {}

    public function get(int $id): Answer
    {
        $rows = $this->db->query('SELECT * FROM answers WHERE id = ?', $id);

        if (count($rows) === 0) {
            throw new NotFoundException("Answer with ID $id not found");
        }

        return $this->hydrate($rows[0]);
    }

    public function select(QueryInterface $query): array
    {
        $sql = 'SELECT * FROM answers WHERE 1=1';
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
            'INSERT INTO answers (statementId, userId, answer, reason) VALUES (?, ?, ?, ?)',
            $object->statementId,
            $object->userId,
            $object->answer,
            $object->reason
        );

        $object->id = $this->db->getLastInsertId();
    }

    public function update($object): void
    {
        $this->db->execute(
            'UPDATE answers SET answer = ?, reason = ? WHERE statementId = ? AND userId = ?',
            $object->answer,
            $object->reason,
            $object->statementId,
            $object->userId
        );
    }

    public function delete($object): void
    {
        $this->db->execute(
            'DELETE FROM answers WHERE id = ?',
            $object->id
        );
    }

    private function hydrate(array $row): Answer
    {
        return new Answer(
            id: (int)$row['id'],
            statementId: (int)$row['statementId'],
            userId: (int)$row['userId'],
            answer: $row['answer'],
            reason: $row['reason'] ?? ''
        );
    }
}
