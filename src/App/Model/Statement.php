<?php

namespace App\Model;

class Statement {
    public function __construct(
        public ?int $id,
        public string $statement,
    ) {}
}
