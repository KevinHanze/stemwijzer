<?php


namespace App\Mapper;

use App\Model\Statement;
use App\Model\User;
use Framework\Database\ConnectionInterface;
use Framework\Database\DataMapperInterface;
use Framework\Database\NotFoundException;
use Framework\Database\QueryInterface;

class StatementMapper implements DataMapperInterface
{

    public function __construct(private ConnectionInterface $db)
    {
    }

    public function get(int $id): Statement
    {
        $rows = $this->db->query('SELECT * FROM statements WHERE id = ?', $id);

        if (count($rows) === 0) {
            throw new NotFoundException("User with ID $id not found");
        }

        return $this->hydrate($rows[0]);
    }

    public function select(QueryInterface $query): array
    {
        $sql = 'SELECT * FROM statements WHERE 1=1';
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
            'INSERT INTO statements (text, author) VALUES (?, ?)',
            $object->text,
            $object->author
        );

        $object->id = $this->db->getLastInsertId();
    }

    public function update($object): void
    {
        $this->db->execute(
            'UPDATE statements SET text = ?, author = ? WHERE id = ?',
            $object->text,
            $object->author,
            $object->id
        );
    }

    public function delete($object): void
    {
        $this->db->execute('DELETE FROM statements WHERE id = ?', $object->id);
    }

    private function hydrate(array $row): Statement
    {
        return new Statement(
            id: (int)$row['id'],
            text: $row['text'],
            author: $row['author']
        );
    }
}
