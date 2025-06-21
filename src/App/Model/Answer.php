<?php

namespace App\Model;

class Answer {
    public function __construct(
        public ?int $id,
        public int $statementId,
        public int $partyId,
        public string $answer
    ) {}
}
