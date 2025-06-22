<?php

namespace App\Mapper;

use App\Model\UserAnswer;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\NotFoundException;
use Framework\Database\QueryInterface;

class UserAnswerMapper implements DataMapperInterface
{
    public function __construct(private ConnectionInterface $db) {}

    public function get(int $id): UserAnswer
    {
        $rows = $this->db->query('SELECT * FROM user_answers WHERE id = ?', $id);

        if (count($rows) === 0) {
            throw new NotFoundException("UserAnswer with ID $id not found");
        }

        return $this->hydrate($rows[0]);
    }

    public function select(QueryInterface $query): array
    {
        $sql = 'SELECT * FROM user_answers WHERE 1=1';
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
            'INSERT INTO user_answers (statement_id, user_id, answer, stemwijzer_id) VALUES (?, ?, ?, ?)',
            $object->statementId,
            $object->userId,
            $object->answer,
            $object->stemwijzerId
        );

        $object->id = $this->db->getLastInsertId();
    }

    public function update($object): void
    {
        $this->db->execute(
            'UPDATE user_answers SET statement_id = ?, user_id = ?, answer = ?, stemwijzer_id = ? WHERE id = ?',
            $object->statementId,
            $object->userId,
            $object->answer,
            $object->stemwijzerId,
            $object->id
        );
    }

    public function delete($object): void
    {
        $this->db->execute('DELETE FROM user_answers WHERE id = ?', $object->id);
    }

    private function hydrate(array $row): UserAnswer
    {
        return new UserAnswer(
            id: (int)$row['id'],
            statementId: (int)$row['statement_id'],
            userId: (int)$row['user_id'],
            answer: $row['answer'],
            stemwijzerId: (int)$row['stemwijzer_id']
        );
    }
}
