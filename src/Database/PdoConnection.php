<?php

namespace Framework\Database;

use PDO;
use PDOException;

class PdoConnection implements ConnectionInterface {
    private PDO $pdo;

    /**
     * Initializes the database connection using the provided DSN.
     *
     * This constructor creates a new PDO instance and configures it with:
     * - Error mode set to throw exceptions on failure (PDO::ERRMODE_EXCEPTION)
     * - Default fetch mode set to associative arrays (PDO::FETCH_ASSOC)
     *
     * @param string $dsn The Data Source Name used to connect to the database.
     *
     * @throws PDOException If the connection fails.
     */
    public function __construct(string $dsn) {
        $this->pdo = new PDO($dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function query(string $query, ...$params): array
    {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $query, ...$params): int
    {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function getLastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }
}

