<?php

namespace Framework\Database;

use PDO;

class PdoConnection implements ConnectionInterface {
    private PDO $pdo;

    public function __construct(string $dsn) {
        $this->pdo = new PDO($dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function query(string $query, ...$params): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $query, ...$params): int
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function getLastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }
}

