<?php

namespace Framework\Database;

/**
 * A service that maps domain objects to database records.
 * @template T
 */
interface DataMapperInterface
{
    /**
     * Select a single object by its primary key.
     * @param int $id
     * @return T
     * @throws NotFoundException if the object was not found.
     */
    public function get(int $id): object;

    /**
     * Select a number of objects with a query.
     * @param QueryInterface $query
     * @return array<T>
     */
    public function select(QueryInterface $query): array;

    /**
     * Insert a new object in the database.
     * @param T $object
     */
    public function insert($object): void;

    /**
     * Update an existing object in the database.
     * @param T $object
     */
    public function update($object): void;

    /**
     * Delete an object from the database.
     * @param T $object
     */
    public function delete($object): void;
}