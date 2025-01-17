<?php

namespace Framework\Database;

/**
 * A service that allows access to a collection of domain objects.
 * @template T
 */
interface RepositoryInterface
{
    /**
     * Get a single object by its primary key value.
     * @param int $id Primary key value.
     * @return T
     * @throws NotFoundException if the object was not found.
     */
    public function get(int $id): object;

    /**
     * Store a new or existing object in the repository.
     * @param T $object
     */
    public function save(object $object): void;

    /**
     * Remove an object from the repository.
     * @param T $object
     */
    public function remove($object): void;

    /**
     * Find a number of objects in the repository based on a query.
     * @param QueryInterface $query
     * @return array<T>
     */
    public function find(QueryInterface $query): array;

    /**
     * Find a single object in the repository based on a query.
     * @param QueryInterface $query
     * @return T
     * @throws NotFoundException if no matching object was found.
     */
    public function findOne(QueryInterface $query): object;
}