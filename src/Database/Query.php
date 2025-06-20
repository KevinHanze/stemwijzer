<?php

namespace Framework\Database;
class Query implements QueryInterface {

    private array $filter;
    private array $order;
    private int $offset;
    private int $limit;

    public function __construct(array $filter = [], array $order = [], int $offset = 0, int $limit = 100) {
        $this->filter = $filter;
        $this->order = $order;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
