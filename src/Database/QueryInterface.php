<?php

namespace Framework\Database;

/**
 * A query that can be used to select objects in a repository.
 */
interface QueryInterface
{
    /**
     * Get the filter to apply to the result set.
     * @return array<string, mixed> An associative array of column names and corresponding values to filter on.
     */
    public function getFilter(): array;

    /**
     * Get the ordering criteria to apply to the result set.
     * @return array<string> An array of column names, optionally suffixed with ASC or DESC as in SQL.
     */
    public function getOrder(): array;

    /**
     * Get the offset to apply to the result set.
     * @return int The offset, or 0 if there is none.
     */
    public function getOffset(): int;

    /**
     * Get the maximum number of results in tbe result set.
     * @return int The limit, or 0 if there is none.
     */
    public function getLimit(): int;
}